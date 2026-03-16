<?php

namespace App\Http\Controllers;

use App\Services\OpenLibraryService;
use Illuminate\Http\Request;

class OpenLibraryController extends Controller
{
    public function __construct(
        protected OpenLibraryService $openLibrary,
    ) {
    }

    /**
     * Поиск книг в Open Library (открытая база, без ключа API).
     * GET ?q=...&limit=20&page=1
     */
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $limit = (int) $request->get('limit', config('openlibrary.search_limit_default', 20));
        $page = max(1, (int) $request->get('page', 1));

        if ($query === '') {
            return response()->json([
                'message' => 'Параметр q (поисковый запрос) обязателен.',
                'count' => 0,
                'total' => 0,
                'items' => [],
            ], 422);
        }

        $items = $this->openLibrary->search($query, $limit, $page);
        $total = $this->openLibrary->searchCount($query);

        return response()->json([
            'source' => 'open_library',
            'query' => $query,
            'count' => $items->count(),
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'items' => $items,
        ]);
    }

    /**
     * Получить книгу по ISBN.
     * GET /open-library/isbn/{isbn}
     */
    public function showByIsbn(string $isbn)
    {
        $book = $this->openLibrary->getByIsbn($isbn);

        if ($book === null) {
            return response()->json(['message' => 'Книга по данному ISBN не найдена.'], 404);
        }

        return response()->json([
            'source' => 'open_library',
            'item' => $book,
        ]);
    }

    /**
     * Получить работу (work) по ключу Open Library.
     * GET /open-library/work/{key}  например work/OL27448W
     */
    public function showWork(string $key)
    {
        $work = $this->openLibrary->getWork($key);

        if ($work === null) {
            return response()->json(['message' => 'Работа не найдена.'], 404);
        }

        return response()->json([
            'source' => 'open_library',
            'item' => $work,
        ]);
    }
}
