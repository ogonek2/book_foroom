<?php

namespace App\Imports;

use App\Helpers\CDNUploader;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
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

class SimpleBooksImport implements ToModel, WithHeadingRow, SkipsOnError, WithChunkReading
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

        Log::info("Обрабатываем строку {$this->currentRowNumber}: " . ($row['nazvanie'] ?? $row['book_name_ua'] ?? 'Без названия'));

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
                $categoryIds = $result['category_ids'];

                /** @var \App\Models\Book|null $book */
                $existingBookId = $result['existing_book_id'] ?? null;

                if ($existingBookId) {
                    $book = Book::find($existingBookId);

                    if ($book) {
                        $book->fill($attributes);
                        $book->save();
                    } else {
                        $book = Book::create($attributes);
                    }
                } else {
                    $book = Book::create($attributes);
                }
                if (!empty($categoryIds)) {
                    $book->categories()->sync($categoryIds);
                }

                $this->rowCount++;

                $action = $book->wasRecentlyCreated ? 'Создана' : ($existingBookId ? 'Обновлена' : 'Создана');
                Log::info("{$action} книга: {$book->title} (ID: {$book->id})");
                return $book;
            });
        } catch (\Exception $e) {
            Log::error("Ошибка импорта строки {$this->currentRowNumber}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            $this->errors[] = "Строка {$this->currentRowNumber}: " . $e->getMessage();
            return null;
        }
    }

    private function processRowData(array $row, int $rowNumber): array
    {
        $errors = [];
        $warnings = [];

        $title = trim($row['nazvanie'] ?? $row['book_name_ua'] ?? '');
        if ($title === '') {
            $errors[] = 'Название книги обязательно';
        }

        $titleUa = trim($row['book_name_ua'] ?? $row['nazvanie'] ?? '') ?: null;

        $slug = !empty($row['slag']) ? Str::slug($row['slag']) : Str::slug($title);
        $originalSlug = $slug;

        $existingBook = $this->findExistingBook($title, $titleUa, $slug);

        if ($this->previewMode) {
            if ($slug && Book::where('slug', $slug)
                    ->when($existingBook, fn ($query) => $query->where('id', '!=', $existingBook->id))
                    ->exists()) {
                $warnings[] = "Слаг '{$slug}' уже используется і буде змінений при імпорті";
            }
        } else {
            if ($existingBook) {
                if ($slug && $slug !== $existingBook->slug) {
                    $slugInUse = Book::where('slug', $slug)
                        ->where('id', '!=', $existingBook->id)
                        ->exists();

                    if ($slugInUse) {
                        $warnings[] = "Слаг '{$slug}' уже используется іншою книгою, залишено '{$existingBook->slug}'";
                        $slug = $existingBook->slug;
                    } else {
                        $warnings[] = "Слаг обновлен на '{$slug}' для существующей книги";
                    }
                } else {
                    $slug = $existingBook->slug;
                }
            } else {
                $counter = 1;
                while ($slug && Book::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                if ($slug !== $originalSlug) {
                    $warnings[] = "Слаг изменен на '{$slug}' для уникальности";
                }
            }
        }

        $annotation = !empty($row['annotation'])
            ? trim($row['annotation'])
            : (!empty($row['opisanie']) ? trim($row['opisanie']) : null);
        $annotationSource = !empty($row['annotation_source']) ? trim($row['annotation_source']) : null;

        $isbn = !empty($row['isbn']) ? trim($row['isbn']) : null;

        $publicationYear = $this->extractYear($row['god_izdaniya'] ?? '');
        $firstPublishYear = $this->extractYear($row['first_publish_year'] ?? '') ?? $publicationYear;
        if (!$publicationYear && $firstPublishYear) {
            $publicationYear = $firstPublishYear;
        }

        $publisher = !empty($row['izdatelstvo'] ?? $row['publisher'] ?? $row['publishers'] ?? null)
            ? trim($row['izdatelstvo'] ?? $row['publisher'] ?? $row['publishers'])
            : null;

        $coverImageSource = !empty($row['oblozhka'])
            ? trim($row['oblozhka'])
            : (!empty($row['img']) ? trim($row['img']) : null);

        $coverImage = $this->processCoverImage($coverImageSource, $warnings, $errors);

        $language = $this->mapLanguage($row['yazyk'] ?? '');
        $originalLanguage = $this->mapLanguage($row['original_language'] ?? $row['book_name_eng'] ?? '') ?: $language;

        $pages = $this->extractNumber($row['pages'] ?? $row['stranitsy'] ?? '');
        $rating = $this->extractRating($row['reiting'] ?? '');
        $reviewsCount = $this->extractNumber($row['kolichestvo_recenziy'] ?? '') ?? 0;

        $categorySource = $row['genre'] ?? $row['kategoriya'] ?? '';
        $categories = $this->resolveCategories($categorySource);
        if (empty($categories['names'])) {
            $errors[] = 'Жанр/категория обязательна';
        }

        $legacyAuthor = !empty($row['avtor_staryy']) ? trim($row['avtor_staryy']) : null;

        $authorRaw = $row['author'] ?? $row['avtor'] ?? $legacyAuthor ?? '';
        $authorInfo = $this->resolveAuthor($authorRaw);
        $authorId = $authorInfo['id'];
        if ($authorInfo['warning']) {
            $warnings[] = $authorInfo['warning'];
        }

        $authorDisplayName = $legacyAuthor
            ?: ($authorInfo['name'] ?? null)
            ?: (is_string($authorRaw) ? trim($authorRaw) : null);

        if (!$authorDisplayName) {
            $errors[] = 'Автор обязателен';
        }

        $isFeatured = $this->parseBoolean($row['rekomenduemaya'] ?? '');

        $synonyms = $this->parseSynonyms($row['synonyms'] ?? '');
        $series = !empty($row['series'] ?? $row['seriia'] ?? $row['seriya'] ?? $row['series_name'] ?? null)
            ? trim($row['series'] ?? $row['seriia'] ?? $row['seriya'] ?? $row['series_name'])
            : null;

        if (!empty($categories['warnings'])) {
            $warnings = array_merge($warnings, $categories['warnings']);
        }

        $attributes = [
            'title' => $title,
            'book_name_ua' => $titleUa,
            'slug' => $slug,
            'annotation' => $annotation,
            'annotation_source' => $annotationSource,
            'author' => $authorDisplayName,
            'isbn' => $isbn,
            'publication_year' => $publicationYear,
            'first_publish_year' => $firstPublishYear,
            'publisher' => $publisher,
            'cover_image' => $coverImage,
            'language' => $language,
            'original_language' => $originalLanguage,
            'pages' => $pages,
            'rating' => $rating,
            'reviews_count' => $reviewsCount,
            'author_id' => $authorId,
            'is_featured' => $isFeatured,
            'synonyms' => $synonyms,
            'series' => $series,
        ];

        if ($existingBook && $coverImage === null && empty($coverImageSource)) {
            unset($attributes['cover_image']);
        }

        if ($this->previewMode) {
            unset($attributes['author_id']);
        }

        return [
            'valid' => empty($errors),
            'row' => $rowNumber,
            'attributes' => $attributes,
            'category_ids' => $categories['ids'],
            'errors' => $errors,
            'warnings' => $warnings,
            'existing_book_id' => $existingBook?->id,
            'meta' => [
                'author' => [
                    'name' => $authorInfo['name'],
                    'will_create' => $authorInfo['will_create'],
                ],
                'categories' => [
                    'names' => $categories['names'],
                    'will_create' => $categories['created'],
                ],
                'synonyms' => $synonyms ?? [],
                'slug' => $slug,
                'action' => $existingBook ? 'update' : 'create',
                'existing_book_id' => $existingBook?->id,
                'existing_book_title' => $existingBook?->title,
                'cover_image_source' => $coverImageSource,
                'cover_image_url' => $coverImage,
            ],
        ];
    }

    private function extractYear($value): ?int
    {
        if (empty($value)) {
            return null;
        }

        if (preg_match('/\b(19|20)\d{2}\b/u', $value, $matches)) {
            $year = (int) $matches[0];
            if ($year >= 1800 && $year <= (date('Y') + 10)) {
                return $year;
            }
        }

        return null;
    }

    private function extractNumber($value): ?int
    {
        if (empty($value)) {
            return null;
        }

        $number = preg_replace('/[^0-9]/', '', (string) $value);

        return $number !== '' ? (int) $number : null;
    }

    private function extractRating($value): float
    {
        if (empty($value)) {
            return 0.0;
        }

        $value = str_replace(',', '.', (string) $value);

        if (preg_match('/(\d+\.?\d*)/', $value, $matches)) {
            $rating = (float) $matches[1];
            return round(min(5.0, max(0.0, $rating)), 2);
        }

        return 0.0;
    }

    private function resolveCategories($value): array
    {
        if (empty($value)) {
            return [
                'ids' => [],
                'names' => [],
                'created' => [],
                'warnings' => [],
            ];
        }

        $names = preg_split('/[,;|]/u', $value) ?: [$value];
        $names = array_values(array_filter(array_map(function ($name) {
            return $this->normalizeCategoryName($name);
        }, $names), fn ($name) => $name !== ''));

        $ids = [];
        $created = [];
        $warnings = [];

        foreach ($names as $categoryName) {
            $slug = Str::slug($categoryName);
            $category = Category::query()
                ->where('name', $categoryName)
                ->when($slug !== '', fn ($query) => $query->orWhere('slug', $slug))
                ->first();

            if (!$category) {
                if ($this->previewMode) {
                    $created[] = $categoryName;
                    $warnings[] = "Буде створено нову категорію '{$categoryName}'";
                } else {
                    $uniqueSlug = $this->ensureUniqueCategorySlug($slug);
                    $category = Category::create([
                        'name' => $categoryName,
                        'slug' => $uniqueSlug,
                        'description' => 'Автоматично створено під час імпорту',
                        'color' => '#3B82F6',
                        'icon' => 'heroicon-o-book-open',
                        'sort_order' => 0,
                        'is_active' => true,
                    ]);
                    $created[] = $categoryName;
                    $warnings[] = "Создана новая категория '{$categoryName}'";
                    Log::info("Создана новая категория: {$category->name} (ID: {$category->id})");
                }
            }

            if ($category) {
                $ids[] = $category->id;
            }
        }

        return [
            'ids' => array_values(array_unique(array_filter($ids))),
            'names' => $names,
            'created' => $created,
            'warnings' => $warnings,
        ];
    }

    private function resolveAuthor($authorName): array
    {
        if (empty($authorName)) {
            return [
                'id' => null,
                'name' => null,
                'will_create' => false,
                'warning' => null,
            ];
        }

        $authorName = trim($authorName);
        $nameParts = preg_split('/\s+/u', $authorName);
        $firstName = $nameParts[0] ?? '';
        $lastName = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : null;

        $query = Author::query()->where('first_name', $firstName);
        if ($lastName) {
            $query->where('last_name', $lastName);
        }

        $author = $query->first();

        if (!$author) {
            if ($this->previewMode) {
                return [
                    'id' => null,
                    'name' => trim($authorName),
                    'will_create' => true,
                    'warning' => "Буде створено нового автора '{$authorName}'",
                ];
            }

            $author = Author::create([
                'first_name' => $firstName ?: $authorName,
                'last_name' => $lastName,
                'slug' => Str::slug($authorName),
                'biography' => null,
                'is_featured' => false,
            ]);

            Log::info("Создан новый автор: {$author->first_name} {$author->last_name} (ID: {$author->id})");

            return [
                'id' => $author->id,
                'name' => trim(($author->first_name ?? '') . ' ' . ($author->last_name ?? '')),
                'will_create' => true,
                'warning' => "Создан новый автор '{$authorName}'",
            ];
        }

        return [
            'id' => $author->id,
            'name' => trim(($author->first_name ?? '') . ' ' . ($author->last_name ?? '')),
            'will_create' => false,
            'warning' => null,
        ];
    }

    private function mapLanguage($value): string
    {
        if (empty($value)) {
            return 'ru';
        }

        $value = strtolower(trim($value));

        $languageMap = [
            'русский' => 'ru',
            'russian' => 'ru',
            'украинский' => 'uk',
            'ukrainian' => 'uk',
            'английский' => 'en',
            'english' => 'en',
            'немецкий' => 'de',
            'german' => 'de',
            'французский' => 'fr',
            'french' => 'fr',
            'испанский' => 'es',
            'spanish' => 'es',
        ];

        return $languageMap[$value] ?? substr($value, 0, 5);
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

    private function processCoverImage(?string $source, array &$warnings, array &$errors): ?string
    {
        if (empty($source)) {
            return null;
        }

        if ($this->previewMode) {
            if (Str::startsWith($source, 'data:')) {
                $warnings[] = 'Обкладинка буде збережена з base64 на CDN під час імпорту';
            } elseif (filter_var($source, FILTER_VALIDATE_URL)) {
                $warnings[] = 'Обкладинка буде скопійована на наш CDN під час імпорту';
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

            $tempPath = tempnam(sys_get_temp_dir(), 'book_cover_');
            file_put_contents($tempPath, $binary);

            $extension = $extension ?: $this->guessExtensionFromMime($mimeType) ?: 'jpg';
            $mimeType = $mimeType ?: $this->guessMimeFromExtension($extension);

            $uploadedFile = new UploadedFile(
                $tempPath,
                'cover.' . $extension,
                $mimeType,
                null,
                true
            );

            $cdnUrl = CDNUploader::uploadFile($uploadedFile, 'books/covers');

            if (!$cdnUrl) {
                $errors[] = 'Не вдалося зберегти обкладинку на CDN';
                return null;
            }

            return $cdnUrl;
        } catch (\Exception $e) {
            Log::error('Помилка обробки обкладинки при імпорті', [
                'message' => $e->getMessage(),
                'source_preview' => Str::limit($source, 100),
            ]);
            $errors[] = 'Помилка обробки обкладинки: ' . $e->getMessage();
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
            $errors[] = 'Некоректний формат base64 для обкладинки';
            return null;
        }

        $mimeType = $matches[1] ?: 'image/jpeg';
        $base64 = preg_replace('/\s+/', '', $matches[2]);
        $data = base64_decode(str_replace(' ', '+', $base64), true);

        if ($data === false) {
            $errors[] = 'Не вдалося розкодувати base64 обкладинки';
            return null;
        }

        $extension = $this->guessExtensionFromMime($mimeType) ?: 'jpg';

        return $data;
    }

    private function fetchRemoteImage(string $url, ?string &$mimeType, ?string &$extension, array &$warnings, array &$errors): ?string
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $errors[] = 'Некоректне посилання на обкладинку';
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
            Log::warning('Не вдалося завантажити зовнішню обкладинку', [
                'url' => $url,
                'message' => $e->getMessage(),
            ]);
            $errors[] = 'Не вдалося завантажити обкладинку: ' . $e->getMessage();
            return null;
        }

        if (!$response->successful()) {
            $errors[] = "Не вдалося завантажити обкладинку (HTTP {$response->status()})";
            return null;
        }

        $binary = $response->body();

        if ($binary === '' || $binary === false) {
            $errors[] = 'Отримано порожню обкладинку';
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
            $warnings[] = 'Не вдалося визначити формат обкладинки, використано JPG';
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

        return $map[strtolower($mimeType)] ?? null;
    }

    private function guessMimeFromExtension(?string $extension): string
    {
        $extension = strtolower((string) $extension);

        $map = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'avif' => 'image/avif',
            'svg' => 'image/svg+xml',
        ];

        return $map[$extension] ?? 'image/jpeg';
    }

    private function normalizeCategoryName(string $name): string
    {
        $name = str_replace("\xC2\xA0", ' ', $name);
        $name = preg_replace('/\s+/u', ' ', $name);
        return trim($name);
    }

    private function ensureUniqueCategorySlug(string $baseSlug): string
    {
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'category';
        $slug = $baseSlug;
        $counter = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function findExistingBook(?string $title, ?string $titleUa, ?string $slug): ?Book
    {
        $query = Book::query();
        $hasCondition = false;

        if ($title) {
            $query->where('title', $title);
            $hasCondition = true;
        }

        if ($titleUa) {
            $method = $hasCondition ? 'orWhere' : 'where';
            $query->{$method}('book_name_ua', $titleUa);
            $hasCondition = true;
        }

        if ($slug) {
            $method = $hasCondition ? 'orWhere' : 'where';
            $query->{$method}('slug', $slug);
            $hasCondition = true;
        }

        if (!$hasCondition) {
            return null;
        }

        return $query->first();
    }

    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (!empty(trim((string) $value))) {
                return false;
            }
        }

        return true;
    }

    public function getPreviewRows(): array
    {
        return array_map(function (array $result) {
            return [
                'row' => $result['row'],
                'title' => $result['attributes']['title'] ?? '',
                'book_name_ua' => $result['attributes']['book_name_ua'] ?? '',
                'author' => $result['meta']['author']['name'] ?? '',
                'will_create_author' => $result['meta']['author']['will_create'] ?? false,
                'categories' => $result['meta']['categories']['names'] ?? [],
                'will_create_categories' => $result['meta']['categories']['will_create'] ?? [],
                'warnings' => $result['warnings'] ?? [],
                'errors' => $result['errors'] ?? [],
                'synonyms' => $result['meta']['synonyms'] ?? [],
                'slug' => $result['meta']['slug'] ?? '',
                'action' => $result['meta']['action'] ?? 'create',
                'existing_book_id' => $result['meta']['existing_book_id'] ?? null,
                'existing_book_title' => $result['meta']['existing_book_title'] ?? null,
                'cover_image_source' => $result['meta']['cover_image_source'] ?? null,
                'cover_image_url' => $result['meta']['cover_image_url'] ?? null,
            ];
        }, $this->previewRows);
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

    public function chunkSize(): int
    {
        return 100;
    }

    public function onError(\Throwable $e): void
    {
        Log::error("Ошибка импорта: " . $e->getMessage());
    }
}


