<?php

namespace App\Filament\Widgets;

use App\Models\Review;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class ReviewsChart extends ChartWidget
{
    protected static ?string $heading = 'Рецензии за последние 30 дней';

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Review::whereDate('created_at', $date)->count();
            $data[] = $count;
            $labels[] = $date->format('d.m');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Написано рецензий',
                    'data' => $data,
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#059669',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
