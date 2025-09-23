<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    /**
     * Получить путь к загруженному файлу
     */
    public static function getFilePath($file): ?string
    {
        if (is_array($file) && isset($file[0])) {
            // Если это массив файлов, берем первый
            return $file[0]->getRealPath();
        }
        
        if (is_string($file)) {
            // Если это строка, ищем файл в разных местах
            $possiblePaths = [
                storage_path('app/livewire-tmp/' . $file),
                storage_path('app/public/' . $file),
                public_path('storage/' . $file),
                storage_path('app/temp/' . $file),
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    return $path;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Сохранить файл во временное хранилище
     */
    public static function storeTempFile(UploadedFile $file): string
    {
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('temp', $filename);
        return storage_path('app/' . $path);
    }
    
    /**
     * Очистить временные файлы
     */
    public static function cleanTempFiles(): void
    {
        $tempDir = storage_path('app/temp');
        if (is_dir($tempDir)) {
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }
}
