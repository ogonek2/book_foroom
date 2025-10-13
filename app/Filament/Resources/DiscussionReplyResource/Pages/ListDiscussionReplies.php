<?php

namespace App\Filament\Resources\DiscussionReplyResource\Pages;

use App\Filament\Resources\DiscussionReplyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiscussionReplies extends ListRecords
{
    protected static string $resource = DiscussionReplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

