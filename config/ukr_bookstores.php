<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Українські книгарні для парсингу
    |--------------------------------------------------------------------------
    | Кожен магазин: key (унікальний), name, search_url ({query} замінюється на пошуковий запит),
    | timeout_sec, optional user_agent.
    */
    'stores' => [
        'yakaboo' => [
            'name' => 'Yakaboo',
            'base_url' => 'https://www.yakaboo.ua',
            'search_url' => 'https://www.yakaboo.ua/ua/search/?q={query}',
            'timeout' => 15,
            'enabled' => true,
            'parser' => \App\Services\UkrBookstores\Parsers\YakabooParser::class,
            'product_page_parser' => \App\Services\UkrBookstores\ProductPageParsers\YakabooProductPageParser::class,
        ],
        'bookclub' => [
            'name' => 'Bookclub (КСД)',
            'base_url' => 'https://bookclub.ua',
            'search_url' => 'https://bookclub.ua/catalog/books/?q={query}',
            'timeout' => 15,
            'enabled' => true,
            'parser' => \App\Services\UkrBookstores\Parsers\BookclubParser::class,
            'product_page_parser' => \App\Services\UkrBookstores\ProductPageParsers\GenericProductPageParser::class,
        ],
        'elibri' => [
            'name' => 'Elibri',
            'base_url' => 'https://elibri.com.ua',
            'search_url' => 'https://elibri.com.ua/search?q={query}',
            'timeout' => 15,
            'enabled' => false, // B2B-платформа, публічного пошуку може не бути
            'parser' => \App\Services\UkrBookstores\Parsers\ElibriParser::class,
        ],
        'nashformat' => [
            'name' => 'Наш Формат',
            'base_url' => 'https://nashformat.ua',
            'search_url' => 'https://nashformat.ua/search/?query={query}&type=all',
            'timeout' => 15,
            'enabled' => true,
            'parser' => \App\Services\UkrBookstores\Parsers\NashFormatParser::class,
            'product_page_parser' => \App\Services\UkrBookstores\ProductPageParsers\GenericProductPageParser::class,
        ],
        'bookye' => [
            'name' => 'Книгарня Є (Book Ye)',
            'base_url' => 'https://book-ye.com.ua',
            'search_url' => 'https://book-ye.com.ua/search?q={query}',
            'timeout' => 15,
            'enabled' => false, // 404 — пошук може бути через JS або інший URL
            'parser' => \App\Services\UkrBookstores\Parsers\YeParser::class,
        ],
        'bookua' => [
            'name' => 'Book.ua',
            'base_url' => 'https://book.ua',
            'search_url' => 'https://book.ua/books?search={query}',
            'timeout' => 15,
            'enabled' => true,
            'parser' => \App\Services\UkrBookstores\Parsers\BooklerParser::class,
            'product_page_parser' => \App\Services\UkrBookstores\ProductPageParsers\GenericProductPageParser::class,
        ],
    ],

    'timeout_default' => 15,
    'user_agent' => 'Mozilla/5.0 (compatible; BooksForoom/1.0; +https://github.com/books-foroom)',

    /**
     * Запити для команди books:fetch-ukr-bookstores (якщо не передано --query).
     */
    'default_query' => env('UKR_BOOKSTORES_DEFAULT_QUERY', 'книга'),
    'queries_for_fetch' => [
        'книга',
        'роман',
        'поезія',
    ],

    /** Розмір однієї джоби при імпорті в books (кількість книг за один ImportUkrBookstoresToBooks). */
    'import_to_books_chunk_size' => (int) env('UKR_BOOKSTORES_IMPORT_CHUNK_SIZE', 20),

    /** Затримка (секунди) між запитами до сторінки товару при імпорті в books. */
    'fetch_delay_seconds' => (float) env('UKR_BOOKSTORES_FETCH_DELAY', 1),
];
