<?php

namespace App\Services\UkrBookstores\Parsers;

use DOMDocument;
use DOMXPath;

class YakabooParser extends AbstractStoreParser
{
    public function parse(string $html, string $baseUrl): array
    {
        $dom = $this->createDom($html);
        if (! $dom) {
            return [];
        }
        $xpath = $this->xpath($dom);
        $items = [];

        // Yakaboo: пошукові результати часто в контейнерах з класом продукту / посиланнями на книгу
        $nodes = $xpath->query("//a[contains(@href,'/ua/') and contains(@href,'.html')]");
        if ($nodes === false || $nodes->length === 0) {
            $nodes = $xpath->query("//*[contains(@class,'product') or contains(@class,'book')]//a[contains(@href,'.html')]");
        }
        $seen = [];
        foreach ($nodes as $node) {
            $href = $node->getAttribute('href');
            if (empty($href) || isset($seen[$href])) {
                continue;
            }
            $title = $this->textContent($node);
            if (strlen($title) < 2) {
                continue;
            }
            $url = $this->resolveUrl($baseUrl, $href);
            $seen[$href] = true;
            $externalId = $this->extractIdFromSlug($href);
            $items[] = $this->normalizeItem($title, $url, null, null, null, $externalId);
            if (count($items) >= 30) {
                break;
            }
        }

        return $items;
    }

    private function textContent(\DOMNode $node): string
    {
        return trim(preg_replace('/\s+/', ' ', $node->textContent ?? ''));
    }

    private function extractIdFromSlug(string $href): ?string
    {
        if (preg_match('/-(\d+)\.html$/', $href, $m)) {
            return $m[1];
        }

        return null;
    }
}
