<?php

namespace App\Jobs;

use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use App\Services\TranslationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportGoogleBooksBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Количество попыток и таймаут — большие партии с переводом могут долго выполняться. */
    public int $tries = 2;
    public int $timeout = 600;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $volumes;

    public function __construct(array $volumes)
    {
        $this->volumes = $volumes;
    }

    public function handle(): void
    {
        /** @var TranslationService $translator */
        $translator = app(TranslationService::class);

        foreach ($this->volumes as $volume) {
            $googleId = $volume['id'] ?? null;

            if (! $googleId) {
                continue;
            }

            $language = $this->normalizeLanguage($volume['language'] ?? null);

            $originalTitle = $volume['title'] ?? 'Без названия';
            $originalDescription = $volume['description'] ?? null;

            // Переводим название и описание на украинский
            $translatedTitle = $translator->translateToUkrainian($originalTitle);
            $translatedDescription = $translator->translateToUkrainian($originalDescription);

            $book = Book::firstOrCreate(
                ['google_volume_id' => $googleId],
                [
                    // Оригинальное название на английском
                    'title' => $originalTitle,
                    // Украинский перевод названия
                    'book_name_ua' => $translatedTitle,
                    // Описание на украинском
                    'annotation' => $translatedDescription,
                    'annotation_source' => 'google_books_translated_uk',
                    'author' => isset($volume['authors'][0]) ? $volume['authors'][0] : 'Невідомий автор',
                    'isbn' => $volume['isbn'] ?? null,
                    'publication_year' => $this->extractYear($volume['published_date'] ?? null),
                    'first_publish_year' => $this->extractYear($volume['published_date'] ?? null),
                    'publisher' => $volume['publisher'] ?? null,
                    'cover_image' => $volume['thumbnail'] ?? null,
                    'language' => $language,
                    'original_language' => $language,
                    'pages' => $volume['page_count'] ?? null,
                    'rating' => $volume['average_rating'] ?? 0,
                    'reviews_count' => $volume['ratings_count'] ?? 0,
                    'interesting_facts' => null,
                    'synonyms' => [],
                    'series' => null,
                    'series_number' => null,
                ]
            );

            // Обработка авторов
            $authorNames = $volume['authors'] ?? [];
            $authorIds = [];
            $primaryAuthorId = null;

            foreach ($authorNames as $index => $fullName) {
                if (! is_string($fullName) || trim($fullName) === '') {
                    continue;
                }

                $fullName = trim($fullName);
                $parts = preg_split('/\s+/u', $fullName);

                $firstName = $parts[0] ?? $fullName;
                $lastName = $parts[1] ?? ($parts[count($parts) - 1] ?? '');

                $author = Author::firstOrCreate(
                    [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                    ],
                    [
                        'middle_name' => null,
                        'biography' => null,
                        'birth_date' => null,
                        'death_date' => null,
                        'nationality' => null,
                        'photo' => null,
                        'website' => null,
                        'awards' => null,
                        'is_featured' => false,
                    ]
                );

                $authorIds[] = $author->id;

                if ($primaryAuthorId === null) {
                    $primaryAuthorId = $author->id;
                }
            }

            if ($primaryAuthorId !== null) {
                $book->author_id = $primaryAuthorId;
                $book->author = $authorNames[0] ?? $book->author;
                $book->save();
            }

            if (! empty($authorIds)) {
                $authorIds = array_values(array_unique(array_map('intval', $authorIds)));

                $rows = [];
                $now = now();
                foreach ($authorIds as $authorId) {
                    if ($authorId <= 0) {
                        continue;
                    }
                    $rows[] = [
                        'author_id' => $authorId,
                        'book_id' => $book->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if (! empty($rows)) {
                    // Атомарно и безопасно при параллельных запусках (не падает на UNIQUE).
                    DB::table('author_book')->insertOrIgnore($rows);
                }
            }

            $categories = $volume['categories'] ?? [];

            if (! empty($categories)) {
                $categoryIds = [];

                foreach ($categories as $categoryName) {
                    if (! is_string($categoryName) || $categoryName === '') {
                        continue;
                    }

                    $slug = Str::slug($categoryName);

                    $category = Category::firstOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $categoryName,
                            'description' => null,
                            'color' => '#3B82F6',
                            'icon' => null,
                            'sort_order' => 0,
                            'is_active' => true,
                        ]
                    );

                    $categoryIds[] = $category->id;
                }

                if (! empty($categoryIds)) {
                    $categoryIds = array_values(array_unique(array_map('intval', $categoryIds)));

                    $rows = [];
                    $now = now();
                    foreach ($categoryIds as $categoryId) {
                        if ($categoryId <= 0) {
                            continue;
                        }
                        $rows[] = [
                            'book_id' => $book->id,
                            'category_id' => $categoryId,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    if (! empty($rows)) {
                        DB::table('book_category')->insertOrIgnore($rows);
                    }
                }
            }
        }
    }

    protected function extractYear(?string $publishedDate): ?int
    {
        if (! $publishedDate) {
            return null;
        }

        if (preg_match('/^\d{4}/', $publishedDate, $matches)) {
            return (int) $matches[0];
        }

        return null;
    }

    /**
     * Нормализуем язык, чтобы он никогда не был null.
     * Если язык не указан или пустой – считаем, что это en.
     */
    protected function normalizeLanguage(mixed $language): string
    {
        if (! is_string($language)) {
            return 'en';
        }

        $language = trim($language);

        if ($language === '') {
            return 'en';
        }

        return $language;
    }
}

