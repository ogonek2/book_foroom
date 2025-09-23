<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action as PageAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class DatabaseManager extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $navigationLabel = 'Управление БД';
    protected static ?string $title = 'Менеджер базы данных';
    protected static string $view = 'filament.pages.database-manager';

    public ?array $data = [];
    public $tables = [];
    public $selectedTable = '';
    public $tableStructure = [];
    public $tableData = [];

    public function mount(): void
    {
        $this->loadTables();
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Управление таблицами')
                    ->schema([
                        Select::make('selectedTable')
                            ->label('Выберите таблицу')
                            ->options($this->tables)
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                if ($state) {
                                    $this->loadTableStructure($state);
                                    $this->loadTableData($state);
                                }
                            }),
                    ])
                    ->columns(1),

                Section::make('Структура таблицы')
                    ->schema([
                        Repeater::make('tableStructure')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextInput::make('field')
                                            ->label('Поле')
                                            ->disabled(),
                                        Select::make('type')
                                            ->label('Тип')
                                            ->options([
                                                'varchar' => 'VARCHAR',
                                                'text' => 'TEXT',
                                                'int' => 'INT',
                                                'bigint' => 'BIGINT',
                                                'decimal' => 'DECIMAL',
                                                'boolean' => 'BOOLEAN',
                                                'date' => 'DATE',
                                                'datetime' => 'DATETIME',
                                                'timestamp' => 'TIMESTAMP',
                                            ])
                                            ->disabled(),
                                        TextInput::make('length')
                                            ->label('Длина')
                                            ->disabled(),
                                        Select::make('nullable')
                                            ->label('Nullable')
                                            ->options([
                                                'YES' => 'Да',
                                                'NO' => 'Нет',
                                            ])
                                            ->disabled(),
                                    ]),
                            ])
                            ->disabled()
                            ->visible(fn () => !empty($this->tableStructure)),
                    ])
                    ->visible(fn () => !empty($this->tableStructure)),

                Section::make('Данные таблицы')
                    ->schema([
                        Repeater::make('tableData')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('id')
                                            ->label('ID')
                                            ->disabled(),
                                        TextInput::make('name')
                                            ->label('Название')
                                            ->disabled(),
                                        TextInput::make('created_at')
                                            ->label('Создано')
                                            ->disabled(),
                                    ]),
                            ])
                            ->disabled()
                            ->visible(fn () => !empty($this->tableData)),
                    ])
                    ->visible(fn () => !empty($this->tableData)),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            PageAction::make('refreshTables')
                ->label('Обновить список таблиц')
                ->icon('heroicon-o-arrow-path')
                ->action('refreshTables'),
            PageAction::make('runMigrations')
                ->label('Запустить миграции')
                ->icon('heroicon-o-play')
                ->color('success')
                ->action('runMigrations'),
            PageAction::make('rollbackMigrations')
                ->label('Откатить миграции')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->action('rollbackMigrations'),
            PageAction::make('clearCache')
                ->label('Очистить кэш')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->action('clearCache'),
        ];
    }

    public function loadTables(): void
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $this->tables = [];
            
            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                $this->tables[$tableName] = $tableName;
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка загрузки таблиц')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function loadTableStructure(string $tableName): void
    {
        try {
            $columns = DB::select("DESCRIBE `{$tableName}`");
            $this->tableStructure = [];
            
            foreach ($columns as $column) {
                $this->tableStructure[] = [
                    'field' => $column->Field,
                    'type' => $column->Type,
                    'length' => $this->extractLength($column->Type),
                    'nullable' => $column->Null,
                    'key' => $column->Key,
                    'default' => $column->Default,
                ];
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка загрузки структуры')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function loadTableData(string $tableName): void
    {
        try {
            $data = DB::table($tableName)->limit(10)->get();
            $this->tableData = $data->toArray();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка загрузки данных')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function extractLength(string $type): string
    {
        if (preg_match('/\((\d+)\)/', $type, $matches)) {
            return $matches[1];
        }
        return '';
    }

    public function refreshTables(): void
    {
        $this->loadTables();
        Notification::make()
            ->title('Список таблиц обновлен')
            ->success()
            ->send();
    }

    public function runMigrations(): void
    {
        try {
            Artisan::call('migrate');
            $this->loadTables();
            Notification::make()
                ->title('Миграции выполнены')
                ->body(Artisan::output())
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка выполнения миграций')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function rollbackMigrations(): void
    {
        try {
            Artisan::call('migrate:rollback');
            $this->loadTables();
            Notification::make()
                ->title('Миграции откачены')
                ->body(Artisan::output())
                ->warning()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка отката миграций')
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
}
