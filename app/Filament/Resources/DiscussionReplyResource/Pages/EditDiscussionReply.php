<?php

namespace App\Filament\Resources\DiscussionReplyResource\Pages;

use App\Filament\Resources\DiscussionReplyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDiscussionReply extends EditRecord
{
    protected static string $resource = DiscussionReplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

