<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BooksTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * @return array
     */
    public function array(): array
    {
        // Возвращаем пустой массив, так как это шаблон
        return [];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'nazvanie',             // Название (основное, ОБЯЗАТЕЛЬНО)
            'book_name_ua',         // Название украинською
            'slag',                 // Слаг (автогенерация при пустом)
            'annotation',           // Анотація
            'annotation_source',    // Джерело анотації
            'avtor_staryy',         // Автор (старый) (опционально)
            'avtor',                // Автор (создается автоматически)
            'isbn',                 // ISBN (опционально)
            'god_izdaniya',         // Год издания (опционально)
            'first_publish_year',   // Рік першого видання
            'izdatelstvo',          // Издательство (опционально)
            'oblozhka',             // Обложка (опционально)
            'yazyk',                // Язык (по умолчанию: ru)
            'original_language',    // Мова оригіналу
            'synonyms',             // Синоніми (через кому)
            'series',               // Серія
            'stranitsy',            // Страницы (опционально)
            'reiting',              // Рейтинг (опционально)
            'kolichestvo_recenziy', // Количество рецензий (опционально)
            'kategoriya',           // Категории (через кому, создаются автоматически)
            'rekomenduemaya',       // Рекомендуемая (Да/Нет)
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Заголовки
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFE2E8F0',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 30,  // Название
            'B' => 30,  // Название UA
            'C' => 20,  // Слаг
            'D' => 40,  // Анотація
            'E' => 25,  // Джерело анотації
            'F' => 20,  // Автор (старый)
            'G' => 25,  // Автор
            'H' => 15,  // ISBN
            'I' => 12,  // Год издания
            'J' => 12,  // Перше видання
            'K' => 20,  // Издательство
            'L' => 30,  // Обложка
            'M' => 8,   // Язык
            'N' => 10,  // Мова оригіналу
            'O' => 30,  // Синоніми
            'P' => 20,  // Серія
            'Q' => 10,  // Страницы
            'R' => 10,  // Рейтинг
            'S' => 15,  // Количество рецензий
            'T' => 30,  // Категории
            'U' => 12,  // Рекомендуемая
        ];
    }
}
