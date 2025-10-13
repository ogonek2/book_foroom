<?php

namespace App\Filament\Resources\DiscussionResource\Pages;

use App\Filament\Resources\DiscussionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDiscussion extends ViewRecord
{
    protected static string $resource = DiscussionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

