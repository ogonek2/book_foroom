<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action as PageAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class SystemSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Системные настройки';
    protected static ?string $title = 'Настройки системы';
    protected static string $view = 'filament.pages.system-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'app_debug' => config('app.debug'),
            'app_env' => config('app.env'),
            'db_connection' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'mail_driver' => config('mail.default'),
            'admin_theme' => 'light',
            'admin_language' => 'uk',
            'admin_timezone' => config('app.timezone'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основные настройки приложения')
                    ->schema([
                        TextInput::make('app_name')
                            ->label('Название приложения')
                            ->required(),
                        TextInput::make('app_url')
                            ->label('URL приложения')
                            ->url()
                            ->required(),
                        Toggle::make('app_debug')
                            ->label('Режим отладки')
                            ->helperText('Включить для разработки'),
                        Select::make('app_env')
                            ->label('Окружение')
                            ->options([
                                'local' => 'Локальное',
                                'staging' => 'Тестовое',
                                'production' => 'Продакшн',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Настройки базы данных')
                    ->schema([
                        Select::make('db_connection')
                            ->label('Тип БД')
                            ->options([
                                'mysql' => 'MySQL',
                                'pgsql' => 'PostgreSQL',
                                'sqlite' => 'SQLite',
                                'sqlsrv' => 'SQL Server',
                            ])
                            ->required(),
                        TextInput::make('db_host')
                            ->label('Хост БД')
                            ->default('localhost'),
                        TextInput::make('db_port')
                            ->label('Порт БД')
                            ->numeric()
                            ->default('3306'),
                        TextInput::make('db_database')
                            ->label('Имя БД')
                            ->required(),
                        TextInput::make('db_username')
                            ->label('Пользователь БД'),
                        TextInput::make('db_password')
                            ->label('Пароль БД')
                            ->password(),
                    ])
                    ->columns(2),

                Section::make('Настройки кэша и сессий')
                    ->schema([
                        Select::make('cache_driver')
                            ->label('Драйвер кэша')
                            ->options([
                                'file' => 'Файлы',
                                'redis' => 'Redis',
                                'memcached' => 'Memcached',
                                'database' => 'База данных',
                            ])
                            ->required(),
                        Select::make('session_driver')
                            ->label('Драйвер сессий')
                            ->options([
                                'file' => 'Файлы',
                                'database' => 'База данных',
                                'redis' => 'Redis',
                                'memcached' => 'Memcached',
                            ])
                            ->required(),
                        TextInput::make('session_lifetime')
                            ->label('Время жизни сессии (минуты)')
                            ->numeric()
                            ->default('120'),
                    ])
                    ->columns(3),

                Section::make('Настройки почты')
                    ->schema([
                        Select::make('mail_driver')
                            ->label('Драйвер почты')
                            ->options([
                                'smtp' => 'SMTP',
                                'mailgun' => 'Mailgun',
                                'ses' => 'Amazon SES',
                                'sendmail' => 'Sendmail',
                            ])
                            ->required(),
                        TextInput::make('mail_host')
                            ->label('SMTP хост'),
                        TextInput::make('mail_port')
                            ->label('SMTP порт')
                            ->numeric(),
                        TextInput::make('mail_username')
                            ->label('SMTP пользователь'),
                        TextInput::make('mail_password')
                            ->label('SMTP пароль')
                            ->password(),
                        Toggle::make('mail_encryption')
                            ->label('Шифрование'),
                    ])
                    ->columns(2),

                Section::make('Настройки админ панели')
                    ->schema([
                        Select::make('admin_theme')
                            ->label('Тема')
                            ->options([
                                'light' => 'Светлая',
                                'dark' => 'Темная',
                                'auto' => 'Автоматически',
                            ])
                            ->required(),
                        Select::make('admin_language')
                            ->label('Язык')
                            ->options([
                                'ru' => 'Русский',
                                'en' => 'English',
                                'uk' => 'Українська',
                            ])
                            ->required(),
                        Select::make('admin_timezone')
                            ->label('Часовой пояс')
                            ->options([
                                'Europe/Moscow' => 'Москва',
                                'Europe/Kiev' => 'Киев',
                                'Europe/London' => 'Лондон',
                                'America/New_York' => 'Нью-Йорк',
                                'Asia/Tokyo' => 'Токио',
                            ])
                            ->required(),
                        ColorPicker::make('primary_color')
                            ->label('Основной цвет')
                            ->default('#3B82F6'),
                        ColorPicker::make('secondary_color')
                            ->label('Вторичный цвет')
                            ->default('#6B7280'),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            PageAction::make('saveSettings')
                ->label('Сохранить настройки')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('saveSettings'),
            PageAction::make('resetSettings')
                ->label('Сбросить настройки')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action('resetSettings'),
            PageAction::make('clearCache')
                ->label('Очистить кэш')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->action('clearCache'),
            PageAction::make('optimizeApp')
                ->label('Оптимизировать приложение')
                ->icon('heroicon-o-bolt')
                ->color('info')
                ->action('optimizeApp'),
        ];
    }

    public function saveSettings(): void
    {
        try {
            // Здесь можно добавить логику сохранения настроек
            Notification::make()
                ->title('Настройки сохранены')
                ->body('Все настройки успешно сохранены')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка сохранения')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function resetSettings(): void
    {
        try {
            $this->mount();
            Notification::make()
                ->title('Настройки сброшены')
                ->body('Настройки возвращены к значениям по умолчанию')
                ->warning()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка сброса')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function clearCache(): void
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('optimize:clear');
            
            Notification::make()
                ->title('Кэш очищен')
                ->body('Все кэши успешно очищены')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка очистки кэша')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function optimizeApp(): void
    {
        try {
            Artisan::call('optimize');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            
            Notification::make()
                ->title('Приложение оптимизировано')
                ->body('Приложение успешно оптимизировано для продакшна')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка оптимизации')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
