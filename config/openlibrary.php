<?php

return [
    'base_url' => env('OPENLIBRARY_BASE_URL', 'https://openlibrary.org'),
    'covers_url' => env('OPENLIBRARY_COVERS_URL', 'https://covers.openlibrary.org'),
    'search_limit_default' => (int) env('OPENLIBRARY_SEARCH_LIMIT', 20),
    'search_limit_max' => 100,

    /** Размер одной джобы импорта (чтобы не падать по таймауту/памяти). */
    'import_chunk_size' => (int) env('OPENLIBRARY_IMPORT_CHUNK_SIZE', 50),

    /** Список запросов для набора книг (перебираем по очереди до target). */
    'queries_for_fetch' => [
        'fiction',
        'history',
        'science',
        'romance',
        'fantasy',
        'biography',
    ],
];
