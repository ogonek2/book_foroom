<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
use App\Models\Author;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Создать новую книгу';
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/admin11') => 'Главная',
            $this->getResource()::getUrl('index') => 'Книги',
            'Создать',
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->syncPrimaryAuthorFields($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function syncPrimaryAuthorFields(array $data): array
    {
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
