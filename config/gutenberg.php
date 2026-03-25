<?php

return [
    /**
     * RapidAPI endpoint base URL.
     * Docs: https://gutenbergapi.com/ (вариант API)
     * RapidAPI listing: Project Gutenberg - Free Books API
     */
    'base_url' => env('GUTENBERG_BASE_URL', 'https://project-gutenberg-free-books-api1.p.rapidapi.com'),

    /** RapidAPI headers */
    'rapidapi_key' => env('GUTENBERG_RAPIDAPI_KEY'),
    'rapidapi_host' => env('GUTENBERG_RAPIDAPI_HOST', 'project-gutenberg-free-books-api1.p.rapidapi.com'),

    /** Request tuning */
    'timeout' => (int) env('GUTENBERG_TIMEOUT', 15),
    'http_verify' => filter_var(env('GUTENBERG_HTTP_VERIFY', true), FILTER_VALIDATE_BOOL),
    'retry_times' => (int) env('GUTENBERG_RETRY_TIMES', 2),
    'retry_sleep_ms' => (int) env('GUTENBERG_RETRY_SLEEP_MS', 1500),
    'expand_authors_default' => filter_var(env('GUTENBERG_EXPAND_AUTHORS', true), FILTER_VALIDATE_BOOL),
    'expand_authors_limit' => (int) env('GUTENBERG_EXPAND_AUTHORS_LIMIT', 5),
    'translate_api_responses' => filter_var(env('GUTENBERG_TRANSLATE_API_RESPONSES', true), FILTER_VALIDATE_BOOL),
    'translate_mode' => env('GUTENBERG_TRANSLATE_MODE', 'replace'), // replace|overlay
    'search_page_size_default' => (int) env('GUTENBERG_SEARCH_PAGE_SIZE', 10),
    'search_page_size_max' => 100,

    /** Cache to avoid re-fetching the same pages/authors repeatedly */
    'cache_enabled' => filter_var(env('GUTENBERG_CACHE_ENABLED', true), FILTER_VALIDATE_BOOL),
    'cache_ttl_minutes' => (int) env('GUTENBERG_CACHE_TTL_MINUTES', 1440), // 24h

    /** Text cleaning mode for /text endpoint */
    'text_cleaning_mode_default' => env('GUTENBERG_TEXT_CLEANING_MODE', 'simple'),
];

