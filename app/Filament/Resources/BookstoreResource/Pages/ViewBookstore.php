<?php

namespace App\Filament\Resources\BookstoreResource\Pages;

use App\Filament\Resources\BookstoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBookstore extends ViewRecord
{
    protected static string $resource = BookstoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

