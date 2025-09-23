<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class RecentBooksChart extends ChartWidget
{
    protected static ?string $heading = 'Книги за последние 7 дней';

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Book::whereDate('created_at', $date)->count();
            $data[] = $count;
            $labels[] = $date->format('d.m');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Добавлено книг',
                    'data' => $data,
                    'backgroundColor' => '#3B82F6',
                    'borderColor' => '#1D4ED8',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
