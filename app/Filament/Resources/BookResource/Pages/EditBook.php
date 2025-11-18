<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class EditBook extends EditRecord
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Редактировать: ' . $this->record->title;
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/admin11') => 'Главная',
            $this->getResource()::getUrl('index') => 'Книги',
            'Редактировать',
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Можно добавить виджеты в футер
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        \Log::info('EditBook::mutateFormDataBeforeSave called', ['data' => $data]);

        unset($data['rating'], $data['reviews_count']);

        if (isset($data['cover_image']) && is_array($data['cover_image'])) {
            $data['cover_image'] = $data['cover_image'][0] ?? null;
        }

        return $data;
    }
}
