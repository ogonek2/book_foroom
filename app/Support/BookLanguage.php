<?php

namespace App\Support;

class BookLanguage
{
    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            'uk' => 'Українська',
            'en' => 'English',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'es' => 'Español',
            'it' => 'Italiano',
            'pl' => 'Polski',
            'ru' => 'Російська',
            'ja' => '日本語',
            'zh' => '中文',
        ];
    }

    public static function mapImportValue(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = mb_strtolower(trim($value));

        $languageMap = [
            'uk' => 'uk',
            'ua' => 'uk',
            'українська' => 'uk',
            'украинский' => 'uk',
            'ukrainian' => 'uk',
            'en' => 'en',
            'англійська' => 'en',
            'английский' => 'en',
            'english' => 'en',
            'de' => 'de',
            'німецька' => 'de',
            'немецкий' => 'de',
            'german' => 'de',
            'fr' => 'fr',
            'французька' => 'fr',
            'французский' => 'fr',
            'french' => 'fr',
            'es' => 'es',
            'іспанська' => 'es',
            'испанский' => 'es',
            'spanish' => 'es',
            'it' => 'it',
            'італійська' => 'it',
            'pl' => 'pl',
            'польська' => 'pl',
            'ru' => 'ru',
            'російська' => 'ru',
            'русский' => 'ru',
            'russian' => 'ru',
            'ja' => 'ja',
            'zh' => 'zh',
        ];

        if (isset($languageMap[$value])) {
            return $languageMap[$value];
        }

        return strlen($value) <= 5 ? $value : null;
    }
}
