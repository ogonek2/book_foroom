<?php

namespace App\Filament\Resources\BookReadingStatusResource\Pages;

use App\Filament\Resources\BookReadingStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\BookReadingStatus;

class ListBookReadingStatuses extends ListRecords
{
    protected static string $resource = BookReadingStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Обычно статусы создаются из фронтенда
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Все'),
            'read' => Tab::make('Прочитано')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'read'))
                ->badge(BookReadingStatus::where('status', 'read')->count()),
            'reading' => Tab::make('Читаю')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'reading'))
                ->badge(BookReadingStatus::where('status', 'reading')->count()),
            'want_to_read' => Tab::make('Буду читать')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'want_to_read'))
                ->badge(BookReadingStatus::where('status', 'want_to_read')->count()),
        ];
    }
}

