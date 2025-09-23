<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBook extends ViewRecord
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return $this->record->title;
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/admin11') => 'Главная',
            $this->getResource()::getUrl('index') => 'Книги',
            'Просмотр',
        ];
    }
}
