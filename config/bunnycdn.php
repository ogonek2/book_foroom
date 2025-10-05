<?php

return [
    /*
    |--------------------------------------------------------------------------
    | BunnyCDN Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for BunnyCDN Storage and CDN integration
    |
    */

    'storage' => [
        'name' => env('BUNNY_STORAGE_NAME'),
        'password' => env('BUNNY_STORAGE_PASSWORD'),
    ],

    'cdn' => [
        'url' => env('BUNNY_CDN_URL'),
    ],

    'ssl_verify' => env('CDN_SSL_VERIFY', false),
    'timeout' => env('CDN_TIMEOUT', 30),
];
