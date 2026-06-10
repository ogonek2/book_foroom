<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BooksTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return [
            'main_book_name',
            'book_name_ua',
            'slag',
            'annotation',
            'annotation_source',
            'avtor_staryy',
            'first_name_author',
            'last_name_author',
            'UKR_publish_year',
            'first_publish_year',
            'cover',
            'original_language',
            'synonym',
            'series',
            'num_in_series',
            'cycle',
            'format',
            'included_works',
            'pages',
            'category',
            'recommend',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
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

    public function columnWidths(): array
    {
        return [
            'A' => 30,  // main_book_name
            'B' => 30,  // book_name_ua
            'C' => 22,  // slag
            'D' => 40,  // annotation
            'E' => 25,  // annotation_source
            'F' => 28,  // avtor_staryy
            'G' => 18,  // first_name_author
            'H' => 22,  // last_name_author
            'I' => 14,  // UKR_publish_year
            'J' => 14,  // first_publish_year
            'K' => 30,  // cover
            'L' => 14,  // original_language
            'M' => 30,  // synonym
            'N' => 20,  // series
            'O' => 14,  // num_in_series
            'P' => 22,  // cycle
            'Q' => 24,  // format
            'R' => 36,  // included_works
            'S' => 10,  // pages
            'T' => 30,  // category
            'U' => 12,  // recommend
        ];
    }
}
