<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GutenbergService
{
    protected string $baseUrl;
    /** @var array<int, string> */
    protected array $booksIndexPaths = ['/api/books', '/books'];

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('gutenberg.base_url'), '/');
    }

    protected function client(): PendingRequest
    {
        $key = (string) config('gutenberg.rapidapi_key');
        $host = (string) config('gutenberg.rapidapi_host');
        $timeout = (int) config('gutenberg.timeout', 15);
        $verify = (bool) config('gutenberg.http_verify', true);

        return Http::timeout($timeout)->withOptions([
            'verify' => $verify,
        ])->withHeaders([
            'X-RapidAPI-Key' => $key,
            'X-RapidAPI-Host' => $host,
        ]);
    }

    /**
     * Поиск книг.
     * GET /api/books?q=...&page_size=10&page=1
     */
    public function search(string $query, int $pageSize = 10, int $page = 1): array
    {
        $pageSize = max(1, min(
            $pageSize,
            (int) config('gutenberg.search_page_size_max', 100)
        ));
        $page = max(1, $page);

        $expandAuthors = (bool) config('gutenberg.expand_authors_default', true);
        $expandLimit = max(0, (int) config('gutenberg.expand_authors_limit', 5));

        $cacheEnabled = (bool) config('gutenberg.cache_enabled', true);
        $ttlMinutes = max(0, (int) config('gutenberg.cache_ttl_minutes', 1440));
        $cacheKey = 'gutenberg:search:' . md5(json_encode([
            'q' => $query,
            'page_size' => $pageSize,
            'page' => $page,
        ], JSON_UNESCAPED_UNICODE));

        $data = null;
        if ($cacheEnabled && $ttlMinutes > 0) {
            $data = Cache::remember($cacheKey, now()->addMinutes($ttlMinutes), function () use ($query, $pageSize, $page) {
                $response = null;
                foreach ($this->booksIndexPaths as $path) {
                    $response = $this->requestWith429Retry(
                        "{$this->baseUrl}{$path}",
                        [
                            'q' => $query,
                            'page_size' => $pageSize,
                            'page' => $page,
                        ],
                    );

                    if ($response->successful()) {
                        break;
                    }

                    if (! $this->looksLikeMissingApiRoute($response->json())) {
                        break;
                    }
                }

                if ($response === null || ! $response->successful()) {
                    return null;
                }

                $json = $response->json();
                return is_array($json) ? $json : null;
            });
        }

        if ($data === null) {
            $response = null;
            foreach ($this->booksIndexPaths as $path) {
                $response = $this->requestWith429Retry(
                    "{$this->baseUrl}{$path}",
                    [
                        'q' => $query,
                        'page_size' => $pageSize,
                        'page' => $page,
                    ],
                );

                if ($response->successful()) {
                    break;
                }

                if (! $this->looksLikeMissingApiRoute($response->json())) {
                    break;
                }
            }

            if ($response === null || ! $response->successful()) {
                return [
                    'ok' => false,
                    'status' => $response?->status() ?? 500,
                    'message' => $response?->json('detail') ?? 'Gutenberg API request failed.',
                    'raw' => $response?->json(),
                    'items' => collect(),
                    'next' => null,
                    'previous' => null,
                    'page' => $page,
                    'page_size' => $pageSize,
                ];
            }

            $data = $response->json() ?? [];
        }

        if (! is_array($data)) {
            return [
                'ok' => false,
                'status' => 500,
                'message' => 'Gutenberg API request failed.',
                'raw' => $data,
                'items' => collect(),
                'next' => null,
                'previous' => null,
                'page' => $page,
                'page_size' => $pageSize,
            ];
        }

        $results = $data['results'] ?? [];

        return [
            'ok' => true,
            'status' => 200,
            'items' => collect($results)->map(function (array $row) use ($expandAuthors, $expandLimit) {
                $mapped = $this->mapBookRaw($row, $expandAuthors, $expandLimit);
                return $this->localizeGutenbergPayload($mapped);
            })->values(),
            'next' => $data['next'] ?? null,
            'previous' => $data['previous'] ?? null,
            'page' => $page,
            'page_size' => $pageSize,
            'raw' => $data,
        ];
    }

    /**
     * Получить полную информацию о книге по ID.
     * GET /books/{id}
     */
    public function getBook(int $bookId, ?bool $expandAuthors = null): array
    {
        $expandAuthors = $expandAuthors ?? (bool) config('gutenberg.expand_authors_default', true);
        $expandLimit = max(0, (int) config('gutenberg.expand_authors_limit', 5));

        $response = null;
        foreach ($this->booksIndexPaths as $path) {
            $url = "{$this->baseUrl}{$path}/{$bookId}";
            $response = $this->requestWith429Retry($url, []);

            if ($response->successful()) {
                break;
            }

            if (! $this->looksLikeMissingApiRoute($response->json())) {
                break;
            }
        }

        if ($response === null || ! $response->successful()) {
            return [
                'ok' => false,
                'status' => $response?->status() ?? 500,
                'message' => $response?->json('detail') ?? 'Gutenberg API request failed.',
                'raw' => $response?->json(),
            ];
        }

        $data = $response->json();
        if (! is_array($data)) {
            return [
                'ok' => false,
                'status' => 502,
                'message' => 'Unexpected response format.',
                'raw' => $data,
            ];
        }

        return [
            'ok' => true,
            'status' => 200,
            'item' => $this->localizeGutenbergPayload($this->mapBookRaw($data, $expandAuthors, $expandLimit)),
        ];
    }

    /**
     * Получить "очищенный" текст книги.
     * GET /api/books/{id}/text?cleaning_mode=simple
     */
    public function getText(int $bookId, ?string $cleaningMode = null): array
    {
        $cleaningMode = $cleaningMode ?: (string) config('gutenberg.text_cleaning_mode_default', 'simple');

        $response = null;
        foreach ($this->booksIndexPaths as $path) {
            $url = "{$this->baseUrl}{$path}/{$bookId}/text";
            $response = $this->requestWith429Retry($url, [
                'cleaning_mode' => $cleaningMode,
            ]);

            if ($response->successful()) {
                break;
            }

            if (! $this->looksLikeMissingApiRoute($response->json())) {
                break;
            }
        }

        if ($response === null || ! $response->successful()) {
            return [
                'ok' => false,
                'status' => $response?->status() ?? 500,
                'message' => $response?->json('detail') ?? 'Gutenberg API request failed.',
                'raw' => $response?->json(),
            ];
        }

        return [
            'ok' => true,
            'status' => 200,
            'item' => $response->json(),
        ];
    }

    /**
     * Справочник subjects (как в примере RapidAPI).
     * GET /subjects
     */
    public function subjects(): array
    {
        $response = $this->requestWith429Retry("{$this->baseUrl}/subjects", []);

        if (! $response->successful()) {
            return [
                'ok' => false,
                'status' => $response->status(),
                'message' => $response->json('detail') ?? 'Gutenberg API request failed.',
                'raw' => $response->json(),
                'items' => [],
            ];
        }

        $data = $response->json();
        $data = $this->localizeGutenbergPayload($data);

        return [
            'ok' => true,
            'status' => 200,
            'items' => $data,
        ];
    }

    public function getAuthor(int $authorId): array
    {
        $cacheEnabled = (bool) config('gutenberg.cache_enabled', true);
        $ttlMinutes = max(0, (int) config('gutenberg.cache_ttl_minutes', 1440));
        $cacheKey = "gutenberg:author:{$authorId}";

        $json = null;
        if ($cacheEnabled && $ttlMinutes > 0) {
            $json = Cache::remember($cacheKey, now()->addMinutes($ttlMinutes), function () use ($authorId) {
                $response = $this->requestWith429Retry("{$this->baseUrl}/authors/{$authorId}", []);
                if (! $response->successful()) {
                    return null;
                }
                $j = $response->json();
                return is_array($j) ? $j : null;
            });
        }

        $response = null;
        if ($json === null) {
            $response = $this->requestWith429Retry("{$this->baseUrl}/authors/{$authorId}", []);
            $json = $response->json();
        }

        if (! is_array($json) || ($response !== null && ! $response->successful())) {
            return [
                'ok' => false,
                'status' => $response?->status() ?? 500,
                'message' => is_array($json) ? ($json['detail'] ?? 'Gutenberg API request failed.') : 'Gutenberg API request failed.',
                'raw' => $json,
            ];
        }

        return [
            'ok' => true,
            'status' => 200,
            'item' => $this->localizeGutenbergPayload($this->unwrapSingleResult($json)),
        ];
    }

    /**
     * Переводит текстовые поля ответа Gutenberg API на украинский.
     * Делает это максимально безопасно: переводит только строки, URL не трогает.
     */
    protected function localizeGutenbergPayload(mixed $payload): mixed
    {
        if (! (bool) config('gutenberg.translate_api_responses', true)) {
            return $payload;
        }

        /** @var TranslationService $tr */
        $tr = app(TranslationService::class);

        if (! is_array($payload)) {
            return $payload;
        }

        $mode = (string) config('gutenberg.translate_mode', 'replace'); // replace|overlay
        $overlay = $mode === 'overlay';

        // Book-like
        foreach (['title', 'alternative_title', 'summary', 'name'] as $key) {
            if (! isset($payload[$key]) || ! is_string($payload[$key]) || $payload[$key] === '') {
                continue;
            }

            $translated = $tr->translateToUkrainian($payload[$key]);
            if (! is_string($translated) || $translated === '') {
                continue;
            }

            if ($overlay) {
                $payload[$key . '_uk'] = $translated;
            } else {
                $payload[$key] = $translated;
                unset($payload[$key . '_uk']);
            }
        }

        // subjects/bookshelves arrays (strings)
        foreach (['subjects', 'bookshelves'] as $key) {
            if (isset($payload[$key]) && is_array($payload[$key])) {
                $uk = [];
                foreach ($payload[$key] as $s) {
                    if (is_string($s) && $s !== '') {
                        $uk[] = $tr->translateToUkrainian($s);
                    }
                }
                if (! empty($uk)) {
                    if ($overlay) {
                        $payload[$key . '_uk'] = $uk;
                    } else {
                        $payload[$key] = $uk;
                        unset($payload[$key . '_uk']);
                    }
                }
            }
        }

        // authors array
        if (isset($payload['authors']) && is_array($payload['authors'])) {
            $authorsUk = [];
            foreach ($payload['authors'] as $a) {
                if (is_array($a)) {
                    $a2 = $a;
                    if (isset($a2['name']) && is_string($a2['name']) && $a2['name'] !== '') {
                        $translated = $tr->translateToUkrainian($a2['name']);
                        if (is_string($translated) && $translated !== '') {
                            if ($overlay) {
                                $a2['name_uk'] = $translated;
                            } else {
                                $a2['name'] = $translated;
                                unset($a2['name_uk']);
                            }
                        }
                    }
                    $authorsUk[] = $a2;
                } else {
                    $authorsUk[] = $a;
                }
            }
            $payload['authors'] = $authorsUk;
        }

        // authors_full (details)
        if (isset($payload['authors_full']) && is_array($payload['authors_full'])) {
            $af = [];
            foreach ($payload['authors_full'] as $author) {
                $af[] = $this->localizeGutenbergPayload($author);
            }
            $payload['authors_full'] = $af;
        }

        // lists with results (subjects/authors endpoints)
        if (isset($payload['results']) && is_array($payload['results'])) {
            $results = [];
            foreach ($payload['results'] as $row) {
                $results[] = $this->localizeGutenbergPayload($row);
            }
            $payload['results'] = $results;
        }

        // author books list
        if (isset($payload['books']) && is_array($payload['books'])) {
            $books = [];
            foreach ($payload['books'] as $b) {
                $books[] = $this->localizeGutenbergPayload($b);
            }
            $payload['books'] = $books;
        }

        return $payload;
    }

    protected function mapBookRaw(array $row, bool $expandAuthors, int $expandLimit): array
    {
        $mapped = $row;

        if (! $expandAuthors) {
            return $mapped;
        }

        $authors = $row['authors'] ?? [];
        if (! is_array($authors) || $expandLimit === 0) {
            return $mapped;
        }

        $authorsFull = [];
        $count = 0;
        foreach ($authors as $a) {
            if ($count >= $expandLimit) {
                break;
            }

            $id = null;
            if (is_array($a) && isset($a['id'])) {
                $id = (int) $a['id'];
            } elseif (is_numeric($a)) {
                $id = (int) $a;
            }

            if (! $id || $id <= 0) {
                continue;
            }

            $authorResult = $this->getAuthor($id);
            if (($authorResult['ok'] ?? false) && isset($authorResult['item'])) {
                $authorsFull[] = $authorResult['item'];
            }
            $count++;
        }

        $mapped['authors_full'] = $authorsFull;

        return $mapped;
    }

    protected function unwrapSingleResult(mixed $json): mixed
    {
        if (! is_array($json)) {
            return $json;
        }
        $results = $json['results'] ?? null;
        if (is_array($results) && count($results) === 1) {
            return $results[0];
        }
        return $json;
    }

    protected function looksLikeMissingApiRoute(mixed $json): bool
    {
        if (! is_array($json)) {
            return false;
        }
        $message = $json['message'] ?? null;
        if (! is_string($message)) {
            return false;
        }
        $m = mb_strtolower($message);
        return str_contains($m, "api doesn't exists")
            || str_contains($m, "endpoint") && str_contains($m, "does not exist");
    }

    protected function requestWith429Retry(string $url, array $query): Response
    {
        $retries = max(0, (int) config('gutenberg.retry_times', 2));
        $sleepMs = max(0, (int) config('gutenberg.retry_sleep_ms', 1500));

        $attempt = 0;
        do {
            $attempt++;
            $response = $this->client()->get($url, $query);

            if ($response->status() !== 429) {
                return $response;
            }

            if ($attempt > $retries) {
                return $response;
            }

            if ($sleepMs > 0) {
                usleep($sleepMs * 1000);
            }
        } while (true);
    }
}

