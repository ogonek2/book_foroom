<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\RecentBooksChart;
use App\Filament\Widgets\ReviewsChart;
use App\Filament\Widgets\UserActivityTable;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $navigationLabel = 'Главная';
    
    protected static ?string $title = 'Панель управления';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            RecentBooksChart::class,
            ReviewsChart::class,
            UserActivityTable::class,
        ];
    }
}
