<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GoogleBooksService
{
    protected function client(): PendingRequest
    {
        $verify = (bool) config('googlebooks.http_verify', true);

        return Http::withOptions([
            'verify' => $verify,
        ])->withHeaders([
            'User-Agent' => 'project_001/1.0',
        ]);
    }

    protected function requestVolumes(array $query): Response
    {
        try {
            return $this->client()->get('https://www.googleapis.com/books/v1/volumes', $query);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $msg = $e->getMessage();
            if (is_string($msg) && str_contains($msg, 'cURL error 60')) {
                // Windows-local fallback: retry once with SSL verify disabled
                return Http::withOptions(['verify' => false])
                    ->withHeaders(['User-Agent' => 'project_001/1.0'])
                    ->get('https://www.googleapis.com/books/v1/volumes', $query);
            }
            throw $e;
        }
    }

    public function fetchFirstPageExcludingRussian(string $query, int $maxResults = 40): Collection
    {
        $maxResults = max(1, min($maxResults, (int) config('googlebooks.max_results_per_request', 40)));

        $response = $this->requestVolumes([
            'q' => $query,
            'maxResults' => $maxResults,
            'startIndex' => 0,
            'printType' => 'books',
            'projection' => 'lite',
            'key' => config('googlebooks.api_key'),
        ]);

        if (! $response->successful()) {
            return collect();
        }

        $items = $response->json('items', []);

        return collect($items)
            ->filter(function (array $item) {
                $volumeInfo = $item['volumeInfo'] ?? [];

                return ! $this->isRussianAuthorOrLanguage($volumeInfo);
            })
            ->values()
            ->map(fn (array $item) => $this->mapVolumeToArray($item));
    }

    /**
     * Собрать партию до $batchSize книг (без русских авторов) с постраничным обходом.
     */
    public function fetchBatchExcludingRussian(string $query, int $batchSize = 500, int $startIndex = 0): Collection
    {
        $batchSize = max(1, min($batchSize, 1000));

        $maxPerPage = (int) config('googlebooks.max_results_per_request', 40);
        $maxPerPage = max(1, min($maxPerPage, 40));

        $collected = collect();
        $currentIndex = $startIndex;

        while ($collected->count() < $batchSize) {
            $response = $this->requestVolumes([
                'q' => $query,
                'maxResults' => $maxPerPage,
                'startIndex' => $currentIndex,
                'printType' => 'books',
                'projection' => 'lite',
                'key' => config('googlebooks.api_key'),
            ]);

            if (! $response->successful()) {
                break;
            }

            $data = $response->json();
            $items = $data['items'] ?? [];

            if (empty($items)) {
                break;
            }

            $filtered = collect($items)
                ->filter(function (array $item) {
                    $volumeInfo = $item['volumeInfo'] ?? [];

                    return ! $this->isRussianAuthorOrLanguage($volumeInfo);
                })
                ->values()
                ->map(fn (array $item) => $this->mapVolumeToArray($item));

            $collected = $collected->merge($filtered);

            $currentIndex += $maxPerPage;

            if (isset($data['totalItems']) && $currentIndex >= (int) $data['totalItems']) {
                break;
            }
        }

        return $collected->take($batchSize)->values();
    }

    public function countAllExcludingRussian(string $query, ?int $maxTotal = null): int
    {
        $maxPerPage = (int) config('googlebooks.max_results_per_request', 40);
        $maxPerPage = max(1, min($maxPerPage, 40));

        $startIndex = 0;
        $totalCount = 0;

        while (true) {
            if ($maxTotal !== null && $totalCount >= $maxTotal) {
                break;
            }

            $response = $this->requestVolumes([
                'q' => $query,
                'maxResults' => $maxPerPage,
                'startIndex' => $startIndex,
                'printType' => 'books',
                'projection' => 'lite',
                'key' => config('googlebooks.api_key'),
            ]);

            if (! $response->successful()) {
                break;
            }

            $data = $response->json();
            $items = $data['items'] ?? [];

            if (empty($items)) {
                break;
            }

            foreach ($items as $item) {
                if ($maxTotal !== null && $totalCount >= $maxTotal) {
                    break 2;
                }

                $volumeInfo = $item['volumeInfo'] ?? [];

                if ($this->isRussianAuthorOrLanguage($volumeInfo)) {
                    continue;
                }

                $totalCount++;
            }

            $startIndex += $maxPerPage;

            if (isset($data['totalItems']) && $startIndex >= (int) $data['totalItems']) {
                break;
            }
        }

        return $totalCount;
    }

    protected function isRussianAuthorOrLanguage(array $volumeInfo): bool
    {
        $language = $volumeInfo['language'] ?? null;
        if ($language === 'ru') {
            return true;
        }

        $authors = $volumeInfo['authors'] ?? [];
        foreach ($authors as $author) {
            if (! is_string($author)) {
                continue;
            }

            if (preg_match('/[А-Яа-яЁёІіЇїЄє]/u', $author)) {
                return true;
            }
        }

        return false;
    }

    protected function mapVolumeToArray(array $item): array
    {
        $volumeInfo = $item['volumeInfo'] ?? [];

        return [
            'id' => $item['id'] ?? null,
            'title' => $volumeInfo['title'] ?? null,
            'subtitle' => $volumeInfo['subtitle'] ?? null,
            'authors' => $volumeInfo['authors'] ?? [],
            'publisher' => $volumeInfo['publisher'] ?? null,
            'published_date' => $volumeInfo['publishedDate'] ?? null,
            'description' => $volumeInfo['description'] ?? null,
            'categories' => $volumeInfo['categories'] ?? [],
            'language' => $volumeInfo['language'] ?? null,
            'page_count' => $volumeInfo['pageCount'] ?? null,
            'average_rating' => $volumeInfo['averageRating'] ?? null,
            'ratings_count' => $volumeInfo['ratingsCount'] ?? null,
            'thumbnail' => $volumeInfo['imageLinks']['thumbnail'] ?? null,
            'info_link' => $volumeInfo['infoLink'] ?? null,
            'isbn' => $this->extractIsbn($volumeInfo),
        ];
    }

    protected function extractIsbn(array $volumeInfo): ?string
    {
        $identifiers = $volumeInfo['industryIdentifiers'] ?? [];

        foreach ($identifiers as $identifier) {
            if (($identifier['type'] ?? null) === 'ISBN_13' && ! empty($identifier['identifier'])) {
                return $identifier['identifier'];
            }
        }

        foreach ($identifiers as $identifier) {
            if (! empty($identifier['identifier'])) {
                return $identifier['identifier'];
            }
        }

        return null;
    }
}

