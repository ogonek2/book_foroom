<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use App\Helpers\CDNUploader;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Создать новую книгу';
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/admin11') => 'Главная',
            $this->getResource()::getUrl('index') => 'Книги',
            'Создать',
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        \Log::info('CreateBook::mutateFormDataBeforeCreate called', ['data' => $data]);
        
        // Обработка загрузки обложки книги
        if (isset($data['cover_image']) && is_array($data['cover_image'])) {
            \Log::info('Processing cover_image upload in create', ['cover_image' => $data['cover_image']]);
            $filePath = $data['cover_image'][0] ?? null;
            
            if ($filePath) {
                // Получаем полный путь к файлу
                $fullPath = storage_path('app/public/' . $filePath);
                
                if (file_exists($fullPath)) {
                    // Создаем UploadedFile объект
                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $fullPath,
                        basename($filePath),
                        mime_content_type($fullPath),
                        null,
                        true
                    );
                    
                    // Загружаем на CDN
                    $cdnUrl = CDNUploader::uploadFile($uploadedFile, 'book-covers');
                    
                    if ($cdnUrl) {
                        $data['cover_image'] = $cdnUrl;
                        
                        // Удаляем временный файл
                        unlink($fullPath);
                    }
                }
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        \Log::info('CreateBook::afterCreate called');
        
        // Проверяем временную папку на наличие файлов
        $tempDir = storage_path('app/public/book-covers-temp');
        
        if (is_dir($tempDir)) {
            $files = scandir($tempDir);
            $files = array_filter($files, function($file) {
                return $file !== '.' && $file !== '..';
            });
            
            \Log::info('Found temp files in book-covers-temp', ['files' => $files]);
            
            foreach ($files as $file) {
                $filePath = $tempDir . '/' . $file;
                
                if (file_exists($filePath)) {
                    \Log::info('Processing temp file', ['file' => $file]);
                    
                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $filePath,
                        $file,
                        mime_content_type($filePath),
                        null,
                        true
                    );
                    
                    $cdnUrl = CDNUploader::uploadFile($uploadedFile, 'book-covers');
                    
                    if ($cdnUrl) {
                        $this->record->update(['cover_image' => $cdnUrl]);
                        unlink($filePath);
                        \Log::info('Cover image uploaded to CDN and record updated', ['cdn_url' => $cdnUrl]);
                    }
                }
            }
        }
    }

    private function processCoverImageUpload($coverImage)
    {
        \Log::info('Processing cover image upload in afterCreate', ['cover_image' => $coverImage]);
        
        $filePath = $coverImage[0] ?? null;
        
        if ($filePath) {
            $fullPath = storage_path('app/public/' . $filePath);
            
            if (file_exists($fullPath)) {
                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $fullPath,
                    basename($filePath),
                    mime_content_type($fullPath),
                    null,
                    true
                );
                
                $cdnUrl = CDNUploader::uploadFile($uploadedFile, 'book-covers');
                
                if ($cdnUrl) {
                    $this->record->update(['cover_image' => $cdnUrl]);
                    unlink($fullPath);
                    \Log::info('Cover image uploaded to CDN', ['cdn_url' => $cdnUrl]);
                }
            }
        }
    }
}
