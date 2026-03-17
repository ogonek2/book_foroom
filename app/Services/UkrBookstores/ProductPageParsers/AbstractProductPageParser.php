<?php

namespace App\Services\UkrBookstores\ProductPageParsers;

use DOMDocument;
use DOMNode;
use DOMXPath;

abstract class AbstractProductPageParser implements ProductPageParserInterface
{
    /** Фрази, що видаляються з опису (покупка, завантаження, ціна). */
    protected const PURCHASE_PATTERNS = [
        '/Купити[^.]*\./u',
        '/Завантажити[^.]*\./u',
        '/Миттєвий доступ[^.]*\.?/u',
        '/Світові бестселери[^.]*\.?/u',
        '/у книгарні\s+[А-Яа-яІіЇїЄє\s.]+/ui',
        '/в книгарні\s+[А-Яа-яІіЇїЄє\s.]+/ui',
        '/за\s+\d+\s*грн[^.]*\.?/ui',
        '/ціною\s+\d+[^.]*\.?/ui',
        '/у форматі\s+[A-Z]+[^.]*\.?/ui',
        '/форматі\s+EPUB[^.]*\.?/ui',
        '/Доставка[^.]*\./u',
        '/Оплата[^.]*\./u',
        '/Book\.ua[^.]*\.?/ui',
        '/Yakaboo[^.]*\.?/ui',
    ];

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
        return new DOMXPath($dom);
    }

    /** Не використовувати title сторінки — тільки заголовок книги з контенту, очищений від цін і магазинів. */
    protected function cleanBookTitle(string $raw): string
    {
        $t = $raw;
        if (preg_match('/["\']([^"\']{2,})["\']/u', $t, $m)) {
            $t = $m[1];
        } else {
            $t = preg_replace('/\s*[вi]\s+Book\.ua[^.]*$/ui', '', $t);
            $t = preg_replace('/\s*[вi]\s+Yakaboo[^.]*$/ui', '', $t);
            $t = preg_replace('/\s*[вi]\s+[А-Яа-яІіЇїЄє\s]+(книгарні|магазині)[^.]*$/ui', '', $t);
            $t = preg_replace('/\s*:\s*Купити\s+за\s+ціною\s+\d+\s*грн[^.]*$/ui', '', $t);
            $t = preg_replace('/\s*за\s+\d+\s*грн[^.]*$/ui', '', $t);
            $t = preg_replace('/\s*у\s+форматі\s+[A-Za-z]+[^.]*$/ui', '', $t);
            $t = preg_replace('/^\s*Електронна\s+книга\s+["\']?/ui', '', $t);
            $t = preg_replace('/\s*["\']\s*$/u', '', $t);
        }
        return trim(preg_replace('/\s+/', ' ', $t));
    }

    /** Видалити з тексту блоки про покупку, ціну, доставку. */
    protected function stripPurchaseText(?string $text): ?string
    {
        if ($text === null || $text === '') {
            return null;
        }
        $t = $text;
        foreach (self::PURCHASE_PATTERNS as $pattern) {
            $t = preg_replace($pattern, ' ', $t);
        }
        $t = trim(preg_replace('/\s+/', ' ', $t));
        return $t === '' ? null : $t;
    }

    /** Витягнути зміст meta og (не використовувати og:title для назви книги). */
    protected function getMetaOg(DOMXPath $xpath): array
    {
        $result = ['title' => null, 'description' => null, 'image' => null];
        $nodes = $xpath->query("//meta[starts-with(@property,'og:')]");
        if (! $nodes) {
            return $result;
        }
        foreach ($nodes as $node) {
            $prop = $node->getAttribute('property');
            $content = $node->getAttribute('content');
            if ($prop === 'og:title') {
                $result['title'] = trim($content);
            }
            if ($prop === 'og:description') {
                $result['description'] = trim($content);
            }
            if ($prop === 'og:image') {
                $result['image'] = trim($content);
            }
        }
        return $result;
    }

    /**
     * Зображення саме обкладинки книги: приоритет — контейнери book-cover/product-image,
     * виключити логотипи (посилання з logo/banner у шляху).
     */
    protected function extractBookCoverImage(DOMXPath $xpath, string $baseUrl = ''): ?string
    {
        $queries = [
            '//*[contains(@class,"book-cover") or contains(@class,"product-image") or contains(@class,"book-image") or contains(@class,"product-gallery")]//img[@src]',
            '//img[@itemprop="image"]',
            '//*[contains(@class,"product") or contains(@class,"book-detail")]//img[@src]',
        ];
        foreach ($queries as $query) {
            $nodes = $xpath->query($query);
            if (! $nodes) {
                continue;
            }
            foreach ($nodes as $node) {
                $src = $node->getAttribute('src');
                if ($src && ! $this->isLikelyLogoOrBanner($src)) {
                    return $this->resolveImageUrl($src, $baseUrl);
                }
            }
        }
        return null;
    }

    protected function isLikelyLogoOrBanner(string $src): bool
    {
        $lower = strtolower($src);
        if (preg_match('/logo|banner|icon|sprite|favicon|placeholder|no[-_]?image/i', $lower)) {
            return true;
        }
        return false;
    }

    protected function resolveImageUrl(string $src, string $baseUrl): string
    {
        $src = trim($src);
        if (preg_match('#^https?://#', $src)) {
            return $src;
        }
        $parsed = parse_url($baseUrl);
        $base = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '');
        return $base . (str_starts_with($src, '/') ? $src : '/' . $src);
    }

    protected function baseUrlFromProductUrl(string $productUrl): string
    {
        $parsed = parse_url($productUrl);
        return ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '');
    }

    /** Отримати опис з блоків, де немає ціни; потім вирізати тексти про покупку. */
    protected function extractDescriptionWithoutPriceBlocks(DOMXPath $xpath): ?string
    {
        $descNodes = $xpath->query('
            //*[@itemprop="description"]
            | //*[contains(@class,"description") or contains(@class,"annotation") or contains(@class,"synopsis") or contains(@class,"about-book")]
        ');
        if (! $descNodes) {
            return null;
        }
        foreach ($descNodes as $node) {
            $text = $this->textContent($node);
            if (strlen($text) < 30) {
                continue;
            }
            $cleaned = $this->stripPurchaseText($text);
            if ($cleaned && strlen($cleaned) > 20) {
                return $cleaned;
            }
        }
        return null;
    }

    protected function textContent(DOMNode $node): string
    {
        return trim(preg_replace('/\s+/', ' ', $node->textContent ?? ''));
    }

    protected function firstNodeText(DOMXPath $xpath, string $query): ?string
    {
        $nodes = $xpath->query($query);
        if (! $nodes || $nodes->length === 0) {
            return null;
        }
        $t = $this->textContent($nodes->item(0));
        return $t === '' ? null : $t;
    }
}
