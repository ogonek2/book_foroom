<?php

namespace App\Jobs;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Services\TranslationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ImportOpenLibraryBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 600;

    /** @var array<int, array<string, mixed>> */
    public array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function handle(): void
    {
        /** @var TranslationService $translator */
        $translator = app(TranslationService::class);

        foreach ($this->items as $item) {
            $workKey = $this->normalizeWorkKey($item['work_key'] ?? $item['id'] ?? null);
            if ($workKey === null || $workKey === '') {
                continue;
            }

            $language = $this->normalizeLanguage($item['language'] ?? null);
            $originalTitle = $item['title'] ?? 'Без названия';
            $originalDescription = $item['description'] ?? null;
            $translatedTitle = $translator->translateToUkrainian($originalTitle);
            $translatedDescription = $translator->translateToUkrainian($originalDescription);

            $book = Book::firstOrCreate(
                ['open_library_work_id' => $workKey],
                [
                    'title' => $originalTitle,
                    'book_name_ua' => $translatedTitle,
                    'annotation' => $translatedDescription,
                    'annotation_source' => 'open_library_translated_uk',
                    'author' => isset($item['authors'][0]) ? $item['authors'][0] : 'Невідомий автор',
                    'isbn' => $item['isbn'] ?? null,
                    'publication_year' => $this->extractYear($item['first_publish_year'] ?? null),
                    'first_publish_year' => $this->extractYear($item['first_publish_year'] ?? null),
                    'publisher' => $item['publisher'] ?? null,
                    'cover_image' => $item['cover_image'] ?? null,
                    'language' => $language,
                    'original_language' => $language,
                    'pages' => $item['page_count'] ?? null,
                    'rating' => 0,
                    'reviews_count' => 0,
                    'interesting_facts' => null,
                    'synonyms' => [],
                    'series' => null,
                    'series_number' => null,
                ]
            );

            $authorNames = $item['authors'] ?? [];
            $authorIds = [];
            $primaryAuthorId = null;

            foreach ($authorNames as $index => $fullName) {
                if (! is_string($fullName) || trim($fullName) === '') {
                    continue;
                }
                $fullName = trim($fullName);
                $parts = preg_split('/\s+/u', $fullName, 2);
                $firstName = $parts[0] ?? $fullName;
                $lastName = $parts[1] ?? '';

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
                $book->authors()->syncWithoutDetaching($authorIds);
            }

            $categories = $item['categories'] ?? [];
            if (! empty($categories)) {
                $categoryIds = [];
                foreach ($categories as $nameOrKey) {
                    if (! is_string($nameOrKey) || $nameOrKey === '') {
                        continue;
                    }
                    $slug = Str::slug($nameOrKey);
                    if ($slug === '') {
                        continue;
                    }
                    $name = strlen($nameOrKey) <= 3 ? ucfirst($nameOrKey) : $nameOrKey;
                    $category = Category::firstOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $name,
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
                    $book->categories()->syncWithoutDetaching($categoryIds);
                }
            }
        }
    }

    protected function normalizeWorkKey(mixed $key): ?string
    {
        if ($key === null || $key === '') {
            return null;
        }
        $key = is_string($key) ? trim($key) : (string) $key;
        $key = ltrim($key, '/');
        if (str_starts_with($key, 'works/')) {
            $key = substr($key, 6);
        }
        return $key === '' ? null : $key;
    }

    protected function normalizeLanguage(mixed $language): string
    {
        if (! is_string($language)) {
            return 'en';
        }
        $language = trim($language);
        return $language === '' ? 'en' : $language;
    }

    protected function extractYear(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }
        if (is_int($value)) {
            return $value;
        }
        $value = (string) $value;
        if (preg_match('/\d{4}/', $value, $m)) {
            return (int) $m[0];
        }
        return null;
    }
}
