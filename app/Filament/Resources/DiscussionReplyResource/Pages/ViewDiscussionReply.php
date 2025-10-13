<?php

namespace App\Filament\Resources\DiscussionReplyResource\Pages;

use App\Filament\Resources\DiscussionReplyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDiscussionReply extends ViewRecord
{
    protected static string $resource = DiscussionReplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

