<?php

namespace App\Filament\Pages;

use App\Helpers\FileHelper;
use App\Imports\SimpleAuthorsImport;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class ImportAuthors extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    
    protected static ?string $navigationLabel = 'Импорт авторов';
    
    protected static ?string $title = 'Импорт авторов из Excel';
    
    protected static string $view = 'filament.pages.import-authors';

    public ?array $data = [];
    public array $previewRows = [];
    public array $previewErrors = [];
    public array $previewWarnings = [];
    public bool $hasPreview = false;
    public bool $isImporting = false;
    public string $importProgress = '';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Импорт авторов')
                    ->description('Загрузите Excel файл с данными об авторах')
                    ->schema([
                        FileUpload::make('file')
                            ->label('Excel файл')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->required()
                            ->helperText('Поддерживаются файлы .xlsx и .xls. Скачайте шаблон для правильного формата.'),
                        
                        TextInput::make('info')
                            ->label('Информация')
                            ->disabled()
                            ->default('Перед импортом убедитесь, что:')
                            ->helperText('• Файл соответствует шаблону
• Все обязательные поля заполнены
• Изображения будут автоматически загружены на CDN'),
                    ])
            ])
            ->statePath('data');
    }

    protected function getActions(): array
    {
        return [
            Action::make('download_template')
                ->label('Скачать шаблон')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(function () {
                    // TODO: Create AuthorsTemplateExport if needed
                    Notification::make()
                        ->title('Шаблон')
                        ->body('Шаблон будет создан позже')
                        ->info()
                        ->send();
                }),
        ];
    }

    public function import(): void
    {
        $this->resetPreview();
        $this->isImporting = true;
        $this->importProgress = 'Начинаем импорт...';

        $data = $this->form->getState();
        
        if (!isset($data['file']) || empty($data['file'])) {
            $this->isImporting = false;
            $this->importProgress = '';
            Notification::make()
                ->title('Ошибка')
                ->body('Пожалуйста, выберите файл для импорта')
                ->danger()
                ->send();
            return;
        }

        try {
            $file = $data['file'];
            
            // Получаем путь к файлу используя helper
            $this->importProgress = 'Обработка файла...';
            
            $filePath = FileHelper::getFilePath($file);
            
            if (!$filePath || !file_exists($filePath)) {
                $this->isImporting = false;
                $this->importProgress = '';
                Notification::make()
                    ->title('Ошибка импорта')
                    ->body('Файл не найден. Попробуйте загрузить файл заново.')
                    ->danger()
                    ->send();
                return;
            }

            $this->importProgress = 'Импорт данных...';
            
            $import = new SimpleAuthorsImport();
            Excel::import($import, $filePath);
            
            $importedCount = $import->getRowCount();
            $errors = $import->getErrors();
            $warnings = $import->getWarnings();
            
            $this->isImporting = false;
            $this->importProgress = '';
            
            $message = "Успешно импортировано {$importedCount} авторов";
            
            if (!empty($warnings)) {
                $message .= ". Предупреждений: " . count($warnings);
            }
            
            if (!empty($errors)) {
                $message .= ". Ошибок: " . count($errors);
                Notification::make()
                    ->title('Импорт завершен с ошибками')
                    ->body($message)
                    ->warning()
                    ->persistent()
                    ->send();
            } else {
                Notification::make()
                    ->title('Импорт завершен')
                    ->body($message)
                    ->success()
                    ->persistent()
                    ->send();
            }
            
            $this->form->fill();
            
        } catch (\Exception $e) {
            $this->isImporting = false;
            $this->importProgress = '';
            Notification::make()
                ->title('Ошибка импорта')
                ->body('Произошла ошибка при импорте: ' . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    public function preview(): void
    {
        $this->resetPreview();
        $this->isImporting = true;
        $this->importProgress = 'Формирование предпросмотра...';

        $data = $this->form->getState();

        if (!isset($data['file']) || empty($data['file'])) {
            $this->isImporting = false;
            $this->importProgress = '';
            Notification::make()
                ->title('Предпросмотр недоступен')
                ->body('Пожалуйста, выберите файл для предпросмотра.')
                ->warning()
                ->send();
            return;
        }

        $file = $data['file'];
        $filePath = FileHelper::getFilePath($file);

        if (!$filePath || !file_exists($filePath)) {
            $this->isImporting = false;
            $this->importProgress = '';
            Notification::make()
                ->title('Ошибка предпросмотра')
                ->body('Файл не найден. Попробуйте загрузить файл заново.')
                ->danger()
                ->send();
            return;
        }

        try {
            $this->importProgress = 'Анализ данных...';
            
            $import = new SimpleAuthorsImport(true);
            Excel::import($import, $filePath);

            $this->previewRows = $import->getPreviewRows();
            $this->previewErrors = $import->getErrors();
            $this->previewWarnings = $import->getWarnings();
            $this->hasPreview = true;
            $this->isImporting = false;
            $this->importProgress = '';

            $message = 'Предпросмотр сформирован.';
            if (!empty($this->previewErrors)) {
                $message .= ' Ошибок: ' . count($this->previewErrors) . '.';
            }
            if (!empty($this->previewWarnings)) {
                $message .= ' Предупреждений: ' . count($this->previewWarnings) . '.';
            }

            Notification::make()
                ->title('Предпросмотр готов')
                ->body($message)
                ->info()
                ->send();
        } catch (\Exception $e) {
            $this->isImporting = false;
            $this->importProgress = '';
            Notification::make()
                ->title('Ошибка предпросмотра')
                ->body('Не удалось обработать файл: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function resetPreview(): void
    {
        $this->previewRows = [];
        $this->previewErrors = [];
        $this->previewWarnings = [];
        $this->hasPreview = false;
    }
}
