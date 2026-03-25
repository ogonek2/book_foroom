<?php

namespace App\Filament\Pages;

use App\Helpers\CDNUploader;
use App\Services\ImagePlaceholderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImagePlaceholders extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Заглушки изображений';
    protected static ?string $title = 'Заглушки изображений';
    protected static ?string $navigationGroup = 'Настройки';

    protected static string $view = 'filament.pages.image-placeholders';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'book_cover' => ImagePlaceholderService::getRaw(ImagePlaceholderService::KEY_BOOK_COVER),
            'author_photo' => ImagePlaceholderService::getRaw(ImagePlaceholderService::KEY_AUTHOR_PHOTO),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Заглушка для обложки книги')
                    ->schema([
                        Forms\Components\TextInput::make('book_cover')
                            ->label('URL или путь')
                            ->helperText('Можно указать полный URL (CDN), data:image/... или путь вида /images/placeholders/book-cover.svg')
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('book_cover_upload')
                            ->label('Загрузить файл (перезапишет URL)')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                            ->maxSize(2048)
                            ->fetchFileInformation(false)
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                $cdnUrl = CDNUploader::uploadFile($file, 'placeholders/book-covers');

                                if (! $cdnUrl) {
                                    throw new \RuntimeException('Не удалось загрузить файл на CDN.');
                                }

                                return $cdnUrl;
                            })
                            ->dehydrated(false)
                            ->afterStateUpdated(function ($state, Forms\Set $set): void {
                                if (blank($state)) {
                                    return;
                                }

                                // Filament может вернуть строку url после saveUploadedFileUsing
                                if (is_string($state)) {
                                    $set('book_cover', $state);
                                }
                            }),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Forms\Components\Section::make('Заглушка для фото автора')
                    ->schema([
                        Forms\Components\TextInput::make('author_photo')
                            ->label('URL или путь')
                            ->helperText('Можно указать полный URL (CDN), data:image/... или путь вида /images/placeholders/author-photo.svg')
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('author_photo_upload')
                            ->label('Загрузить файл (перезапишет URL)')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                            ->maxSize(2048)
                            ->fetchFileInformation(false)
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                $cdnUrl = CDNUploader::uploadFile($file, 'placeholders/author-photos');

                                if (! $cdnUrl) {
                                    throw new \RuntimeException('Не удалось загрузить файл на CDN.');
                                }

                                return $cdnUrl;
                            })
                            ->dehydrated(false)
                            ->afterStateUpdated(function ($state, Forms\Set $set): void {
                                if (blank($state)) {
                                    return;
                                }

                                if (is_string($state)) {
                                    $set('author_photo', $state);
                                }
                            }),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        ImagePlaceholderService::set(ImagePlaceholderService::KEY_BOOK_COVER, $state['book_cover'] ?? null);
        ImagePlaceholderService::set(ImagePlaceholderService::KEY_AUTHOR_PHOTO, $state['author_photo'] ?? null);

        Notification::make()
            ->title('Сохранено')
            ->success()
            ->send();
    }
}

