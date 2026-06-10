<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use App\Models\Author;
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $authorIds = $this->record->authors()->pluck('authors.id')->all();

        if ($authorIds === [] && $this->record->author_id) {
            $data['authors'] = [$this->record->author_id];
        } elseif ($authorIds !== []) {
            $data['authors'] = $authorIds;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['rating'], $data['reviews_count']);

        if (isset($data['cover_image']) && is_array($data['cover_image'])) {
            $data['cover_image'] = $data['cover_image'][0] ?? null;
        }

        $authorIds = array_values(array_filter((array) ($data['authors'] ?? [])));
        if ($authorIds !== []) {
            $data['author_id'] = $authorIds[0];
            $author = Author::find($authorIds[0]);
            if ($author) {
                $data['author'] = $author->full_name;
            }
        }

        return $data;
    }
}
