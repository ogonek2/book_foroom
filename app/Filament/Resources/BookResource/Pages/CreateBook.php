<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Filament\Resources\BookResource;
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
}
