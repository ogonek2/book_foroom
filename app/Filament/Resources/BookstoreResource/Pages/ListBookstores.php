<?php

namespace App\Filament\Resources\BookstoreResource\Pages;

use App\Filament\Resources\BookstoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookstores extends ListRecords
{
    protected static string $resource = BookstoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

