<?php

namespace App\Filament\Resources\BookReadingStatusResource\Pages;

use App\Filament\Resources\BookReadingStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBookReadingStatus extends ViewRecord
{
    protected static string $resource = BookReadingStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

