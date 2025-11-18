<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SelectedBooksExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $selectedIds;

    public function __construct($selectedIds)
    {
        $this->selectedIds = $selectedIds;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Book::with(['author', 'categories'])
            ->whereIn('id', $this->selectedIds)
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Название',
            'Название (UA)',
            'Слаг',
            'Анотация',
            'Источник анотации',
            'Автор (старый)',
            'Автор',
            'ISBN',
            'Год издания',
            'Первое издание',
            'Издательство',
            'Обложка',
            'Язык',
            'Мова оригіналу',
            'Страницы',
            'Рейтинг',
            'Количество рецензий',
            'Категории',
            'Синоніми',
            'Серія',
            'Рекомендуемая',
            'Дата создания',
            'Дата обновления',
        ];
    }

    /**
     * @param Book $book
     * @return array
     */
    public function map($book): array
    {
        return [
            $book->id,
            $book->title,
            $book->book_name_ua,
            $book->slug,
            $book->annotation,
            $book->annotation_source,
            $book->author, // старое поле author
            $book->author_full_name,
            $book->isbn,
            $book->publication_year,
            $book->first_publish_year,
            $book->publisher,
            $book->cover_image,
            $book->language,
            $book->original_language,
            $book->pages,
            $book->rating,
            $book->reviews_count,
            $book->categories->pluck('name')->implode(', '),
            implode(', ', $book->synonyms ?? []),
            $book->series,
            $book->is_featured ? 'Да' : 'Нет',
            $book->created_at?->format('d.m.Y H:i'),
            $book->updated_at?->format('d.m.Y H:i'),
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
            'A' => 8,   // ID
            'B' => 30,  // Название
            'C' => 30,  // Название UA
            'D' => 20,  // Слаг
            'E' => 40,  // Анотация
            'F' => 25,  // Источник анотации
            'G' => 20,  // Автор (старый)
            'H' => 25,  // Автор
            'I' => 15,  // ISBN
            'J' => 12,  // Год издания
            'K' => 12,  // Перше видання
            'L' => 20,  // Издательство
            'M' => 30,  // Обложка
            'N' => 8,   // Язык
            'O' => 10,  // Мова оригіналу
            'P' => 10,  // Страницы
            'Q' => 10,  // Рейтинг
            'R' => 20,  // Категории
            'S' => 25,  // Синоніми
            'T' => 20,  // Серія
            'U' => 12,  // Рекомендуемая
            'V' => 15,  // Дата создания
            'W' => 15,  // Дата обновления
        ];
    }
}
