<?php

namespace App\Filament\Resources\AwardResource\Pages;

use App\Filament\Resources\AwardResource;
use App\Helpers\CDNUploader;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAward extends EditRecord
{
    protected static string $resource = AwardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        
        // Обрабатываем загрузку изображения на CDN
        if ($record->image && str_contains($record->image, 'awards-temp')) {
            $this->processImageUpload($record->image);
        }
    }

    private function processImageUpload($image)
    {
        \Log::info('Processing award image upload in afterSave', ['image' => $image]);
        
        if (is_string($image) && str_contains($image, 'awards-temp')) {
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
                    \Log::info('Award image uploaded to CDN and record updated', ['cdn_url' => $cdnUrl]);
                }
            }
        }
    }
}
