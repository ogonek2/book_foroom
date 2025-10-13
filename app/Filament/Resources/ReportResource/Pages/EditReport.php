<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Если статус изменился на не-pending, устанавливаем moderator_id и processed_at
        if ($data['status'] !== \App\Models\Report::STATUS_PENDING) {
            if (empty($data['moderator_id'])) {
                $data['moderator_id'] = auth()->id();
            }
            if (empty($data['processed_at'])) {
                $data['processed_at'] = now();
            }
        }
        
        return $data;
    }
}

