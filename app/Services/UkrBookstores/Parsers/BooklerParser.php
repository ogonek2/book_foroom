<?php

namespace App\Services\UkrBookstores\Parsers;

use DOMXPath;

class BooklerParser extends AbstractStoreParser
{
    public function parse(string $html, string $baseUrl): array
    {
        $dom = $this->createDom($html);
        if (! $dom) {
            return [];
        }
        $xpath = $this->xpath($dom);
        $items = [];

        $nodes = $xpath->query("//a[contains(@href,'/book/') or contains(@href,'/product/') or contains(@href,'catalog/') or contains(@href,'books/')]");
        $seen = [];
        foreach ($nodes as $node) {
            $href = $node->getAttribute('href');
            if (empty($href) || isset($seen[$href])) {
                continue;
            }
            $title = trim(preg_replace('/\s+/', ' ', $node->textContent ?? ''));
            if (strlen($title) < 2 || strlen($title) > 500) {
                continue;
            }
            $url = $this->resolveUrl($baseUrl, $href);
            $seen[$href] = true;
            $items[] = $this->normalizeItem($title, $url, null, null, null, null);
            if (count($items) >= 30) {
                break;
            }
        }

        return $items;
    }
}
