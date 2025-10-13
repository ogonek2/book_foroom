<?php

namespace App\Filament\Resources\BookReadingStatusResource\Pages;

use App\Filament\Resources\BookReadingStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookReadingStatus extends EditRecord
{
    protected static string $resource = BookReadingStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

