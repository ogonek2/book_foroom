<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class OpenLibraryService
{
    protected string $baseUrl;
    protected string $coversUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('openlibrary.base_url', 'https://openlibrary.org'), '/');
        $this->coversUrl = rtrim(config('openlibrary.covers_url', 'https://covers.openlibrary.org'), '/');
    }

    /**
     * Поиск книг по запросу (заголовок, автор, ключевые слова).
     * Возвращает унифицированный формат для использования в приложении.
     */
    public function search(string $query, int $limit = 20, int $page = 1): Collection
    {
        $limit = max(1, min($limit, (int) config('openlibrary.search_limit_max', 100)));

        $response = Http::get("{$this->baseUrl}/search.json", [
            'q' => $query,
            'limit' => $limit,
            'page' => $page,
        ]);

        if (! $response->successful()) {
            return collect();
        }

        $data = $response->json();
        $docs = $data['docs'] ?? [];

        return collect($docs)->map(fn (array $doc) => $this->mapDocToBook($doc))->values();
    }

    /**
     * Получить общее количество результатов по запросу.
     */
    public function searchCount(string $query): int
    {
        $response = Http::get("{$this->baseUrl}/search.json", [
            'q' => $query,
            'limit' => 0,
        ]);

        if (! $response->successful()) {
            return 0;
        }

        return (int) ($response->json('num_found') ?? 0);
    }

    /**
     * Набрать партию книг по запросу (постранично), пока не наберём $totalLimit или не закончатся результаты.
     */
    public function fetchBatch(string $query, int $totalLimit = 500, int $startPage = 1): Collection
    {
        $limit = min(100, (int) config('openlibrary.search_limit_max', 100));
        $collected = collect();
        $page = $startPage;

        while ($collected->count() < $totalLimit) {
            $items = $this->search($query, $limit, $page);
            if ($items->isEmpty()) {
                break;
            }
            foreach ($items as $item) {
                $collected->push($item);
                if ($collected->count() >= $totalLimit) {
                    break 2;
                }
            }
            $page++;
        }

        return $collected->take($totalLimit)->values();
    }

    /**
     * Получить книгу по ISBN (одно издание).
     */
    public function getByIsbn(string $isbn): ?array
    {
        $isbn = preg_replace('/\D/', '', $isbn);
        if ($isbn === '') {
            return null;
        }

        $response = Http::get("{$this->baseUrl}/isbn/{$isbn}.json");

        if (! $response->successful()) {
            return null;
        }

        $edition = $response->json();
        $workKey = $edition['works'][0]['key'] ?? null;

        $authors = [];
        if (! empty($edition['authors'])) {
            foreach (array_slice($edition['authors'], 0, 5) as $a) {
                $key = $a['key'] ?? null;
                if ($key) {
                    $author = $this->fetchAuthorName($key);
                    if ($author) {
                        $authors[] = $author;
                    }
                }
            }
        }

        $description = $edition['description']['value'] ?? $edition['description'] ?? null;
        if (is_array($description)) {
            $description = $description['value'] ?? null;
        }

        $coverId = $edition['covers'][0] ?? null;
        $coverUrl = $coverId
            ? "{$this->coversUrl}/b/id/{$coverId}-L.jpg"
            : null;

        return [
            'id' => $edition['key'] ?? null,
            'source' => 'open_library',
            'title' => $edition['title'] ?? null,
            'authors' => $authors,
            'first_publish_year' => $edition['publish_date'] ?? null,
            'description' => $description,
            'isbn' => $isbn,
            'cover_image' => $coverUrl,
            'language' => isset($edition['languages'][0]['key']) ? substr($edition['languages'][0]['key'], -2) : null,
            'publisher' => is_array($edition['publishers'][0] ?? null) ? ($edition['publishers'][0]['name'] ?? null) : ($edition['publishers'][0] ?? null),
            'page_count' => $edition['number_of_pages'] ?? null,
            'work_key' => $workKey,
            'info_link' => $workKey ? "{$this->baseUrl}{$workKey}" : null,
        ];
    }

    /**
     * Получить данные по работе (work) по ключу типа /works/OL12345W.
     */
    public function getWork(string $workKey): ?array
    {
        $workKey = ltrim($workKey, '/');
        if (! str_starts_with($workKey, 'works/')) {
            $workKey = 'works/' . $workKey;
        }

        $response = Http::get("{$this->baseUrl}/{$workKey}.json");

        if (! $response->successful()) {
            return null;
        }

        $work = $response->json();
        $description = $work['description']['value'] ?? $work['description'] ?? null;
        if (is_array($description)) {
            $description = $description['value'] ?? null;
        }

        $authors = [];
        foreach ($work['authors'] ?? [] as $a) {
            $key = $a['author']['key'] ?? $a['key'] ?? null;
            if ($key) {
                $name = $this->fetchAuthorName($key);
                if ($name) {
                    $authors[] = $name;
                }
            }
        }

        $coverId = $work['covers'][0] ?? null;
        $coverUrl = $coverId
            ? "{$this->coversUrl}/b/id/{$coverId}-L.jpg"
            : null;

        return [
            'id' => $work['key'] ?? null,
            'source' => 'open_library',
            'title' => $work['title'] ?? null,
            'authors' => $authors,
            'first_publish_year' => $work['first_publish_date'] ?? null,
            'description' => $description,
            'isbn' => null,
            'cover_image' => $coverUrl,
            'language' => null,
            'work_key' => $work['key'] ?? null,
            'info_link' => isset($work['key']) ? "{$this->baseUrl}{$work['key']}" : null,
        ];
    }

    protected function mapDocToBook(array $doc): array
    {
        $key = $doc['key'] ?? '';
        $coverId = $doc['cover_i'] ?? null;
        $coverUrl = $coverId
            ? "{$this->coversUrl}/b/id/{$coverId}-L.jpg"
            : null;

        $isbns = $doc['isbn'] ?? [];
        $isbn = is_array($isbns) ? ($isbns[0] ?? null) : $isbns;

        $year = $doc['first_publish_year'] ?? null;
        $langs = $doc['language'] ?? [];
        $lang = is_array($langs) ? (isset($langs[0]) ? substr((string) $langs[0], -2) : null) : null;

        $subjects = $doc['subject'] ?? $doc['subject_key'] ?? [];
        $categories = is_array($subjects) ? array_slice($subjects, 0, 5) : [];

        return [
            'id' => $key,
            'source' => 'open_library',
            'title' => $doc['title'] ?? null,
            'authors' => $doc['author_name'] ?? [],
            'first_publish_year' => $year,
            'description' => null,
            'isbn' => $isbn,
            'cover_image' => $coverUrl,
            'language' => $lang,
            'work_key' => $key,
            'edition_count' => $doc['edition_count'] ?? null,
            'info_link' => $key ? $this->baseUrl . (str_starts_with((string) $key, '/') ? $key : '/works/' . $key) : null,
            'categories' => $categories,
        ];
    }

    protected function fetchAuthorName(string $authorKey): ?string
    {
        $authorKey = ltrim($authorKey, '/');
        $response = Http::get("{$this->baseUrl}/authors/{$authorKey}.json");
        if (! $response->successful()) {
            return null;
        }
        $data = $response->json();
        $name = $data['personal_name'] ?? $data['name'] ?? null;
        if ($name) {
            return $name;
        }
        if (! empty($data['fuller_name'])) {
            return $data['fuller_name'];
        }
        return trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
    }
}
