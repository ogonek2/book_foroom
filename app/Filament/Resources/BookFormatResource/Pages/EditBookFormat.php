<?php

namespace App\Filament\Resources\BookFormatResource\Pages;

use App\Filament\Resources\BookFormatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookFormat extends EditRecord
{
    protected static string $resource = BookFormatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
