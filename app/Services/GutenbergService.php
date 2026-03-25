<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
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
        $results = $data['results'] ?? [];

        return [
            'ok' => true,
            'status' => 200,
            'items' => collect($results)->map(function (array $row) use ($expandAuthors, $expandLimit) {
                return $this->mapBookRaw($row, $expandAuthors, $expandLimit);
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
            'item' => $this->mapBookRaw($data, $expandAuthors, $expandLimit),
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

        return [
            'ok' => true,
            'status' => 200,
            'items' => $data,
        ];
    }

    public function getAuthor(int $authorId): array
    {
        $response = $this->requestWith429Retry("{$this->baseUrl}/authors/{$authorId}", []);

        if (! $response->successful()) {
            return [
                'ok' => false,
                'status' => $response->status(),
                'message' => $response->json('detail') ?? 'Gutenberg API request failed.',
                'raw' => $response->json(),
            ];
        }

        return [
            'ok' => true,
            'status' => 200,
            'item' => $this->unwrapSingleResult($response->json()),
        ];
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

