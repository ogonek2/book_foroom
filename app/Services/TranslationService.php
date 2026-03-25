<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TranslationService
{
    /**
     * Перевод текста на украинский с сохранением HTML (насколько это возможно).
     * Если ключа или доступа нет — возвращает исходный текст.
     */
    public function translateToUkrainian(?string $text): ?string
    {
        if ($text === null || trim($text) === '') {
            return $text;
        }

        try {
            $cacheKey = 'tr:uk:' . md5($text);
            $cached = Cache::get($cacheKey);
            if (is_string($cached) && $cached !== '') {
                return $cached;
            }

            // Google "gtx" endpoint (без ключа). Для Windows окружений часто нужен verify=false.
            // Важно: длинные тексты режем на куски, чтобы не упираться в лимиты/URL.
            $chunks = $this->chunkText($text, 1500);
            $out = [];

            foreach ($chunks as $chunk) {
                $translated = $this->translateChunk($chunk, 'en', 'uk');
                $out[] = $translated ?: $chunk;
                // небольшая пауза, чтобы не ловить rate-limit
                usleep(150_000);
            }

            $joined = implode('', $out);
            $final = trim($joined) !== '' ? $joined : $text;
            if (is_string($final) && trim($final) !== '') {
                Cache::put($cacheKey, $final, now()->addDays(30));
            }
            return $final;
        } catch (\Throwable $e) {
            return $text;
        }
    }

    protected function translateChunk(string $text, string $source, string $target): ?string
    {
        $resp = Http::timeout(15)
            ->withOptions(['verify' => false])
            ->withHeaders(['User-Agent' => 'project_001/1.0'])
            ->get('https://translate.googleapis.com/translate_a/single', [
                'client' => 'gtx',
                'sl' => $source,
                'tl' => $target,
                'dt' => 't',
                'q' => $text,
            ]);

        if (! $resp->successful()) {
            return null;
        }

        $json = $resp->json();
        if (! is_array($json) || ! isset($json[0]) || ! is_array($json[0])) {
            return null;
        }

        $translated = '';
        foreach ($json[0] as $part) {
            if (is_array($part) && isset($part[0]) && is_string($part[0])) {
                $translated .= $part[0];
            }
        }

        return $translated !== '' ? $translated : null;
    }

    /**
     * @return array<int, string>
     */
    protected function chunkText(string $text, int $maxLen): array
    {
        $text = (string) $text;
        if ($maxLen < 200) {
            $maxLen = 200;
        }

        // Простое разбиение по абзацам/предложениям, сохраняя разделители.
        $parts = preg_split('/(\n{2,}|\n)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (! is_array($parts) || $parts === []) {
            return [$text];
        }

        $chunks = [];
        $buf = '';
        foreach ($parts as $p) {
            if (! is_string($p)) {
                continue;
            }
            if (mb_strlen($buf . $p) > $maxLen && $buf !== '') {
                $chunks[] = $buf;
                $buf = '';
            }
            $buf .= $p;
        }
        if ($buf !== '') {
            $chunks[] = $buf;
        }

        return $chunks ?: [$text];
    }
}

