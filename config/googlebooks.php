<?php

return [
    'api_key' => env('GOOGLE_BOOKS_API_KEY'),

    'default_query' => env('GOOGLE_BOOKS_DEFAULT_QUERY', 'subject:books'),

    'max_results_per_request' => env('GOOGLE_BOOKS_MAX_RESULTS', 40),

    /**
     * Список запросов для набора 500 книг (перебираем по очереди, пока не наберём целевое количество).
     */
    /** Размер одной джобы импорта (чтобы не падать по таймауту/памяти). */
    'import_chunk_size' => (int) env('GOOGLE_BOOKS_IMPORT_CHUNK_SIZE', 50),

    'queries_for_500' => [
        'intitle:a',
        'intitle:e',
        'intitle:i',
        'intitle:o',
        'subject:fiction',
        'subject:history',
        'subject:science',
    ],
];

