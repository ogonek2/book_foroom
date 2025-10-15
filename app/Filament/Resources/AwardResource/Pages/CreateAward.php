<?php

namespace App\Filament\Resources\AwardResource\Pages;

use App\Filament\Resources\AwardResource;
use App\Helpers\CDNUploader;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAward extends CreateRecord
{
    protected static string $resource = AwardResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;
        
        // Обрабатываем загрузку изображения на CDN
        if ($record->image) {
            $this->processImageUpload($record->image);
        }
    }

    private function processImageUpload($image)
    {
        \Log::info('Processing award image upload in afterCreate', ['image' => $image]);
        
        if (is_string($image)) {
            $fullPath = storage_path('app/public/' . $image);
            
            if (file_exists($fullPath)) {
                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $fullPath,
                    basename($fullPath),
                    mime_content_type($fullPath),
                    null,
                    true
                );
                
                // Загружаем на CDN
                $cdnUrl = CDNUploader::uploadFile($uploadedFile, 'awards');
                
                if ($cdnUrl) {
                    // Обновляем запись с URL CDN
                    $this->record->update(['image' => $cdnUrl]);
                    
                    // Удаляем временный файл
                    unlink($fullPath);
                    \Log::info('Award image uploaded to CDN', ['cdn_url' => $cdnUrl]);
                }
            }
        }
    }
}
