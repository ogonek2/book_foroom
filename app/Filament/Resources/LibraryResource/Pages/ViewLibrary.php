<?php

namespace App\Filament\Resources\LibraryResource\Pages;

use App\Filament\Resources\LibraryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLibrary extends ViewRecord
{
    protected static string $resource = LibraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

