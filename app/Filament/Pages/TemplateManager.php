<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action as PageAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TemplateManager extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Управление шаблонами';
    protected static ?string $title = 'Менеджер шаблонов';
    protected static string $view = 'filament.pages.template-manager';

    public ?array $data = [];
    public $templates = [];
    public $selectedTemplate = '';
    public $templateContent = '';

    public function mount(): void
    {
        $this->loadTemplates();
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Управление шаблонами')
                    ->schema([
                        Select::make('selectedTemplate')
                            ->label('Выберите шаблон')
                            ->options($this->templates)
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                if ($state) {
                                    $this->loadTemplateContent($state);
                                }
                            }),
                    ])
                    ->columns(1),

                Section::make('Редактирование шаблона')
                    ->schema([
                        TextInput::make('templateName')
                            ->label('Название шаблона')
                            ->required(),
                        Textarea::make('templateContent')
                            ->label('Содержимое шаблона')
                            ->rows(20)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn () => !empty($this->selectedTemplate))
                    ->columns(1),

                Section::make('Настройки темы')
                    ->schema([
                        ColorPicker::make('primaryColor')
                            ->label('Основной цвет')
                            ->default('#3B82F6'),
                        ColorPicker::make('secondaryColor')
                            ->label('Вторичный цвет')
                            ->default('#6B7280'),
                        ColorPicker::make('accentColor')
                            ->label('Акцентный цвет')
                            ->default('#10B981'),
                        Toggle::make('darkMode')
                            ->label('Темная тема')
                            ->default(false),
                        TextInput::make('fontFamily')
                            ->label('Семейство шрифтов')
                            ->default('Inter'),
                        TextInput::make('fontSize')
                            ->label('Размер шрифта')
                            ->default('14px'),
                    ])
                    ->columns(3),

                Section::make('Загрузка файлов')
                    ->schema([
                        FileUpload::make('templateFiles')
                            ->label('Файлы шаблонов')
                            ->multiple()
                            ->acceptedFileTypes(['blade.php', 'css', 'js', 'html'])
                            ->directory('templates')
                            ->visibility('private'),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            PageAction::make('saveTemplate')
                ->label('Сохранить шаблон')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action('saveTemplate'),
            PageAction::make('createTemplate')
                ->label('Создать новый шаблон')
                ->icon('heroicon-o-plus')
                ->action('createTemplate'),
            PageAction::make('deleteTemplate')
                ->label('Удалить шаблон')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->action('deleteTemplate'),
            PageAction::make('exportTemplate')
                ->label('Экспортировать шаблон')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('exportTemplate'),
            PageAction::make('importTemplate')
                ->label('Импортировать шаблон')
                ->icon('heroicon-o-arrow-up-tray')
                ->action('importTemplate'),
        ];
    }

    public function loadTemplates(): void
    {
        try {
            $templatePath = resource_path('views');
            $this->templates = [];
            
            if (File::exists($templatePath)) {
                $files = File::allFiles($templatePath);
                
                foreach ($files as $file) {
                    if ($file->getExtension() === 'php') {
                        $relativePath = str_replace($templatePath . '/', '', $file->getPathname());
                        $this->templates[$relativePath] = $relativePath;
                    }
                }
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка загрузки шаблонов')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function loadTemplateContent(string $templatePath): void
    {
        try {
            $fullPath = resource_path('views/' . $templatePath);
            if (File::exists($fullPath)) {
                $this->templateContent = File::get($fullPath);
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка загрузки содержимого')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function saveTemplate(): void
    {
        try {
            $templatePath = resource_path('views/' . $this->selectedTemplate);
            File::put($templatePath, $this->templateContent);
            
            Notification::make()
                ->title('Шаблон сохранен')
                ->body('Изменения успешно сохранены')
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

    public function createTemplate(): void
    {
        try {
            $templateName = $this->data['templateName'] ?? 'new-template.blade.php';
            $templatePath = resource_path('views/' . $templateName);
            
            if (!File::exists($templatePath)) {
                File::put($templatePath, '{{-- Новый шаблон --}}');
                $this->loadTemplates();
                $this->selectedTemplate = $templateName;
                $this->loadTemplateContent($templateName);
                
                Notification::make()
                    ->title('Шаблон создан')
                    ->body('Новый шаблон успешно создан')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Ошибка')
                    ->body('Шаблон с таким именем уже существует')
                    ->warning()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка создания')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteTemplate(): void
    {
        try {
            if ($this->selectedTemplate) {
                $templatePath = resource_path('views/' . $this->selectedTemplate);
                File::delete($templatePath);
                $this->loadTemplates();
                $this->selectedTemplate = '';
                $this->templateContent = '';
                
                Notification::make()
                    ->title('Шаблон удален')
                    ->body('Шаблон успешно удален')
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка удаления')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function exportTemplate()
    {
        try {
            if ($this->selectedTemplate) {
                $templatePath = resource_path('views/' . $this->selectedTemplate);
                $content = File::get($templatePath);
                
                return response()->streamDownload(function () use ($content) {
                    echo $content;
                }, $this->selectedTemplate);
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка экспорта')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function importTemplate(): void
    {
        // Логика импорта шаблона
        Notification::make()
            ->title('Импорт шаблонов')
            ->body('Функция импорта будет добавлена в следующей версии')
            ->info()
            ->send();
    }
}
