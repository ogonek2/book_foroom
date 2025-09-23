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
            'nazvanie',           // Название (ОБЯЗАТЕЛЬНО)
            'slag',               // Слаг (автогенерация)
            'opisanie',           // Описание (опционально)
            'avtor_staryy',       // Автор (старый) (опционально)
            'isbn',               // ISBN (опционально)
            'god_izdaniya',       // Год издания (опционально)
            'izdatelstvo',        // Издательство (опционально)
            'oblozhka',           // Обложка (опционально)
            'yazyk',              // Язык (по умолчанию: ru)
            'stranitsy',          // Страницы (опционально)
            'reiting',            // Рейтинг (опционально)
            'kolichestvo_recenziy', // Количество рецензий (опционально)
            'kategoriya',         // Категория (создается автоматически)
            'avtor',              // Автор (создается автоматически)
            'rekomenduemaya',     // Рекомендуемая (Да/Нет)
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
            'B' => 20,  // Слаг
            'C' => 40,  // Описание
            'D' => 20,  // Автор (старый)
            'E' => 15,  // ISBN
            'F' => 12,  // Год издания
            'G' => 20,  // Издательство
            'H' => 30,  // Обложка
            'I' => 8,   // Язык
            'J' => 10,  // Страницы
            'K' => 10,  // Рейтинг
            'L' => 15,  // Количество рецензий
            'M' => 20,  // Категория
            'N' => 25,  // Автор
            'O' => 12,  // Рекомендуемая
        ];
    }
}
