<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Book;
use App\Models\Review;
use App\Models\Discussion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Всего пользователей', User::count())
                ->description('Зарегистрированных пользователей')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            
            Stat::make('Всего книг', Book::count())
                ->description('В базе данных')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),
            
            Stat::make('Всего рецензий', Review::count())
                ->description('Написано рецензий')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning'),
            
            Stat::make('Всего постов', Discussion::count())
                ->description('В форуме')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
        ];
    }
}
