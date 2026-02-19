<?php

namespace App\Filament\Resources\ReviewResource\Pages;

use App\Filament\Resources\ReviewResource;
use App\Models\Review;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListReviews extends ListRecords
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'reviews' => Tab::make('Рецензії')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('parent_id'))
                ->badge(fn () => Review::whereNull('parent_id')->count()),
            'replies' => Tab::make('Коментарі')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('parent_id'))
                ->badge(fn () => Review::whereNotNull('parent_id')->count()),
            'all' => Tab::make('Всі')
                ->badge(fn () => Review::count()),
        ];
    }

    public function getDefaultActiveTab(): string
    {
        return 'reviews';
    }
}
