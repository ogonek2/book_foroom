<?php

namespace App\Http\Controllers;

use App\Jobs\ImportGoogleBooksBatch;
use App\Services\GoogleBooksService;
use Illuminate\Http\Request;

class GoogleBooksController extends Controller
{
    public function __construct(
        protected GoogleBooksService $googleBooks,
    ) {
    }

    /**
     * Примерный endpoint для получения книг из Google Books API
     * (все книги, кроме книг русских авторов).
     *
     * В будущем этот контроллер можно использовать как точку
     * передачи данных другим контроллерам/слоям.
     */
    public function index(Request $request)
    {
        $query = $request->get('q', config('googlebooks.default_query', 'subject:books'));
        $limit = (int) $request->get('limit', 40);

        $books = $this->googleBooks->fetchFirstPageExcludingRussian($query, $limit);

        return response()->json([
            'query' => $query,
            'count' => $books->count(),
            'items' => $books,
        ]);
    }

    /**
     * Получить партию книг (по умолчанию 500), отправить их в очередь
     * на импорт в локальную БД (таблицы books и book_category).
     *
     * Этот endpoint можно дергать вручную или по крону через HTTP.
     */
    public function ingestBatch(Request $request)
    {
        $query = $request->get('q', config('googlebooks.default_query', 'subject:books'));
        $batchSize = (int) $request->get('batch', 500);
        $startIndex = (int) $request->get('start', 0);

        $batchSize = $batchSize > 0 ? $batchSize : 500;

        $volumes = $this->googleBooks
            ->fetchBatchExcludingRussian($query, $batchSize, $startIndex)
            ->toArray();

        if (! empty($volumes)) {
            ImportGoogleBooksBatch::dispatch($volumes);
        }

        return response()->json([
            'query' => $query,
            'requested_batch' => $batchSize,
            'start_index' => $startIndex,
            'enqueued' => count($volumes),
        ]);
    }
}

