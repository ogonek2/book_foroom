<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Report;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Обычно жалобы создаются из фронтенда, поэтому не добавляем кнопку создания
        ];
    }
    
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Все жалобы'),
            'pending' => Tab::make('Ожидают рассмотрения')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Report::STATUS_PENDING))
                ->badge(Report::where('status', Report::STATUS_PENDING)->count()),
            'reviewed' => Tab::make('Рассмотренные')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Report::STATUS_REVIEWED)),
            'resolved' => Tab::make('Разрешенные')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Report::STATUS_RESOLVED)),
            'dismissed' => Tab::make('Отклоненные')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', Report::STATUS_DISMISSED)),
            'my' => Tab::make('Мои')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('moderator_id', auth()->id()))
                ->badge(Report::where('moderator_id', auth()->id())->count()),
        ];
    }
}

