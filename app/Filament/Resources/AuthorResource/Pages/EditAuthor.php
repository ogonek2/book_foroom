<?php

namespace App\Filament\Resources\AuthorResource\Pages;

use App\Filament\Resources\AuthorResource;
use App\Helpers\CDNUploader;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAuthor extends EditRecord
{
    protected static string $resource = AuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        \Log::info('EditAuthor::mutateFormDataBeforeSave called', ['data' => $data]);
        
        // Обработка загрузки фото автора
        if (isset($data['photo']) && is_array($data['photo'])) {
            \Log::info('Processing photo upload', ['photo' => $data['photo']]);
            $filePath = $data['photo'][0] ?? null;
            
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
                    $cdnUrl = CDNUploader::uploadFile($uploadedFile, 'authors');
                    
                    if ($cdnUrl) {
                        $data['photo'] = $cdnUrl;
                        
                        // Удаляем временный файл
                        unlink($fullPath);
                    }
                }
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        \Log::info('EditAuthor::afterSave called', [
            'record_id' => $this->record->id,
            'photo' => $this->record->photo
        ]);
        
        // Проверяем временную папку на наличие файлов
        $tempDir = storage_path('app/public/authors-temp');
        
        if (is_dir($tempDir)) {
            $files = scandir($tempDir);
            $files = array_filter($files, function($file) {
                return $file !== '.' && $file !== '..';
            });
            
            \Log::info('Found temp files in authors-temp', ['files' => $files]);
            
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
                    
                    $cdnUrl = CDNUploader::uploadFile($uploadedFile, 'authors');
                    
                    if ($cdnUrl) {
                        $this->record->update(['photo' => $cdnUrl]);
                        unlink($filePath);
                        \Log::info('Photo uploaded to CDN and record updated', ['cdn_url' => $cdnUrl]);
                    }
                }
            }
        }
    }

    private function processPhotoUpload($photo)
    {
        \Log::info('Processing photo upload in afterSave', ['photo' => $photo]);
        
        $filePath = $photo[0] ?? null;
        
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
                
                $cdnUrl = CDNUploader::uploadFile($uploadedFile, 'authors');
                
                if ($cdnUrl) {
                    $this->record->update(['photo' => $cdnUrl]);
                    unlink($fullPath);
                    \Log::info('Photo uploaded to CDN', ['cdn_url' => $cdnUrl]);
                }
            }
        }
    }
}
