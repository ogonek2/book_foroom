<?php

namespace App\Imports;

use App\Helpers\CDNUploader;
use App\Models\Author;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SimpleAuthorsImport implements ToModel, WithHeadingRow, SkipsOnError, WithChunkReading
{
    use Importable;

    private int $rowCount = 0;
    private array $errors = [];
    private array $warnings = [];
    private array $previewRows = [];
    private bool $previewMode;
    private int $currentRowNumber = 1;

    public function __construct(bool $previewMode = false)
    {
        $this->previewMode = $previewMode;
    }

    public function model(array $row)
    {
        $this->currentRowNumber++;

        if ($this->isRowEmpty($row)) {
            Log::info("Пропущена пустая строка {$this->currentRowNumber}");
            return null;
        }

        Log::info("Обрабатываем строку {$this->currentRowNumber}: " . ($row['first_name_ua'] ?? $row['first_name'] ?? 'Без имени'));

        try {
            return DB::transaction(function () use ($row) {
                $result = $this->processRowData($row, $this->currentRowNumber);

                if (!$result['valid']) {
                    $this->errors[] = "Строка {$result['row']}: " . implode('; ', $result['errors']);
                    return null;
                }

                if (!empty($result['warnings'])) {
                    foreach ($result['warnings'] as $warning) {
                        $this->warnings[] = "Строка {$result['row']}: {$warning}";
                    }
                }

                if ($this->previewMode) {
                    $this->previewRows[] = $result;
                    return null;
                }

                $attributes = $result['attributes'];
                $photoSource = $result['meta']['photo_source'] ?? null;
                $photo = $result['meta']['photo_url'] ?? null;

                /** @var \App\Models\Author|null $author */
                $existingAuthorId = $result['existing_author_id'] ?? null;

                if ($existingAuthorId) {
                    $author = Author::find($existingAuthorId);

                    if ($author) {
                        // Если фото не указано в импорте, сохраняем существующее
                        if (empty($photoSource) && $author->photo) {
                            unset($attributes['photo']);
                        }
                        
                        // Обновляем только те поля, которые указаны в импорте
                        // (атрибуты уже отфильтрованы в processRowData, но на всякий случай еще раз)
                        $attributesToUpdate = array_filter($attributes, function ($value) {
                            return $value !== null && $value !== '';
                        });
                        
                        // Убеждаемся, что slug всегда обновляется
                        if (isset($attributes['slug'])) {
                            $attributesToUpdate['slug'] = $attributes['slug'];
                        }
                        
                        $author->fill($attributesToUpdate);
                        $author->save();
                    } else {
                        $author = Author::create($attributes);
                    }
                } else {
                    $author = Author::create($attributes);
                }

                $this->rowCount++;

                $action = $author->wasRecentlyCreated ? 'Создан' : ($existingAuthorId ? 'Обновлен' : 'Создан');
                Log::info("{$action} автор: {$author->first_name} {$author->last_name} (ID: {$author->id})");
                return $author;
            });
        } catch (\Exception $e) {
            Log::error("Ошибка импорта строки {$this->currentRowNumber}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            $this->errors[] = "Строка {$this->currentRowNumber}: " . $e->getMessage();
            return null;
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function onError(\Throwable $e): void
    {
        Log::error("Ошибка при импорте автора: " . $e->getMessage(), [
            'row' => $this->currentRowNumber,
            'trace' => $e->getTraceAsString(),
        ]);
        $this->errors[] = "Строка {$this->currentRowNumber}: " . $e->getMessage();
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function getPreviewRows(): array
    {
        return $this->previewRows;
    }

    private function isRowEmpty(array $row): bool
    {
        $requiredFields = ['first_name_ua', 'first_name', 'last_name_ua', 'last_name'];
        foreach ($requiredFields as $field) {
            if (!empty($row[$field])) {
                return false;
            }
        }
        return true;
    }

    private function processRowData(array $row, int $rowNumber): array
    {
        $errors = [];
        $warnings = [];

        // Ukrainian names
        $firstNameUa = trim($row['first_name_ua'] ?? '') ?: null;
        $middleNameUa = trim($row['middle_name_ua'] ?? '') ?: null;
        $lastNameUa = trim($row['last_name_ua'] ?? '') ?: null;

        // English names
        $firstNameEng = trim($row['first_name_eng'] ?? '') ?: null;
        $middleNameEng = trim($row['middle_name_eng'] ?? '') ?: null;
        $lastNameEng = trim($row['last_name_eng'] ?? '') ?: null;

        // Default names (use Ukrainian if available, otherwise English, otherwise fallback)
        $firstName = $firstNameUa ?: $firstNameEng ?: trim($row['first_name'] ?? '');
        $middleName = $middleNameUa ?: $middleNameEng ?: trim($row['middle_name'] ?? '') ?: null;
        $lastName = $lastNameUa ?: $lastNameEng ?: trim($row['last_name'] ?? '');

        if (empty($firstName) && empty($lastName)) {
            $errors[] = 'Имя или фамилия обязательны';
        }

        $pseudonym = trim($row['pseudonym'] ?? '') ?: null;
        $synonyms = $this->parseSynonyms($row['synonyms'] ?? '');

        // Generate slug from name or pseudonym
        $nameForSlug = $pseudonym ?: trim(($firstName ?? '') . ' ' . ($lastName ?? ''));
        $slug = !empty($row['slug']) ? Str::slug($row['slug']) : Str::slug($nameForSlug);
        $originalSlug = $slug;

        $existingAuthor = $this->findExistingAuthor($firstName, $lastName, $slug, $pseudonym);

        if ($this->previewMode) {
            if ($slug && Author::where('slug', $slug)
                    ->when($existingAuthor, fn ($query) => $query->where('id', '!=', $existingAuthor->id))
                    ->exists()) {
                $warnings[] = "Слаг '{$slug}' уже используется і буде змінений при імпорті";
            }
        } else {
            if ($existingAuthor) {
                if ($slug && $slug !== $existingAuthor->slug) {
                    $slugInUse = Author::where('slug', $slug)
                        ->where('id', '!=', $existingAuthor->id)
                        ->exists();

                    if ($slugInUse) {
                        $warnings[] = "Слаг '{$slug}' уже используется іншим автором, залишено '{$existingAuthor->slug}'";
                        $slug = $existingAuthor->slug;
                    } else {
                        $warnings[] = "Слаг обновлен на '{$slug}' для существующего автора";
                    }
                } else {
                    $slug = $existingAuthor->slug;
                }
            } else {
                $counter = 1;
                while ($slug && Author::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                if ($slug !== $originalSlug) {
                    $warnings[] = "Слаг изменен на '{$slug}' для уникальности";
                }
            }
        }

        $biography = !empty($row['annotation'])
            ? trim($row['annotation'])
            : (!empty($row['biography']) ? trim($row['biography']) : null);

        $birthDate = $this->parseDate($row['birth_date'] ?? '');
        $deathDate = $this->parseDate($row['death_date'] ?? '');

        $nationality = trim($row['nationality'] ?? '') ?: null;

        $photoSource = !empty($row['img'])
            ? trim($row['img'])
            : (!empty($row['photo']) ? trim($row['photo']) : null);

        $photo = $this->processPhoto($photoSource, $warnings, $errors);

        $webPage = !empty($row['web_page'])
            ? trim($row['web_page'])
            : (!empty($row['website']) ? trim($row['website']) : null);

        $awards = trim($row['awards'] ?? '') ?: null;
        $isFeatured = $this->parseBoolean($row['is_featured'] ?? '');

        // Формируем атрибуты, включая только те поля, которые указаны в импорте
        $attributes = [];
        
        // Основные имена (обязательно, если указаны)
        if ($firstName !== null && $firstName !== '') {
            $attributes['first_name'] = $firstName;
        }
        if ($middleName !== null && $middleName !== '') {
            $attributes['middle_name'] = $middleName;
        }
        if ($lastName !== null && $lastName !== '') {
            $attributes['last_name'] = $lastName;
        }
        
        // Украинские имена
        if ($firstNameUa !== null && $firstNameUa !== '') {
            $attributes['first_name_ua'] = $firstNameUa;
        }
        if ($middleNameUa !== null && $middleNameUa !== '') {
            $attributes['middle_name_ua'] = $middleNameUa;
        }
        if ($lastNameUa !== null && $lastNameUa !== '') {
            $attributes['last_name_ua'] = $lastNameUa;
        }
        
        // Английские имена
        if ($firstNameEng !== null && $firstNameEng !== '') {
            $attributes['first_name_eng'] = $firstNameEng;
        }
        if ($middleNameEng !== null && $middleNameEng !== '') {
            $attributes['middle_name_eng'] = $middleNameEng;
        }
        if ($lastNameEng !== null && $lastNameEng !== '') {
            $attributes['last_name_eng'] = $lastNameEng;
        }
        
        // Дополнительные поля
        if ($pseudonym !== null && $pseudonym !== '') {
            $attributes['pseudonym'] = $pseudonym;
        }
        if ($synonyms !== null) {
            $attributes['synonyms'] = $synonyms;
        }
        
        // Slug всегда обновляем
        $attributes['slug'] = $slug;
        
        // Биография и даты
        if ($biography !== null && $biography !== '') {
            $attributes['biography'] = $biography;
        }
        if ($birthDate !== null) {
            $attributes['birth_date'] = $birthDate;
        }
        if ($deathDate !== null) {
            $attributes['death_date'] = $deathDate;
        }
        if ($nationality !== null && $nationality !== '') {
            $attributes['nationality'] = $nationality;
        }
        
        // Фото - только если указано в импорте
        if ($photo !== null) {
            $attributes['photo'] = $photo;
        }
        
        // Веб-сайт
        if ($webPage !== null && $webPage !== '') {
            $attributes['website'] = $webPage;
            $attributes['web_page'] = $webPage;
        }
        
        // Награды
        if ($awards !== null && $awards !== '') {
            $attributes['awards'] = $awards;
        }
        
        // Рекомендуемый автор
        $attributes['is_featured'] = $isFeatured;

        return [
            'valid' => empty($errors),
            'row' => $rowNumber,
            'attributes' => $attributes,
            'errors' => $errors,
            'warnings' => $warnings,
            'existing_author_id' => $existingAuthor?->id,
            'meta' => [
                'slug' => $slug,
                'action' => $existingAuthor ? 'update' : 'create',
                'existing_author_id' => $existingAuthor?->id,
                'existing_author_name' => $existingAuthor ? ($existingAuthor->first_name . ' ' . $existingAuthor->last_name) : null,
                'photo_source' => $photoSource,
                'photo_url' => $photo,
            ],
        ];
    }

    private function findExistingAuthor(?string $firstName, ?string $lastName, string $slug, ?string $pseudonym): ?Author
    {
        // Сначала проверяем по slug (самый надежный способ)
        if ($slug) {
            $author = Author::where('slug', $slug)->first();
            if ($author) {
                return $author;
            }
        }

        // Проверяем по псевдониму
        if ($pseudonym) {
            $author = Author::where('pseudonym', $pseudonym)->first();
            if ($author) {
                return $author;
            }
        }

        // Проверяем по комбинации имени и фамилии (основные поля)
        if ($firstName && $lastName) {
            $author = Author::where(function ($q) use ($firstName, $lastName) {
                $q->where(function ($subQ) use ($firstName, $lastName) {
                    $subQ->where('first_name', $firstName)
                          ->where('last_name', $lastName);
                })
                // Также проверяем украинские имена
                ->orWhere(function ($subQ) use ($firstName, $lastName) {
                    $subQ->where('first_name_ua', $firstName)
                          ->where('last_name_ua', $lastName);
                })
                // И английские имена
                ->orWhere(function ($subQ) use ($firstName, $lastName) {
                    $subQ->where('first_name_eng', $firstName)
                          ->where('last_name_eng', $lastName);
                });
            })->first();
            
            if ($author) {
                return $author;
            }
        }

        // Проверяем только по имени (если фамилия не указана)
        if ($firstName && !$lastName) {
            $author = Author::where(function ($q) use ($firstName) {
                $q->where('first_name', $firstName)
                  ->orWhere('first_name_ua', $firstName)
                  ->orWhere('first_name_eng', $firstName);
            })->first();
            
            if ($author) {
                return $author;
            }
        }

        // Проверяем только по фамилии (если имя не указано)
        if ($lastName && !$firstName) {
            $author = Author::where(function ($q) use ($lastName) {
                $q->where('last_name', $lastName)
                  ->orWhere('last_name_ua', $lastName)
                  ->orWhere('last_name_eng', $lastName);
            })->first();
            
            if ($author) {
                return $author;
            }
        }

        return null;
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Try to parse various date formats
        $value = trim((string) $value);

        // If it's already a date string in Y-m-d format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        // Try to extract year from various formats
        if (preg_match('/\b(19|20)\d{2}\b/', $value, $matches)) {
            $year = (int) $matches[0];
            if ($year >= 1800 && $year <= (date('Y') + 10)) {
                // Return as Y-m-d with default month and day
                return $year . '-01-01';
            }
        }

        // Try to parse with Carbon
        try {
            $date = \Carbon\Carbon::parse($value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseBoolean($value): bool
    {
        if (empty($value)) {
            return false;
        }

        $value = strtolower(trim((string) $value));
        return in_array($value, ['да', 'yes', 'true', '1', 'on', 'так', 'y'], true);
    }

    private function parseSynonyms($value): ?array
    {
        if (empty($value)) {
            return null;
        }

        if (is_array($value)) {
            $items = $value;
        } else {
            $items = preg_split('/[,;|]/u', $value) ?: [$value];
        }

        $items = array_filter(array_map(static function ($item) {
            return trim((string) $item);
        }, $items), fn ($item) => $item !== '');

        return empty($items) ? null : array_values(array_unique($items));
    }

    private function processPhoto(?string $source, array &$warnings, array &$errors): ?string
    {
        if (empty($source)) {
            return null;
        }

        if ($this->previewMode) {
            if (Str::startsWith($source, 'data:')) {
                $warnings[] = 'Фото буде збережене з base64 на CDN під час імпорту';
            } elseif (filter_var($source, FILTER_VALIDATE_URL)) {
                $warnings[] = 'Фото буде скопійоване на наш CDN під час імпорту';
            }

            return $source;
        }

        $tempPath = null;

        try {
            $mimeType = null;
            $extension = null;
            $binary = null;

            if (Str::startsWith($source, 'data:')) {
                $binary = $this->decodeBase64Image($source, $mimeType, $extension, $errors);
            } else {
                $binary = $this->fetchRemoteImage($source, $mimeType, $extension, $warnings, $errors);
            }

            if (!$binary) {
                return null;
            }

            $tempPath = tempnam(sys_get_temp_dir(), 'author_photo_');
            file_put_contents($tempPath, $binary);

            $extension = $extension ?: $this->guessExtensionFromMime($mimeType) ?: 'jpg';
            $mimeType = $mimeType ?: $this->guessMimeFromExtension($extension);

            $uploadedFile = new UploadedFile(
                $tempPath,
                'photo.' . $extension,
                $mimeType,
                null,
                true
            );

            $cdnUrl = CDNUploader::uploadFile($uploadedFile, 'authors/photos');

            if (!$cdnUrl) {
                $errors[] = 'Не вдалося зберегти фото на CDN';
                return null;
            }

            return $cdnUrl;
        } catch (\Exception $e) {
            Log::error('Помилка обробки фото при імпорті', [
                'message' => $e->getMessage(),
                'source_preview' => Str::limit($source, 100),
            ]);
            $errors[] = 'Помилка обробки фото: ' . $e->getMessage();
            return null;
        } finally {
            if ($tempPath && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    private function decodeBase64Image(string $source, ?string &$mimeType, ?string &$extension, array &$errors): ?string
    {
        if (!preg_match('/^data:(.*?);base64,(.*)$/', $source, $matches)) {
            $errors[] = 'Некоректний формат base64 для фото';
            return null;
        }

        $mimeType = $matches[1] ?: 'image/jpeg';
        $base64 = preg_replace('/\s+/', '', $matches[2]);
        $data = base64_decode(str_replace(' ', '+', $base64), true);

        if ($data === false) {
            $errors[] = 'Не вдалося розкодувати base64 фото';
            return null;
        }

        $extension = $this->guessExtensionFromMime($mimeType) ?: 'jpg';

        return $data;
    }

    private function fetchRemoteImage(string $url, ?string &$mimeType, ?string &$extension, array &$warnings, array &$errors): ?string
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $errors[] = 'Некоректне посилання на фото';
            return null;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
                    'User-Agent' => 'BooksForoomBot/1.0 (+https://booksforoom.example)',
                ])
                ->get($url);
        } catch (\Exception $e) {
            Log::warning('Не вдалося завантажити зовнішнє фото', [
                'url' => $url,
                'message' => $e->getMessage(),
            ]);
            $errors[] = 'Не вдалося завантажити фото: ' . $e->getMessage();
            return null;
        }

        if (!$response->successful()) {
            $errors[] = "Не вдалося завантажити фото (HTTP {$response->status()})";
            return null;
        }

        $binary = $response->body();

        if ($binary === '' || $binary === false) {
            $errors[] = 'Отримано порожнє фото';
            return null;
        }

        $mimeType = $response->header('Content-Type') ?: null;

        if (!$mimeType) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mimeType = finfo_buffer($finfo, $binary) ?: null;
                finfo_close($finfo);
            }
        }

        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if (!$extension && $mimeType) {
            $extension = $this->guessExtensionFromMime($mimeType);
        }

        if (!$extension) {
            $extension = 'jpg';
            $warnings[] = 'Не вдалося визначити формат фото, використано JPG';
        }

        return $binary;
    }

    private function guessExtensionFromMime(?string $mimeType): ?string
    {
        if (!$mimeType) {
            return null;
        }

        $map = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/avif' => 'avif',
            'image/svg+xml' => 'svg',
        ];

        return $map[$mimeType] ?? null;
    }

    private function guessMimeFromExtension(?string $extension): ?string
    {
        if (!$extension) {
            return null;
        }

        $map = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'avif' => 'image/avif',
            'svg' => 'image/svg+xml',
        ];

        return $map[strtolower($extension)] ?? 'image/jpeg';
    }
}
