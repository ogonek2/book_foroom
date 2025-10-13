<?php

namespace App\Filament\Resources\BookstoreResource\Pages;

use App\Filament\Resources\BookstoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookstore extends EditRecord
{
    protected static string $resource = BookstoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

