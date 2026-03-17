<?php

namespace App\Services;

use App\Services\UkrBookstores\Parsers\AbstractStoreParser;
use App\Services\UkrBookstores\ProductPageParsers\ProductPageParserInterface;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class UkrBookstoresService
{
    /**
     * Список магазинів з конфігу (лише enabled).
     *
     * @return array<string, array{name: string, base_url: string, search_url: string, timeout: int, parser: class-string, enabled?: bool}>
     */
    public function getStores(): array
    {
        $stores = config('ukr_bookstores.stores', []);
        return array_filter($stores, fn (array $c) => ($c['enabled'] ?? true) === true);
    }

    /**
     * Паралельно відправити запити до всіх українських книгарень і розпарсити результати.
     * Повертає масив [ store_key => [ items ] ].
     *
     * @return array<string, array<int, array{title: string, url: string, price: ?string, image_url: ?string, author: ?string, external_id: ?string}>>
     */
    public function searchAll(string $query, ?array $storeKeys = null): array
    {
        $stores = $this->getStores();
        if ($storeKeys !== null) {
            $stores = array_intersect_key($stores, array_flip($storeKeys));
        }
        if (empty($stores)) {
            return [];
        }

        $encodedQuery = rawurlencode($query);
        $timeout = config('ukr_bookstores.timeout_default', 15);
        $userAgent = config('ukr_bookstores.user_agent', 'Mozilla/5.0 (compatible; BooksForoom/1.0)');

        $responses = Http::pool(fn (Pool $pool) => $this->addPoolRequests($pool, $stores, $encodedQuery, $timeout, $userAgent));

        $results = [];
        foreach ($stores as $key => $config) {
            $response = $responses[$key] ?? null;
            if ($response instanceof Throwable) {
                Log::debug("UkrBookstores: exception for store [{$key}]", ['message' => $response->getMessage()]);
                $results[$key] = [
                    'store_name' => $config['name'],
                    'items' => [],
                    'error' => $response->getMessage(),
                ];
                continue;
            }
            if (! $response instanceof Response || ! $response->successful()) {
                $status = $response instanceof Response ? $response->status() : null;
                Log::debug("UkrBookstores: no response or failed for store [{$key}]", ['status' => $status]);
                $results[$key] = [
                    'store_name' => $config['name'],
                    'items' => [],
                    'error' => $response instanceof Response ? "HTTP {$response->status()}" : 'no response',
                ];
                continue;
            }

            $parserClass = $config['parser'] ?? null;
            if (! $parserClass || ! is_subclass_of($parserClass, AbstractStoreParser::class)) {
                $results[$key] = [
                    'store_name' => $config['name'],
                    'items' => [],
                    'error' => 'parser not configured',
                ];
                continue;
            }

            try {
                /** @var AbstractStoreParser $parser */
                $parser = app($parserClass);
                $items = $parser->parse($response->body(), $config['base_url']);
                $results[$key] = [
                    'store_name' => $config['name'],
                    'items' => array_values($items),
                    'error' => null,
                ];
            } catch (\Throwable $e) {
                Log::warning("UkrBookstores: parse error for store [{$key}]", ['message' => $e->getMessage()]);
                $results[$key] = [
                    'store_name' => $config['name'],
                    'items' => [],
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Завантажити сторінку книги за URL і розпарсити дані для запису в books/authors/categories.
     * Повертає масив для імпорту (title, authors, description, cover_image, isbn, publisher, publication_year, categories, ukr_store_url) або null.
     */
    public function fetchBookDetailsFromProductUrl(string $storeKey, string $productUrl): ?array
    {
        $stores = config('ukr_bookstores.stores', []);
        $config = $stores[$storeKey] ?? null;
        if (! $config) {
            return null;
        }

        $productUrl = $this->normalizeProductUrl($productUrl, $config['base_url'] ?? '');
        if (! str_starts_with($productUrl, 'http://') && ! str_starts_with($productUrl, 'https://')) {
            return null;
        }

        $parserClass = $config['product_page_parser'] ?? null;
        if (! $parserClass || ! is_subclass_of($parserClass, ProductPageParserInterface::class)) {
            return null;
        }

        $timeout = $config['timeout'] ?? config('ukr_bookstores.timeout_default', 15);
        $userAgent = config('ukr_bookstores.user_agent', 'Mozilla/5.0 (compatible; BooksForoom/1.0)');

        $response = Http::timeout($timeout)
            ->withHeaders(['User-Agent' => $userAgent])
            ->get($productUrl);

        if (! $response->successful()) {
            return null;
        }

        /** @var ProductPageParserInterface $parser */
        $parser = app($parserClass);
        $data = $parser->parseProductPage($response->body(), $productUrl);
        if (! $data) {
            return null;
        }

        $data['ukr_store_url'] = $productUrl;
        return $data;
    }

    /** Визначити store_key за URL сторінки товару (для оновлення вже імпортованих книг). */
    public function getStoreKeyFromProductUrl(string $productUrl): ?string
    {
        $stores = config('ukr_bookstores.stores', []);
        $host = parse_url($productUrl, PHP_URL_HOST);
        if (! $host) {
            return null;
        }
        $host = strtolower($host);
        foreach ($stores as $key => $config) {
            $baseHost = strtolower(parse_url($config['base_url'] ?? '', PHP_URL_HOST));
            if ($baseHost === $host || str_ends_with($host, '.' . $baseHost)) {
                return $key;
            }
        }
        return null;
    }

    private function normalizeProductUrl(string $url, string $baseUrl): string
    {
        $url = trim($url);
        $baseUrl = rtrim($baseUrl, '/');
        if (str_starts_with($url, '//')) {
            return 'https:' . $url;
        }
        if (preg_match('#^https?://[^/]+#', $url)) {
            return $url;
        }
        if (str_starts_with($url, 'https:/') || str_starts_with($url, 'http:/')) {
            $path = preg_replace('#^https?:/#', '', $url);
            return $baseUrl . (str_starts_with($path, '/') ? $path : '/' . $path);
        }
        return $baseUrl . (str_starts_with($url, '/') ? $url : '/' . $url);
    }

    /**
     * @param array<string, array{name: string, base_url: string, search_url: string, timeout: int}> $stores
     */
    private function addPoolRequests(Pool $pool, array $stores, string $encodedQuery, int $timeout, string $userAgent): array
    {
        $out = [];
        foreach ($stores as $key => $config) {
            $url = str_replace('{query}', $encodedQuery, $config['search_url']);
            $t = $config['timeout'] ?? $timeout;
            $out[] = $pool->as($key)
                ->timeout($t)
                ->withHeaders(['User-Agent' => $userAgent])
                ->get($url);
        }

        return $out;
    }
}
