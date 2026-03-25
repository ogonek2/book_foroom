<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Services\GutenbergService;
use App\Services\TranslationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportGutenbergToDb extends Command
{
    protected $signature = 'books:import-gutenberg
                            {--q=shakespeare : Поисковый запрос для Gutenberg}
                            {--target=100 : Сколько книг попытаться импортировать}
                            {--page_size=20 : Размер страницы (page_size) при поиске}
                            {--max-pages=20 : Максимум страниц (page) за один запуск}
                            {--translate=1 : Переводить EN->UK (1/0)}
                            {--enrich-authors=1 : Дотягивать детали/фото автора, даже если автор уже есть в БД}
                            {--categories-limit=8 : Максимум категорий на книгу}
                            {--authors-limit=5 : Максимум авторов на книгу}
                            {--retries=2 : Повторы при 429}
                            {--sleep_ms=1500 : Пауза между повторами при 429, мс}';

    protected $description = 'Импорт книг/авторов/категорий из Project Gutenberg (RapidAPI) в локальную БД с переводом на украинский';

    public function __construct(
        protected GutenbergService $gutenberg,
        protected TranslationService $translator,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        config([
            'gutenberg.retry_times' => max(0, (int) $this->option('retries')),
            'gutenberg.retry_sleep_ms' => max(0, (int) $this->option('sleep_ms')),
        ]);

        $query = (string) $this->option('q');
        $target = max(1, (int) $this->option('target'));
        $pageSize = max(1, min(100, (int) $this->option('page_size')));
        $maxPages = max(1, (int) $this->option('max-pages'));
        $doTranslate = filter_var($this->option('translate'), FILTER_VALIDATE_BOOL);
        $enrichAuthors = filter_var($this->option('enrich-authors'), FILTER_VALIDATE_BOOL);
        $categoriesLimit = max(0, (int) $this->option('categories-limit'));
        $authorsLimit = max(0, (int) $this->option('authors-limit'));

        $this->info("Импорт Gutenberg → БД");
        $this->line("q={$query}, target={$target}, page_size={$pageSize}, max_pages={$maxPages}");
        $this->line("translate=" . ($doTranslate ? 'yes' : 'no') . ", authors_limit={$authorsLimit}, categories_limit={$categoriesLimit}");

        $imported = 0;
        $skippedExisting = 0;
        $seenBookIds = [];

        /** @var array<int, array<string, mixed>> $authorCache */
        $authorCache = [];

        for ($page = 1; $page <= $maxPages; $page++) {
            if ($imported >= $target) {
                break;
            }

            // Нам для импорта не нужно authors_full (это много запросов), берём базовый список
            config(['gutenberg.expand_authors_default' => false]);

            $result = $this->gutenberg->search($query, $pageSize, $page);
            if (! ($result['ok'] ?? false)) {
                $this->warn("Ошибка Gutenberg на page={$page}: " . ($result['message'] ?? 'unknown'));
                $raw = $result['raw'] ?? null;
                if (is_array($raw) && isset($raw['message'])) {
                    $this->warn((string) $raw['message']);
                }
                break;
            }

            /** @var \Illuminate\Support\Collection<int, array<string, mixed>> $items */
            $items = $result['items'];
            if ($items->isEmpty()) {
                $this->warn("Пустая выдача на page={$page}, остановка.");
                break;
            }

            foreach ($items as $bookRow) {
                if ($imported >= $target) {
                    break;
                }

                $gutenbergId = (int) ($bookRow['id'] ?? 0);
                if ($gutenbergId <= 0) {
                    continue;
                }
                if (isset($seenBookIds[$gutenbergId])) {
                    continue;
                }
                $seenBookIds[$gutenbergId] = true;

                $existingBook = Book::where('gutenberg_book_id', $gutenbergId)->first();
                if ($existingBook) {
                    $skippedExisting++;
                    if ($enrichAuthors) {
                        $this->enrichAuthorsForExistingBook($existingBook, $bookRow, $authorCache, $doTranslate, $authorsLimit, $enrichAuthors);
                    }
                    continue;
                }

                $originalTitle = (string) ($bookRow['title'] ?? 'Без названия');
                $originalSummary = $bookRow['summary'] ?? null;
                $originalSummary = is_string($originalSummary) ? $originalSummary : null;

                $alternativeTitle = $bookRow['alternative_title'] ?? null;
                $alternativeTitle = is_string($alternativeTitle) ? trim($alternativeTitle) : null;

                $uaTitle = $doTranslate ? $this->translator->translateToUkrainian($originalTitle) : $originalTitle;
                $uaSummary = $doTranslate ? $this->translator->translateToUkrainian($originalSummary) : $originalSummary;

                // Защита от сверхдлинных заголовков (VARCHAR в MySQL)
                $originalTitleDb = Str::limit($originalTitle, 250, '…');
                $uaTitleDb = Str::limit((string) $uaTitle, 250, '…');

                $authors = $bookRow['authors'] ?? [];
                $authors = is_array($authors) ? $authors : [];
                $authors = array_slice($authors, 0, $authorsLimit > 0 ? $authorsLimit : 0);

                $authorIds = [];
                $primaryAuthorId = null;
                $authorStringForBook = 'Невідомий автор';

                foreach ($authors as $idx => $a) {
                    $authorApiId = null;
                    $authorName = null;
                    if (is_array($a)) {
                        $authorApiId = isset($a['id']) ? (int) $a['id'] : null;
                        $authorName = isset($a['name']) ? (string) $a['name'] : null;
                    } elseif (is_string($a)) {
                        $authorName = $a;
                    }

                    if ($idx === 0 && is_string($authorName) && trim($authorName) !== '') {
                        $authorStringForBook = $authorName;
                    }

                    $authorModel = $this->upsertAuthorFromGutenberg($authorApiId, $authorName, $authorCache, $doTranslate, $enrichAuthors);
                    if ($authorModel) {
                        $authorIds[] = $authorModel->id;
                        if ($primaryAuthorId === null) {
                            $primaryAuthorId = $authorModel->id;
                        }
                    }
                }

                $book = Book::create([
                    'gutenberg_book_id' => $gutenbergId,
                    'title' => $originalTitleDb,
                    'book_name_ua' => $uaTitleDb,
                    'annotation' => $uaSummary,
                    'annotation_source' => $doTranslate ? 'gutenberg_translated_uk' : 'gutenberg_original',
                    'gutenberg_summary_en' => $originalSummary,
                    'gutenberg_download_count' => is_numeric($bookRow['download_count'] ?? null) ? (int) $bookRow['download_count'] : null,
                    'gutenberg_issued_at' => $this->toTimestamp($bookRow['issued'] ?? null),
                    'gutenberg_reading_ease_score' => is_numeric($bookRow['reading_ease_score'] ?? null) ? (float) $bookRow['reading_ease_score'] : null,
                    'gutenberg_formats' => is_array($bookRow['formats'] ?? null) ? $bookRow['formats'] : null,
                    'gutenberg_media_type' => is_string($bookRow['media_type'] ?? null) ? $bookRow['media_type'] : null,
                    'author' => $authorStringForBook,
                    'author_id' => $primaryAuthorId,
                    'isbn' => null,
                    'publication_year' => $this->extractYear($bookRow['issued'] ?? null),
                    'first_publish_year' => $this->extractYear($bookRow['issued'] ?? null),
                    'publisher' => null,
                    'cover_image' => $bookRow['cover_image'] ?? null,
                    'language' => 'en',
                    'original_language' => 'en',
                    'pages' => null,
                    'rating' => 0,
                    'reviews_count' => 0,
                    'interesting_facts' => null,
                    'synonyms' => array_values(array_unique(array_filter([
                        $originalTitleDb,
                        $uaTitleDb !== $originalTitleDb ? $uaTitleDb : null,
                        $alternativeTitle,
                    ], fn ($v) => is_string($v) && trim($v) !== ''))),
                    'series' => null,
                    'series_number' => null,
                ]);

                if (! empty($authorIds)) {
                    $book->authors()->syncWithoutDetaching(array_values(array_unique($authorIds)));
                }

                $categoryNames = $this->collectCategoryNames($bookRow, $categoriesLimit);
                $categoryIds = [];
                foreach ($categoryNames as $categoryName) {
                    $nameUa = $doTranslate ? $this->translator->translateToUkrainian($categoryName) : $categoryName;
                    $slug = Str::slug($nameUa ?: $categoryName);
                    if ($slug === '') {
                        continue;
                    }
                    $category = Category::firstOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $nameUa ?: $categoryName,
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
                    $book->categories()->syncWithoutDetaching(array_values(array_unique($categoryIds)));
                }

                $imported++;
            }

            $this->line("page={$page}: imported={$imported}, skipped_existing={$skippedExisting}");
        }

        $this->info("Готово. Импортировано: {$imported}. Пропущено (уже есть): {$skippedExisting}.");

        return self::SUCCESS;
    }

    /**
     * @param array<int, array<string, mixed>> $authorCache
     */
    protected function enrichAuthorsForExistingBook(Book $book, array $bookRow, array &$authorCache, bool $translate, int $authorsLimit, bool $enrichAuthors): void
    {
        $authors = $bookRow['authors'] ?? [];
        $authors = is_array($authors) ? $authors : [];
        $authors = array_slice($authors, 0, $authorsLimit > 0 ? $authorsLimit : 0);

        foreach ($authors as $a) {
            $authorApiId = null;
            $authorName = null;
            if (is_array($a)) {
                $authorApiId = isset($a['id']) ? (int) $a['id'] : null;
                $authorName = isset($a['name']) ? (string) $a['name'] : null;
            } elseif (is_string($a)) {
                $authorName = $a;
            }
            $this->upsertAuthorFromGutenberg($authorApiId, $authorName, $authorCache, $translate, $enrichAuthors);
        }
    }

    /**
     * @param array<int, array<string, mixed>> $authorCache
     */
    protected function upsertAuthorFromGutenberg(?int $authorApiId, ?string $authorName, array &$authorCache, bool $translate, bool $enrichAuthors = true): ?Author
    {
        $authorName = is_string($authorName) ? trim($authorName) : null;

        // Если есть API id, пытаемся получить детали автора (кэшируем на время команды)
        $details = null;
        if ($authorApiId && $authorApiId > 0) {
            if (! array_key_exists($authorApiId, $authorCache)) {
                $res = $this->gutenberg->getAuthor($authorApiId);
                $authorCache[$authorApiId] = ($res['ok'] ?? false) ? ($res['item'] ?? []) : [];
            }
            $details = $authorCache[$authorApiId] ?: null;
        }

        $nameForParse = $authorName;
        if (! $nameForParse && is_array($details) && isset($details['name']) && is_string($details['name'])) {
            $nameForParse = $details['name'];
        }
        if (! $nameForParse) {
            return null;
        }

        [$firstName, $lastName] = $this->parseAuthorName($nameForParse);
        $intendedSlug = Str::slug(trim($firstName . ' ' . $lastName));

        $birthYear = is_array($details) ? ($details['birth_year'] ?? null) : null;
        $deathYear = is_array($details) ? ($details['death_year'] ?? null) : null;
        $webpage = is_array($details) ? ($details['webpage'] ?? null) : null;
        $aliases = is_array($details) ? ($details['aliases'] ?? null) : null;
        $aliases = is_array($aliases) ? $aliases : [];

        $firstNameUa = $translate ? $this->translator->translateToUkrainian($firstName) : null;
        $lastNameUa = $translate ? $this->translator->translateToUkrainian($lastName) : null;

        $author = null;
        if ($authorApiId && $authorApiId > 0) {
            $author = Author::where('gutenberg_author_id', $authorApiId)->first();
        }
        if (! $author && $intendedSlug !== '') {
            $author = Author::where('slug', $intendedSlug)->first();
        }
        if (! $author) {
            $author = Author::where('first_name', $firstName)->where('last_name', $lastName)->first();
        }

        if (! $author) {
            $author = new Author();
        }

        if ($authorApiId && $authorApiId > 0 && $author->gutenberg_author_id === null) {
            $author->gutenberg_author_id = $authorApiId;
        }

        $author->first_name = $author->first_name ?: $firstName;
        $author->last_name = $author->last_name ?: $lastName;
        $author->first_name_eng = $author->first_name_eng ?: $firstName;
        $author->last_name_eng = $author->last_name_eng ?: $lastName;
        if ($translate) {
            $author->first_name_ua = $author->first_name_ua ?: $firstNameUa;
            $author->last_name_ua = $author->last_name_ua ?: $lastNameUa;
        }

        if ($author->birth_date === null) {
            $author->birth_date = $this->yearToDate($birthYear);
        }
        if ($author->death_date === null) {
            $author->death_date = $this->yearToDate($deathYear);
        }
        if (is_string($webpage) && trim($webpage) !== '') {
            if ($author->website === null || ($enrichAuthors && trim((string) $author->website) === '')) {
                $author->website = $webpage;
            }
            if ($author->web_page === null || ($enrichAuthors && trim((string) $author->web_page) === '')) {
                $author->web_page = $webpage;
            }
        }

        // Алиасы → synonyms/pseudonym
        $aliasesClean = [];
        foreach ($aliases as $a) {
            if (! is_string($a)) {
                continue;
            }
            $a = trim($a);
            if ($a === '') {
                continue;
            }
            $aliasesClean[] = $a;
        }
        $aliasesClean = array_values(array_unique($aliasesClean));

        if (($author->synonyms === null || $author->synonyms === [] || $author->synonyms === '') && ! empty($aliasesClean)) {
            $author->synonyms = $aliasesClean;
        }
        if (($author->pseudonym === null || trim((string) $author->pseudonym) === '') && ! empty($aliasesClean)) {
            $author->pseudonym = $aliasesClean[0];
        }

        // Wikipedia: фото + биография (summary). Gutenberg API обычно не отдаёт фото/био.
        if (is_string($webpage) && str_contains($webpage, 'wikipedia.org/wiki/')) {
            $wiki = $this->fetchWikipediaSummaryData($webpage);

            if (($author->photo === null || ($enrichAuthors && trim((string) $author->photo) === '')) && ($wiki['photo'] ?? null)) {
                $author->photo = Str::limit((string) $wiki['photo'], 2000, '');
            }

            if (($author->biography === null || ($enrichAuthors && trim((string) $author->biography) === '')) && ($wiki['extract'] ?? null)) {
                $bio = (string) $wiki['extract'];
                $author->biography = $translate ? $this->translator->translateToUkrainian($bio) : $bio;
            }
        }

        // slug выставит модель сама при creating, но если мы нашли по slug — он уже есть
        $author->save();

        return $author;
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function parseAuthorName(string $name): array
    {
        $name = trim($name);
        if (str_contains($name, ',')) {
            [$last, $first] = array_map('trim', explode(',', $name, 2));
            $first = $first !== '' ? $first : $last;
            $last = $last !== '' ? $last : '';
            return [$first, $last];
        }

        $parts = preg_split('/\s+/u', $name) ?: [];
        $first = $parts[0] ?? $name;
        $last = $parts[1] ?? ($parts[count($parts) - 1] ?? '');
        return [$first, $last];
    }

    /**
     * @return array<int, string>
     */
    protected function collectCategoryNames(array $bookRow, int $limit): array
    {
        if ($limit === 0) {
            return [];
        }

        $subjects = $bookRow['subjects'] ?? [];
        $subjects = is_array($subjects) ? $subjects : [];

        $bookshelves = $bookRow['bookshelves'] ?? [];
        $bookshelves = is_array($bookshelves) ? $bookshelves : [];

        $names = [];
        foreach (array_merge($bookshelves, $subjects) as $n) {
            if (! is_string($n)) {
                continue;
            }
            $n = trim($n);
            if ($n === '') {
                continue;
            }
            $names[] = $n;
            if (count($names) >= $limit) {
                break;
            }
        }

        return array_values(array_unique($names));
    }

    protected function extractYear(mixed $issued): ?int
    {
        if ($issued === null) {
            return null;
        }
        if (is_string($issued) && preg_match('/^\d{4}/', $issued, $m)) {
            return (int) $m[0];
        }
        return null;
    }

    protected function yearToDate(mixed $year): ?string
    {
        $y = is_numeric($year) ? (int) $year : null;
        if (! $y || $y < 1) {
            return null;
        }
        return sprintf('%04d-01-01', $y);
    }

    protected function toTimestamp(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }
        // Обычно приходит ISO 8601
        try {
            return (new \DateTimeImmutable($value))->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array{photo?: string|null, extract?: string|null}
     */
    protected function fetchWikipediaSummaryData(string $webpage): array
    {
        try {
            $path = parse_url($webpage, PHP_URL_PATH);
            if (! is_string($path) || ! str_contains($path, '/wiki/')) {
                return [];
            }
            $title = substr($path, strpos($path, '/wiki/') + 6);
            $title = urldecode($title);
            if ($title === '') {
                return [];
            }

            $url = 'https://en.wikipedia.org/api/rest_v1/page/summary/' . rawurlencode($title);
            $resp = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->withHeaders(['User-Agent' => 'project_001/1.0'])
                ->get($url);
            if (! $resp->successful()) {
                return [];
            }
            $json = $resp->json();
            if (! is_array($json)) {
                return [];
            }

            $extract = $json['extract'] ?? null;
            $extract = is_string($extract) && trim($extract) !== '' ? $extract : null;

            $photo = null;
            $thumb = $json['thumbnail']['source'] ?? null;
            if (is_string($thumb) && str_starts_with($thumb, 'http')) {
                $photo = $thumb;
            } else {
                $orig = $json['originalimage']['source'] ?? null;
                if (is_string($orig) && str_starts_with($orig, 'http')) {
                    $photo = $orig;
                }
            }

            return [
                'photo' => $photo,
                'extract' => $extract,
            ];
        } catch (\Throwable) {
            return [];
        }
    }
}

