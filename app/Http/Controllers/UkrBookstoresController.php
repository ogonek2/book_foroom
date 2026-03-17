<?php

namespace App\Http\Controllers;

use App\Models\UkrBookstoreListing;
use App\Services\UkrBookstoresService;
use Illuminate\Http\Request;

class UkrBookstoresController extends Controller
{
    public function __construct(
        protected UkrBookstoresService $ukrBookstores,
    ) {
    }

    /**
     * Пошук книг у всіх українських книгарнях паралельно.
     * GET /api/ukr-bookstores?q=...
     */
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $stores = $request->get('stores'); // optional: comma-separated list of store keys

        if ($query === '') {
            return response()->json([
                'message' => 'Параметр q (пошуковий запит) обов\'язковий.',
                'stores' => array_keys($this->ukrBookstores->getStores()),
            ], 422);
        }

        $storeKeys = null;
        if (is_string($stores) && $stores !== '') {
            $storeKeys = array_map('trim', explode(',', $stores));
            $storeKeys = array_filter($storeKeys);
        }

        $results = $this->ukrBookstores->searchAll($query, $storeKeys ?: null);

        $totalItems = 0;
        foreach ($results as $data) {
            $totalItems += count($data['items'] ?? []);
        }

        return response()->json([
            'query' => $query,
            'stores_count' => count($results),
            'total_items' => $totalItems,
            'results' => $results,
        ]);
    }

    /**
     * Зберегти результати пошуку в БД (таблиця ukr_bookstore_listings).
     * POST /api/ukr-bookstores/save
     * Body: { "query": "...", "results": { ... } } або спочатку викликати index, потім передати results сюди.
     */
    public function save(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:500',
            'results' => 'required|array',
        ]);

        $query = $request->input('query');
        $results = $request->input('results');
        $saved = 0;

        foreach ($results as $storeKey => $data) {
            if (! is_array($data) || ! isset($data['items'])) {
                continue;
            }
            $storeKey = is_string($storeKey) ? $storeKey : (string) $storeKey;
            foreach ($data['items'] as $item) {
                if (empty($item['title']) || empty($item['url'])) {
                    continue;
                }
                $externalId = $item['external_id'] ?? null;
                $existing = $externalId
                    ? UkrBookstoreListing::where('store_key', $storeKey)->where('external_id', $externalId)->first()
                    : UkrBookstoreListing::where('store_key', $storeKey)->where('url', $item['url'])->first();

                if ($existing) {
                    $existing->update([
                        'title' => $item['title'],
                        'url' => $item['url'],
                        'price' => $item['price'] ?? null,
                        'image_url' => $item['image_url'] ?? null,
                        'author' => $item['author'] ?? null,
                        'search_query' => $query,
                    ]);
                } else {
                    UkrBookstoreListing::create([
                        'store_key' => $storeKey,
                        'external_id' => $externalId,
                        'title' => $item['title'],
                        'url' => $item['url'],
                        'price' => $item['price'] ?? null,
                        'image_url' => $item['image_url'] ?? null,
                        'author' => $item['author'] ?? null,
                        'search_query' => $query,
                    ]);
                }
                $saved++;
            }
        }

        return response()->json([
            'query' => $query,
            'saved' => $saved,
        ]);
    }

    /**
     * Пошук + збереження за один виклик.
     * POST /api/ukr-bookstores/search-and-save?q=...
     */
    public function searchAndSave(Request $request)
    {
        $query = $request->get('q', '');
        if ($query === '') {
            return response()->json(['message' => 'Параметр q обов\'язковий.'], 422);
        }

        $results = $this->ukrBookstores->searchAll($query);

        $saved = 0;
        foreach ($results as $storeKey => $data) {
            if (! is_array($data) || ! isset($data['items'])) {
                continue;
            }
            foreach ($data['items'] as $item) {
                if (empty($item['title']) || empty($item['url'])) {
                    continue;
                }
                $externalId = $item['external_id'] ?? null;
                $existing = $externalId
                    ? UkrBookstoreListing::where('store_key', $storeKey)->where('external_id', $externalId)->first()
                    : UkrBookstoreListing::where('store_key', $storeKey)->where('url', $item['url'])->first();

                if ($existing) {
                    $existing->update([
                        'title' => $item['title'],
                        'url' => $item['url'],
                        'price' => $item['price'] ?? null,
                        'image_url' => $item['image_url'] ?? null,
                        'author' => $item['author'] ?? null,
                        'search_query' => $query,
                    ]);
                } else {
                    UkrBookstoreListing::create([
                        'store_key' => $storeKey,
                        'external_id' => $externalId,
                        'title' => $item['title'],
                        'url' => $item['url'],
                        'price' => $item['price'] ?? null,
                        'image_url' => $item['image_url'] ?? null,
                        'author' => $item['author'] ?? null,
                        'search_query' => $query,
                    ]);
                }
                $saved++;
            }
        }

        return response()->json([
            'query' => $query,
            'stores_count' => count($results),
            'saved' => $saved,
            'results' => $results,
        ]);
    }

    /**
     * Список магазинів з конфігу.
     */
    public function stores()
    {
        return response()->json([
            'stores' => $this->ukrBookstores->getStores(),
        ]);
    }
}
