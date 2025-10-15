<?php

namespace App\Filament\Resources\UserAwardResource\Pages;

use App\Filament\Resources\UserAwardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserAwards extends ListRecords
{
    protected static string $resource = UserAwardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
