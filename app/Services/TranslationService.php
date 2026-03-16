<?php

namespace App\Services;

use Stichoza\GoogleTranslate\GoogleTranslate;

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
            $tr = new GoogleTranslate();
            $tr->setSource('en');
            $tr->setTarget('uk');

            $translated = $tr->translate($text);

            return $translated ?: $text;
        } catch (\Throwable $e) {
            return $text;
        }
    }
}

