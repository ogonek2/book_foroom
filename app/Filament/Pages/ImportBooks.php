<?php

namespace App\Filament\Pages;

use App\Helpers\FileHelper;
use App\Imports\SimpleBooksImport;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class ImportBooks extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    
    protected static ?string $navigationLabel = 'Импорт книг';
    
    protected static ?string $title = 'Импорт книг из Excel';
    
    protected static string $view = 'filament.pages.import-books';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Импорт книг')
                    ->description('Загрузите Excel файл с данными о книгах')
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
                            ->helperText('• Категории и авторы уже существуют в системе
• Файл соответствует шаблону
• Все обязательные поля заполнены'),
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
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\BooksTemplateExport(), 
                        'books-template.xlsx'
                    );
                }),
        ];
    }

    public function import(): void
    {
        $data = $this->form->getState();
        
        if (!isset($data['file']) || empty($data['file'])) {
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
            $filePath = FileHelper::getFilePath($file);
            
            if (!$filePath || !file_exists($filePath)) {
                Notification::make()
                    ->title('Ошибка импорта')
                    ->body('Файл не найден. Попробуйте загрузить файл заново.')
                    ->danger()
                    ->send();
                return;
            }

            $import = new SimpleBooksImport();
            Excel::import($import, $filePath);
            
            $importedCount = $import->getRowCount();
            $errors = $import->getErrors();
            $warnings = $import->getWarnings();
            
            $message = "Успешно импортировано {$importedCount} книг";
            
            if (!empty($warnings)) {
                $message .= ". Предупреждений: " . count($warnings);
            }
            
            if (!empty($errors)) {
                $message .= ". Ошибок: " . count($errors);
                Notification::make()
                    ->title('Импорт завершен с ошибками')
                    ->body($message)
                    ->warning()
                    ->send();
            } else {
                Notification::make()
                    ->title('Импорт завершен')
                    ->body($message)
                    ->success()
                    ->send();
            }
            
            $this->form->fill();
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка импорта')
                ->body('Произошла ошибка при импорте: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
