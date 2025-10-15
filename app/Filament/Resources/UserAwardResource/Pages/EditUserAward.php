<?php

namespace App\Filament\Resources\UserAwardResource\Pages;

use App\Filament\Resources\UserAwardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserAward extends EditRecord
{
    protected static string $resource = UserAwardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
