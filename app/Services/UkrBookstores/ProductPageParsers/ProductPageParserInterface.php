<?php

namespace App\Services\UkrBookstores\ProductPageParsers;

/**
 * Парсер сторінки товару (книги) — повертає дані для запису в books/authors/categories.
 */
interface ProductPageParserInterface
{
    /**
     * Розпарсити HTML сторінки книги.
     *
     * @return array{title: string, authors: array<string>, description: ?string, cover_image: ?string, isbn: ?string, publisher: ?string, publication_year: ?int, categories: array<string>}|null null якщо не вдалося розпарсити
     */
    public function parseProductPage(string $html, string $productUrl): ?array;
}
