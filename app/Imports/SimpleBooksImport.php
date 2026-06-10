<?php

namespace App\Imports;

use App\Helpers\CDNUploader;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookFormat;
use App\Models\Category;
use App\Services\CategoryTreeService;
use App\Support\BookLanguage;
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

        Log::info("Обрабатываем строку {$this->currentRowNumber}: " . ($this->rowValue($row, ['main_book_name', 'nazvanie']) ?? $row['book_name_ua'] ?? 'Без названия'));

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

                $formatIds = $result['format_ids'] ?? [];
                if (!empty($formatIds)) {
                    $book->formats()->sync($formatIds);
                }

                $authorIds = $result['author_ids'] ?? [];
                if (!empty($authorIds)) {
                    $book->authors()->sync($authorIds);
                } elseif ($book->author_id) {
                    $book->authors()->syncWithoutDetaching([$book->author_id]);
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

        $title = trim($this->rowValue($row, ['main_book_name', 'nazvanie']) ?? '');
        if ($title === '') {
            $title = trim((string) ($row['book_name_ua'] ?? ''));
        }
        if ($title === '') {
            $errors[] = 'Назва книги обовʼязкова (main_book_name або book_name_ua)';
        }

        $titleUa = trim((string) ($row['book_name_ua'] ?? '')) ?: null;
        if ($titleUa === $title) {
            $titleUa = null;
        }

        $slugSource = $this->rowValue($row, ['slag', 'slug']);
        $slug = $slugSource !== null && $slugSource !== '' ? Str::slug($slugSource) : Str::slug($title);
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

        $ukrYearRaw = $this->rowValue($row, ['ukr_publish_year', 'UKR_publish_year', 'god_izdaniya']);
        $firstYearRaw = $this->rowValue($row, ['first_publish_year']);

        $publicationYear = $ukrYearRaw !== null ? $this->extractYear($ukrYearRaw) : null;
        $firstPublishYear = $firstYearRaw !== null ? $this->extractYear($firstYearRaw) : null;

        if ($ukrYearRaw !== null && $publicationYear === null) {
            $warnings[] = "Не вдалося розпізнати UKR_publish_year: '{$ukrYearRaw}'";
        }
        if ($firstYearRaw !== null && $firstPublishYear === null) {
            $warnings[] = "Не вдалося розпізнати first_publish_year: '{$firstYearRaw}'";
        }

        if (!$publicationYear && $firstPublishYear) {
            $publicationYear = $firstPublishYear;
        }

        $coverImageSource = $this->rowValue($row, ['cover', 'oblozhka', 'img']);
        $coverImageSource = $coverImageSource !== null ? trim($coverImageSource) : null;

        $coverImage = $this->processCoverImage($coverImageSource, $warnings, $errors);

        $originalLanguage = BookLanguage::mapImportValue(
            $row['original_language'] ?? $row['mova_originalu'] ?? null
        );

        $pages = $this->extractNumber($this->rowValue($row, ['pages', 'stranitsy']) ?? '');

        $categorySource = $this->rowValue($row, ['category', 'kategoriya', 'genre']) ?? '';
        $categories = $this->resolveCategories($categorySource);
        if (empty($categories['names'])) {
            $errors[] = 'Категорія обовʼязкова (category)';
        }

        $authorsInfo = $this->resolveAuthorsFromRow($row);
        $authorIds = $authorsInfo['ids'];
        $authorId = $authorIds[0] ?? null;
        $warnings = array_merge($warnings, $authorsInfo['warnings']);

        $authorDisplayName = trim((string) ($row['avtor_staryy'] ?? '')) ?: $authorsInfo['display_name'];

        if (!$authorDisplayName) {
            $errors[] = 'Автор обовʼязковий: first_name_author + last_name_author або avtor_staryy';
        }

        $isFeatured = $this->parseBoolean(
            $this->rowValue($row, ['recommend', 'rekomenduemaya']) ?? ''
        );

        $synonyms = $this->parseSynonyms(
            $this->rowValue($row, ['synonym', 'synonyms']) ?? ''
        );
        $series = $this->rowValue($row, ['series', 'seriia', 'seriya', 'series_name']);
        $series = $series !== null && trim($series) !== '' ? trim($series) : null;
        $seriesNumber = $this->parseSeriesNumber(
            $this->rowValue($row, ['num_in_series', 'nomer_v_serii', 'series_number', 'nomer_v_seriyi']) ?? ''
        );

        $cycle = $this->rowValue($row, ['cycle', 'tsikl', 'cikl']);
        $cycle = $cycle !== null ? trim($cycle) : null;

        $includedWorks = $this->parseListField(
            $this->rowValue($row, ['included_works', 'included_work', 'zmist_zbirnyka']) ?? ''
        );

        $formatSource = $this->rowValue($row, ['format', 'formats', 'formaty']) ?? '';
        $formats = $this->resolveFormats($formatSource);
        $warnings = array_merge($warnings, $formats['warnings']);

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
            'publication_year' => $publicationYear,
            'first_publish_year' => $firstPublishYear,
            'cover_image' => $coverImage,
            'original_language' => $originalLanguage,
            'pages' => $pages,
            'author_id' => $authorId,
            'is_featured' => $isFeatured,
            'synonyms' => $synonyms,
            'series' => $series,
            'series_number' => $seriesNumber,
            'cycle' => $cycle,
            'included_works' => $includedWorks,
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
            'format_ids' => $formats['ids'],
            'author_ids' => $authorIds,
            'errors' => $errors,
            'warnings' => $warnings,
            'existing_book_id' => $existingBook?->id,
            'meta' => [
                'author' => [
                    'name' => $authorDisplayName,
                    'names' => $authorsInfo['names'],
                    'will_create' => $authorsInfo['will_create'],
                ],
                'categories' => [
                    'names' => $categories['names'],
                    'will_create' => $categories['created'],
                ],
                'formats' => [
                    'names' => $formats['names'],
                    'will_create' => $formats['created'],
                ],
                'cycle' => $cycle,
                'included_works' => $includedWorks ?? [],
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

    private function extractYear(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            $year = (int) $value->format('Y');

            return $this->isValidYear($year) ? $year : null;
        }

        if (is_numeric($value)) {
            $numeric = (float) $value;

            if ($this->isValidYear((int) $numeric) && $numeric == (int) $numeric) {
                return (int) $numeric;
            }

            if ($numeric > 10000 && class_exists(\PhpOffice\PhpSpreadsheet\Shared\Date::class)) {
                try {
                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($numeric);
                    $year = (int) $date->format('Y');

                    return $this->isValidYear($year) ? $year : null;
                } catch (\Throwable) {
                    // fall through to string parsing
                }
            }
        }

        $value = trim((string) $value);

        if (preg_match('/\b(1[89]\d{2}|20\d{2})\b/u', $value, $matches)) {
            $year = (int) $matches[1];

            return $this->isValidYear($year) ? $year : null;
        }

        return null;
    }

    private function isValidYear(int $year): bool
    {
        return $year >= 1000 && $year <= ((int) date('Y') + 10);
    }

    private function extractNumber($value): ?int
    {
        if (empty($value)) {
            return null;
        }

        $number = preg_replace('/[^0-9]/', '', (string) $value);

        return $number !== '' ? (int) $number : null;
    }

    private function parseSeriesNumber($value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = trim((string) $value);
        $value = str_replace(',', '.', $value);

        if (preg_match('/^\d+(?:\.\d+)?$/', $value)) {
            return $value;
        }

        return mb_substr($value, 0, 50);
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
            if (preg_match('/[>\/]/u', $categoryName)) {
                $resolved = CategoryTreeService::resolvePath($categoryName, $this->previewMode);

                if ($resolved['will_create']) {
                    $created[] = $categoryName;
                    $warnings[] = "Буде створено категорію (шлях) '{$categoryName}'";
                }

                if (!empty($resolved['id'])) {
                    $ids[] = $resolved['id'];
                }

                continue;
            }

            $slug = Str::slug($categoryName);
            $category = Category::query()
                ->where(function ($query) use ($categoryName, $slug) {
                    $query->where('name', $categoryName);
                    if ($slug !== '') {
                        $query->orWhere('slug', $slug);
                    }
                })
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

    /**
     * @param  array<string, mixed>  $row
     */
    private function resolveAuthorsFromRow(array $row): array
    {
        $firstNames = $this->splitMultiValue(
            $this->rowValue($row, ['first_name_author', 'first_name']) ?? ''
        );
        $lastNames = $this->splitMultiValue(
            $this->rowValue($row, ['last_name_author', 'last_name']) ?? ''
        );

        if ($firstNames !== [] || $lastNames !== []) {
            $ids = [];
            $names = [];
            $warnings = [];
            $willCreate = false;

            foreach ($this->pairAuthorNameParts($firstNames, $lastNames) as $pair) {
                $resolved = $this->resolveAuthorByParts($pair['first'], $pair['last']);
                if ($resolved['id']) {
                    $ids[] = $resolved['id'];
                }
                if ($resolved['name']) {
                    $names[] = $resolved['name'];
                }
                if ($resolved['warning']) {
                    $warnings[] = $resolved['warning'];
                }
                if ($resolved['will_create']) {
                    $willCreate = true;
                }
            }

            return [
                'ids' => array_values(array_unique(array_filter($ids))),
                'names' => $names,
                'display_name' => $names !== [] ? implode(', ', $names) : null,
                'will_create' => $willCreate,
                'warnings' => $warnings,
            ];
        }

        $fallback = $this->rowValue($row, ['avtor_staryy', 'avtor', 'author']) ?? '';

        return $this->resolveAuthors($fallback);
    }

    /**
     * @return array<int, string>
     */
    private function splitMultiValue(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        $parts = preg_split('/[;|]/u', $value) ?: [$value];

        return array_values(array_filter(array_map('trim', $parts), fn ($part) => $part !== ''));
    }

    /**
     * @param  array<int, string>  $firstNames
     * @param  array<int, string>  $lastNames
     * @return array<int, array{first: string, last: string}>
     */
    private function pairAuthorNameParts(array $firstNames, array $lastNames): array
    {
        $count = max(count($firstNames), count($lastNames), 1);
        $pairs = [];

        for ($i = 0; $i < $count; $i++) {
            $first = trim($firstNames[$i] ?? $firstNames[0] ?? '');
            $last = trim($lastNames[$i] ?? $lastNames[0] ?? '');

            if ($first === '' && $last === '') {
                continue;
            }

            $pairs[] = ['first' => $first, 'last' => $last];
        }

        return $pairs;
    }

    private function resolveAuthorByParts(string $firstName, string $lastName): array
    {
        $firstName = trim($firstName);
        $lastName = trim($lastName);

        if ($firstName === '' && $lastName === '') {
            return [
                'id' => null,
                'name' => null,
                'will_create' => false,
                'warning' => null,
            ];
        }

        $displayName = trim($firstName . ' ' . $lastName);

        $query = Author::query()->where('first_name', $firstName);
        if ($lastName !== '') {
            $query->where('last_name', $lastName);
        } else {
            $query->where(function ($q) {
                $q->whereNull('last_name')->orWhere('last_name', '');
            });
        }

        $author = $query->first();

        if (!$author) {
            if ($this->previewMode) {
                return [
                    'id' => null,
                    'name' => $displayName,
                    'will_create' => true,
                    'warning' => "Буде створено нового автора '{$displayName}'",
                ];
            }

            $author = Author::create([
                'first_name' => $firstName ?: $lastName,
                'last_name' => $lastName !== '' ? $lastName : null,
                'slug' => Str::slug($displayName),
                'biography' => null,
                'is_featured' => false,
            ]);

            Log::info("Создан новый автор: {$author->first_name} {$author->last_name} (ID: {$author->id})");

            return [
                'id' => $author->id,
                'name' => trim(($author->first_name ?? '') . ' ' . ($author->last_name ?? '')),
                'will_create' => true,
                'warning' => "Создан новый автор '{$displayName}'",
            ];
        }

        return [
            'id' => $author->id,
            'name' => trim(($author->first_name ?? '') . ' ' . ($author->last_name ?? '')),
            'will_create' => false,
            'warning' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<int, string>  $keys
     */
    private function rowValue(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $row)) {
                continue;
            }

            $value = $row[$key];
            if ($value !== null && trim((string) $value) !== '') {
                return trim((string) $value);
            }
        }

        return null;
    }

    private function resolveAuthors($authorValue): array
    {
        if (empty($authorValue)) {
            return [
                'ids' => [],
                'names' => [],
                'display_name' => null,
                'will_create' => false,
                'warnings' => [],
            ];
        }

        $rawNames = preg_split('/[;|]/u', (string) $authorValue) ?: [(string) $authorValue];
        $rawNames = array_values(array_filter(array_map('trim', $rawNames), fn ($name) => $name !== ''));

        $ids = [];
        $names = [];
        $warnings = [];
        $willCreate = false;

        foreach ($rawNames as $authorName) {
            $resolved = $this->resolveSingleAuthor($authorName);
            if ($resolved['id']) {
                $ids[] = $resolved['id'];
            }
            if ($resolved['name']) {
                $names[] = $resolved['name'];
            }
            if ($resolved['warning']) {
                $warnings[] = $resolved['warning'];
            }
            if ($resolved['will_create']) {
                $willCreate = true;
            }
        }

        return [
            'ids' => array_values(array_unique(array_filter($ids))),
            'names' => $names,
            'display_name' => $names !== [] ? implode(', ', $names) : null,
            'will_create' => $willCreate,
            'warnings' => $warnings,
        ];
    }

    private function resolveSingleAuthor(string $authorName): array
    {
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
                    'name' => $authorName,
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
        return $this->parseListField($value);
    }

    private function parseListField($value): ?array
    {
        if (empty($value)) {
            return null;
        }

        if (is_array($value)) {
            $items = $value;
        } elseif (str_contains((string) $value, ';') || str_contains((string) $value, '|')) {
            $items = preg_split('/[;|]/u', (string) $value) ?: [(string) $value];
        } else {
            $items = [trim((string) $value)];
        }

        $items = array_filter(array_map(static function ($item) {
            return trim((string) $item);
        }, $items), fn ($item) => $item !== '');

        return empty($items) ? null : array_values(array_unique($items));
    }

    private function resolveFormats(?string $value): array
    {
        if ($value === null || trim($value) === '') {
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

        foreach ($names as $formatName) {
            $slug = Str::slug($formatName);
            $format = BookFormat::query()
                ->where('name', $formatName)
                ->when($slug !== '', fn ($query) => $query->orWhere('slug', $slug))
                ->first();

            if (!$format) {
                if ($this->previewMode) {
                    $created[] = $formatName;
                    $warnings[] = "Буде створено новий формат '{$formatName}'";
                } else {
                    $uniqueSlug = $this->ensureUniqueFormatSlug($slug !== '' ? $slug : Str::slug($formatName));
                    $format = BookFormat::create([
                        'name' => $formatName,
                        'slug' => $uniqueSlug,
                        'sort_order' => 0,
                        'is_active' => true,
                    ]);
                    $created[] = $formatName;
                    $warnings[] = "Створено новий формат '{$formatName}'";
                    Log::info("Створено формат книги: {$format->name} (ID: {$format->id})");
                }
            }

            if ($format) {
                $ids[] = $format->id;
            }
        }

        return [
            'ids' => array_values(array_unique(array_filter($ids))),
            'names' => $names,
            'created' => $created,
            'warnings' => $warnings,
        ];
    }

    private function ensureUniqueFormatSlug(string $baseSlug): string
    {
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'format';
        $slug = $baseSlug;
        $counter = 1;

        while (BookFormat::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
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


