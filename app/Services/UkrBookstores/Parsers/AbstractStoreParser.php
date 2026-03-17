<?php

namespace App\Services\UkrBookstores\Parsers;

use DOMDocument;
use DOMXPath;

abstract class AbstractStoreParser
{
    /**
     * Парсить HTML сторінки пошуку і повертає масив книг у єдиному форматі.
     *
     * @return array<int, array{title: string, url: string, price: ?string, image_url: ?string, author: ?string, external_id: ?string}>
     */
    abstract public function parse(string $html, string $baseUrl): array;

    protected function createDom(string $html): ?DOMDocument
    {
        $dom = new DOMDocument;
        $prev = libxml_use_internal_errors(true);
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        return $dom;
    }

    protected function xpath(DOMDocument $dom): DOMXPath
    {
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('html', 'http://www.w3.org/1999/xhtml');

        return $xpath;
    }

    /**
     * Нормалізує URL (відносний -> абсолютний).
     */
    protected function resolveUrl(string $baseUrl, string $href): string
    {
        $href = trim($href);
        if ($href === '' || str_starts_with($href, '#')) {
            return $baseUrl;
        }
        if (preg_match('#^https?://#i', $href)) {
            return $href;
        }
        $base = rtrim(preg_replace('#/[^/]*$#', '', parse_url($baseUrl, PHP_URL_SCHEME) . '://' . parse_url($baseUrl, PHP_URL_HOST) . (parse_url($baseUrl, PHP_URL_PATH) ?? '')), '/');

        return $base . (str_starts_with($href, '/') ? $href : '/' . $href);
    }

    protected function normalizeItem(string $title, string $url, ?string $price = null, ?string $imageUrl = null, ?string $author = null, ?string $externalId = null): array
    {
        return [
            'title' => trim($title),
            'url' => $url,
            'price' => $price ? trim($price) : null,
            'image_url' => $imageUrl ? trim($imageUrl) : null,
            'author' => $author ? trim($author) : null,
            'external_id' => $externalId ? trim($externalId) : null,
        ];
    }
}
