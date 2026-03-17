<?php

namespace App\Services\UkrBookstores\ProductPageParsers;

use DOMXPath;

class YakabooProductPageParser extends AbstractProductPageParser
{
    public function parseProductPage(string $html, string $productUrl): ?array
    {
        $dom = $this->createDom($html);
        if (! $dom) {
            return null;
        }
        $xpath = $this->xpath($dom);
        $baseUrl = $this->baseUrlFromProductUrl($productUrl);

        $rawTitle = $this->firstNodeText($xpath, '//*[@class="product-title"]') ?? $this->firstNodeText($xpath, '//h1') ?? '';
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
                $coverImage = $og['image'];
            }
        }
        $isbn = $this->extractIsbn($xpath, $html);
        $publisher = $this->firstNodeText($xpath, '//*[contains(@class,"publisher") or contains(text(),"Видавництво")]/following-sibling::*[1]') ?? $this->firstNodeText($xpath, '//*[@itemprop="publisher"]') ?? null;
        $year = $this->extractYear($xpath, $html);
        $categories = $this->extractCategories($xpath);

        return [
            'title' => $title,
            'authors' => $authors,
            'description' => $description,
            'cover_image' => $coverImage,
            'isbn' => $isbn,
            'publisher' => $publisher,
            'publication_year' => $year,
            'categories' => $categories,
        ];
    }

    private function extractAuthors(DOMXPath $xpath): array
    {
        $authors = [];
        $nodes = $xpath->query('//*[@itemprop="author"]//*[@itemprop="name"] | //*[contains(@class,"author")]//a | //*[contains(@class,"authors")]//a');
        if ($nodes) {
            foreach ($nodes as $node) {
                $name = $this->textContent($node);
                if (strlen($name) > 1 && strlen($name) < 200) {
                    $authors[] = $name;
                }
            }
        }
        if (empty($authors)) {
            $text = $this->firstNodeText($xpath, '//*[contains(@class,"author") or contains(@class,"authors")]');
            if ($text) {
                $authors = array_filter(array_map('trim', preg_split('/[,;]/u', $text)));
            }
        }
        return array_unique(array_slice($authors, 0, 10));
    }

    private function extractIsbn(DOMXPath $xpath, string $html): ?string
    {
        $text = $this->firstNodeText($xpath, '//*[@itemprop="isbn"]') ?? $this->firstNodeText($xpath, '//*[contains(text(),"ISBN")]/following-sibling::*[1]');
        if ($text) {
            if (preg_match('/\b(\d[\d\-]{9,}[\dXx])\b/', $text, $m)) {
                return preg_replace('/\D/', '', $m[1]);
            }
        }
        if (preg_match('/\bISBN[:\s]*(\d[\d\-]{9,}[\dXx])\b/i', $html, $m)) {
            return preg_replace('/\D/', '', $m[1]);
        }
        return null;
    }

    private function extractYear(DOMXPath $xpath, string $html): ?int
    {
        $text = $this->firstNodeText($xpath, '//*[@itemprop="datePublished"]') ?? $this->firstNodeText($xpath, '//*[contains(text(),"рік") or contains(text(),"год")]');
        if ($text && preg_match('/\b(19|20)\d{2}\b/', $text, $m)) {
            return (int) $m[0];
        }
        if (preg_match('/\b(19|20)\d{2}\b/', $html, $m)) {
            return (int) $m[0];
        }
        return null;
    }

    private function extractCategories(DOMXPath $xpath): array
    {
        $cats = [];
        $nodes = $xpath->query('//*[contains(@class,"category") or contains(@class,"genre") or contains(@class,"breadcrumb")]//a');
        if ($nodes) {
            foreach ($nodes as $node) {
                $name = $this->textContent($node);
                if (strlen($name) > 1 && strlen($name) < 150 && ! in_array($name, $cats, true)) {
                    $cats[] = $name;
                }
            }
        }
        return array_slice($cats, 0, 5);
    }
}
