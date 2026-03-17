<?php

namespace App\Services\UkrBookstores\ProductPageParsers;

use DOMXPath;

/**
 * Універсальний парсер: og:meta + h1, опис, автор з типових селекторів.
 */
class GenericProductPageParser extends AbstractProductPageParser
{
    public function parseProductPage(string $html, string $productUrl): ?array
    {
        $dom = $this->createDom($html);
        if (! $dom) {
            return null;
        }
        $xpath = $this->xpath($dom);
        $baseUrl = $this->baseUrlFromProductUrl($productUrl);

        $rawTitle = $this->firstNodeText($xpath, '//*[@itemprop="name"]') ?? $this->firstNodeText($xpath, '//h1') ?? '';
        $title = $this->cleanBookTitle($rawTitle);
        if ($title === '') {
            return null;
        }

        $authors = $this->extractAuthors($xpath);
        $description = $this->extractDescriptionWithoutPriceBlocks($xpath);
        if ($description === null) {
            $ogDesc = $this->getMetaOg($xpath)['description'] ?? null;
            $description = $this->stripPurchaseText($ogDesc);
        }
        $coverImage = $this->extractBookCoverImage($xpath, $baseUrl);
        if ($coverImage === null) {
            $og = $this->getMetaOg($xpath);
            if (! empty($og['image']) && ! $this->isLikelyLogoOrBanner($og['image'])) {
                $coverImage = trim($og['image']);
            }
        }
        $isbn = $this->firstNodeText($xpath, '//*[@itemprop="isbn"]');
        if ($isbn && ! preg_match('/^\d[\d\-Xx]+$/', $isbn)) {
            preg_match('/\d[\d\-]{9,}[\dXx]?/', $isbn, $m);
            $isbn = $m[0] ?? null;
        }
        $publisher = $this->firstNodeText($xpath, '//*[@itemprop="publisher"]');
        $year = null;
        $yearText = $this->firstNodeText($xpath, '//*[@itemprop="datePublished"]');
        if ($yearText && preg_match('/\b(19|20)\d{2}\b/', $yearText, $m)) {
            $year = (int) $m[0];
        }
        $categories = $this->extractCategories($xpath);

        return [
            'title' => $title,
            'authors' => $authors,
            'description' => $description,
            'cover_image' => $coverImage,
            'isbn' => $isbn ? preg_replace('/\D/', '', $isbn) : null,
            'publisher' => $publisher,
            'publication_year' => $year,
            'categories' => $categories,
        ];
    }

    private function extractAuthors(DOMXPath $xpath): array
    {
        $authors = [];
        foreach ($xpath->query('//*[@itemprop="author"]') as $node) {
            $name = $this->textContent($node);
            if (strlen($name) > 1 && strlen($name) < 200) {
                $authors[] = $name;
            }
        }
        if (empty($authors)) {
            $text = $this->firstNodeText($xpath, '//*[contains(@class,"author")]');
            if ($text) {
                $authors = array_filter(array_map('trim', preg_split('/[,;]/u', $text)));
            }
        }
        return array_unique(array_slice($authors, 0, 10));
    }

    private function extractCategories(DOMXPath $xpath): array
    {
        $cats = [];
        foreach ($xpath->query('//*[@itemprop="genre"] | //*[contains(@class,"category")]//a | //*[contains(@class,"breadcrumb")]//a') as $node) {
            $name = $this->textContent($node);
            if (strlen($name) > 1 && strlen($name) < 150) {
                $cats[] = $name;
            }
        }
        return array_unique(array_slice($cats, 0, 5));
    }
}
