<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    // Проверяем, используется ли разрешение в ролях
                    if ($this->record->roles()->count() > 0) {
                        \Filament\Notifications\Notification::make()
                            ->title('Невозможно удалить разрешение')
                            ->body('Это разрешение используется в ролях. Сначала удалите его из всех ролей.')
                            ->danger()
                            ->send();
                        
                        return false;
                    }
                }),
        ];
    }
}
