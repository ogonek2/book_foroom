<?php

namespace App\Http\Controllers;

use App\Services\GutenbergService;
use Illuminate\Http\Request;

class GutenbergController extends Controller
{
    public function __construct(
        protected GutenbergService $gutenberg,
    ) {
    }

    /**
     * Поиск книг в Project Gutenberg (через RapidAPI).
     * GET ?q=...&page_size=10&page=1
     */
    public function index(Request $request)
    {
        $query = (string) $request->get('q', '');
        $pageSize = (int) $request->get('page_size', config('gutenberg.search_page_size_default', 10));
        $page = max(1, (int) $request->get('page', 1));

        if ($query === '') {
            return response()->json([
                'message' => 'Параметр q (поисковый запрос) обязателен.',
                'count' => 0,
                'items' => [],
            ], 422);
        }

        $result = $this->gutenberg->search($query, $pageSize, $page);

        if (! ($result['ok'] ?? false)) {
            return response()->json([
                'message' => $result['message'] ?? 'Gutenberg API error.',
                'status' => $result['status'] ?? 500,
                'raw' => $result['raw'] ?? null,
            ], 502);
        }

        /** @var \Illuminate\Support\Collection $items */
        $items = $result['items'];

        return response()->json([
            'source' => 'gutenberg',
            'query' => $query,
            'count' => $items->count(),
            'page' => $result['page'] ?? $page,
            'page_size' => $result['page_size'] ?? $pageSize,
            'next' => $result['next'] ?? null,
            'previous' => $result['previous'] ?? null,
            'items' => $items,
        ]);
    }

    /**
     * Полная информация по книге (с подгрузкой авторов).
     * GET /gutenberg/books/{id}
     */
    public function book(int $id, Request $request)
    {
        $expand = $request->get('expand_authors');
        $expandAuthors = is_null($expand)
            ? null
            : filter_var($expand, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

        $result = $this->gutenberg->getBook($id, $expandAuthors);

        if (! ($result['ok'] ?? false)) {
            return response()->json([
                'message' => $result['message'] ?? 'Gutenberg API error.',
                'status' => $result['status'] ?? 500,
                'raw' => $result['raw'] ?? null,
            ], 502);
        }

        return response()->json([
            'source' => 'gutenberg',
            'item' => $result['item'] ?? null,
        ]);
    }

    /**
     * Получить очищенный текст книги.
     * GET /gutenberg/{id}/text?cleaning_mode=simple
     */
    public function text(int $id, Request $request)
    {
        $mode = $request->get('cleaning_mode');

        $result = $this->gutenberg->getText($id, is_string($mode) ? $mode : null);

        if (! ($result['ok'] ?? false)) {
            return response()->json([
                'message' => $result['message'] ?? 'Gutenberg API error.',
                'status' => $result['status'] ?? 500,
                'raw' => $result['raw'] ?? null,
            ], 502);
        }

        return response()->json([
            'source' => 'gutenberg',
            'item' => $result['item'] ?? null,
        ]);
    }

    /**
     * Список subjects.
     * GET /gutenberg/subjects
     */
    public function subjects()
    {
        $result = $this->gutenberg->subjects();

        if (! ($result['ok'] ?? false)) {
            return response()->json([
                'message' => $result['message'] ?? 'Gutenberg API error.',
                'status' => $result['status'] ?? 500,
                'raw' => $result['raw'] ?? null,
            ], 502);
        }

        return response()->json([
            'source' => 'gutenberg',
            'items' => $result['items'] ?? [],
        ]);
    }
}

