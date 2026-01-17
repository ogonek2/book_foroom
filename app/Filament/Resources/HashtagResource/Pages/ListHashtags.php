<?php

namespace App\Filament\Resources\HashtagResource\Pages;

use App\Filament\Resources\HashtagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHashtags extends ListRecords
{
    protected static string $resource = HashtagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('dashboard')
                ->label('Дашборд')
                ->icon('heroicon-o-chart-bar')
                ->url(fn () => \App\Filament\Resources\HashtagResource\Pages\HashtagDashboard::getUrl())
                ->color('success'),
            Actions\CreateAction::make(),
        ];
    }
}
