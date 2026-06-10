<?php

namespace App\Filament\Resources\BookFormatResource\Pages;

use App\Filament\Resources\BookFormatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookFormats extends ListRecords
{
    protected static string $resource = BookFormatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
