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
        return Book::with(['author', 'category'])
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
            'Слаг',
            'Описание',
            'Автор (старый)',
            'ISBN',
            'Год издания',
            'Издательство',
            'Обложка',
            'Язык',
            'Страницы',
            'Рейтинг',
            'Количество рецензий',
            'Категория',
            'Автор',
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
            $book->slug,
            $book->description,
            $book->author, // старое поле author
            $book->isbn,
            $book->publication_year,
            $book->publisher,
            $book->cover_image,
            $book->language,
            $book->pages,
            $book->rating,
            $book->reviews_count,
            $book->category?->name ?? 'Не указана',
            $book->author_full_name,
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
            'C' => 20,  // Слаг
            'D' => 40,  // Описание
            'E' => 20,  // Автор (старый)
            'F' => 15,  // ISBN
            'G' => 12,  // Год издания
            'H' => 20,  // Издательство
            'I' => 30,  // Обложка
            'J' => 8,   // Язык
            'K' => 10,  // Страницы
            'L' => 10,  // Рейтинг
            'M' => 15,  // Количество рецензий
            'N' => 20,  // Категория
            'O' => 25,  // Автор
            'P' => 12,  // Рекомендуемая
            'Q' => 15,  // Дата создания
            'R' => 15,  // Дата обновления
        ];
    }
}
