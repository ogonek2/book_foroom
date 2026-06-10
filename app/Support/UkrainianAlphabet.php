<?php

namespace App\Support;

class UkrainianAlphabet
{
    /**
     * Повний український алфавіт для фільтра авторів.
     *
     * @return array<int, string>
     */
    public static function letters(): array
    {
        return [
            'А', 'Б', 'В', 'Г', 'Ґ', 'Д', 'Е', 'Є', 'Ж', 'З',
            'И', 'І', 'Ї', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П',
            'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ',
            'Ю', 'Я',
        ];
    }

    /**
     * Перша літера прізвища для фільтрації (пріоритет UA, потім основне).
     */
    public static function sortLetter(?string $lastNameUa, ?string $lastName): string
    {
        $source = trim((string) ($lastNameUa ?: $lastName ?: ''));
        if ($source === '') {
            return '';
        }

        return mb_strtoupper(mb_substr($source, 0, 1));
    }
}
