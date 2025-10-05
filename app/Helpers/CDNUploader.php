<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CDNUploader
{
    /**
     * Upload file to BunnyCDN
     *
     * @param UploadedFile $file
     * @param string $pathPrefix - prefix for file path (e.g., 'avatars/', 'uploads/')
     * @return string - full CDN URL of uploaded file
     * @throws \Exception
     */
    public static function uploadToBunnyCDN($file, $folder = 'avatars')
    {
        try {
            // Проверяем наличие необходимых переменных окружения
            $storageName = env('BUNNY_STORAGE_NAME');
            $storagePassword = env('BUNNY_STORAGE_PASSWORD');
            $cdnUrl = env('BUNNY_CDN_URL');
            
            if (!$storageName || !$storagePassword || !$cdnUrl) {
                \Log::error('Отсутствуют настройки BunnyCDN в .env файле', [
                    'storage_name' => $storageName ? 'Set' : 'Missing',
                    'storage_password' => $storagePassword ? 'Set' : 'Missing',
                    'cdn_url' => $cdnUrl ?: 'Missing'
                ]);
                return null;
            }

            $extension = $file->getClientOriginalExtension();
            $fileName = Str::random(20) . '.' . $extension;
            $destinationPath = $folder . '/' . $fileName;

            $fileContents = file_get_contents($file->getRealPath());

            $url = "https://storage.bunnycdn.com/" . $storageName . "/$destinationPath";

            \Log::info('Попытка загрузки на BunnyCDN', [
                'url' => $url,
                'folder' => $folder,
                'fileName' => $fileName,
                'fileSize' => strlen($fileContents)
            ]);

            $response = Http::withOptions([
                'verify' => config('bunnycdn.ssl_verify', false), // SSL проверка (по умолчанию отключена для разработки)
                'timeout' => config('bunnycdn.timeout', 30),
            ])->withHeaders([
                'AccessKey' => $storagePassword,
                'Content-Type' => 'application/octet-stream',
            ])->withBody($fileContents, 'application/octet-stream')
                ->put($url);

            \Log::info('Ответ от BunnyCDN', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $publicUrl = rtrim($cdnUrl, '/') . '/' . $destinationPath;
                \Log::info('Файл успешно загружен на BunnyCDN', ['publicUrl' => $publicUrl]);
                return $publicUrl;
            } else {
                \Log::error('Ошибка загрузки на BunnyCDN', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers()
                ]);
                return null;
            }

        } catch (\Exception $e) {
            \Log::error('Исключение при загрузке на BunnyCDN: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public static function deleteFromBunnyCDN($url)
    {
        $parsed = parse_url($url);
        $path = ltrim($parsed['path'], '/');
        
        $storageName = config('bunnycdn.storage.name');
        $storagePassword = config('bunnycdn.storage.password');

        return Http::withOptions([
            'verify' => config('bunnycdn.ssl_verify', false), // SSL проверка (по умолчанию отключена для разработки)
            'timeout' => config('bunnycdn.timeout', 30),
        ])->withHeaders([
            'AccessKey' => $storagePassword,
            'Content-Type' => 'application/octet-stream',
        ])->delete("https://storage.bunnycdn.com/" . $storageName . "/" . $path)->successful();
    }

    /**
     * Альтернативный метод загрузки файлов локально
     */
    public static function uploadLocally($file, $folder = 'avatars')
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::random(20) . '.' . $extension;
            $destinationPath = "storage/app/public/$folder";
            
            // Создаем директорию если её нет
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $fullPath = $destinationPath . '/' . $fileName;
            
            if (move_uploaded_file($file->getRealPath(), $fullPath)) {
                $publicUrl = asset("storage/$folder/$fileName");
                \Log::info('Файл успешно загружен локально', ['publicUrl' => $publicUrl]);
                return $publicUrl;
            }
            
            return null;
            
        } catch (\Exception $e) {
            \Log::error('Ошибка локальной загрузки: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Универсальный метод загрузки файлов
     */
    public static function uploadFile($file, $folder = 'avatars')
    {
        // Сначала пробуем загрузить на CDN
        $cdnUrl = self::uploadToBunnyCDN($file, $folder);
        
        if ($cdnUrl) {
            return $cdnUrl;
        }
        
        // Если CDN недоступен, загружаем локально
        \Log::warning('CDN недоступен, используем локальную загрузку');
        return self::uploadLocally($file, $folder);
    }
}
