<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    // Проверяем, есть ли пользователи с этой ролью
                    if ($this->record->users()->count() > 0) {
                        \Filament\Notifications\Notification::make()
                            ->title('Невозможно удалить роль')
                            ->body('Эта роль назначена пользователям. Сначала снимите роль с пользователей.')
                            ->danger()
                            ->send();
                        
                        return false;
                    }
                }),
        ];
    }
}
