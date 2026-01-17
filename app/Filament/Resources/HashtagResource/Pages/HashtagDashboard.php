<?php

namespace App\Filament\Resources\HashtagResource\Pages;

use Filament\Pages\Page;

class HashtagDashboard extends Page
{
    protected static string $view = 'filament.resources.hashtag-resource.pages.hashtag-dashboard';

    protected static ?string $title = 'Дашборд хештегів';

    protected static ?string $navigationLabel = 'Дашборд хештегів';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
}
