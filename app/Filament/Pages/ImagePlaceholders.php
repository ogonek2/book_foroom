<?php

namespace App\Filament\Pages;

use App\Services\ImagePlaceholderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
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
                            ->helperText('Можно указать полный URL, data:image/... или путь вида /images/placeholders/book-cover.svg')
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('book_cover_upload')
                            ->label('Загрузить файл (перезапишет URL)')
                            ->image()
                            ->multiple(false)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                            ->maxSize(2048)
                            ->fetchFileInformation(false)
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                return $this->storePublicPlaceholder($file, 'book-cover');
                            })
                            ->dehydrated(false)
                            ->afterStateUpdated(function ($state, Forms\Set $set): void {
                                if (blank($state)) {
                                    return;
                                }

                                $path = $this->extractUploadedPath($state);

                                if (is_string($path) && $path !== '') {
                                    $set('book_cover', $path);
                                }
                            }),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Forms\Components\Section::make('Заглушка для фото автора')
                    ->schema([
                        Forms\Components\TextInput::make('author_photo')
                            ->label('URL или путь')
                            ->helperText('Можно указать полный URL, data:image/... или путь вида /images/placeholders/author-photo.svg')
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('author_photo_upload')
                            ->label('Загрузить файл (перезапишет URL)')
                            ->image()
                            ->multiple(false)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                            ->maxSize(2048)
                            ->fetchFileInformation(false)
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                return $this->storePublicPlaceholder($file, 'author-photo');
                            })
                            ->dehydrated(false)
                            ->afterStateUpdated(function ($state, Forms\Set $set): void {
                                if (blank($state)) {
                                    return;
                                }

                                $path = $this->extractUploadedPath($state);

                                if (is_string($path) && $path !== '') {
                                    $set('author_photo', $path);
                                }
                            }),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    private function storePublicPlaceholder(TemporaryUploadedFile $file, string $prefix): string
    {
        $publicDir = public_path('images/placeholders');

        if (! is_dir($publicDir)) {
            if (! @mkdir($publicDir, 0775, true) && ! is_dir($publicDir)) {
                throw new \RuntimeException("Не удалось создать папку для заглушек: {$publicDir}");
            }
        }

        if (! is_writable($publicDir)) {
            throw new \RuntimeException(
                "Папка не доступна для записи: {$publicDir}. " .
                "Выдай права на запись пользователю PHP-FPM (например: chown/chmod для public/images/placeholders)."
            );
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        $filename = $prefix . '-' . Str::uuid()->toString() . '.' . $ext;

        $targetPath = $publicDir . DIRECTORY_SEPARATOR . $filename;
        if (! @copy($file->getRealPath(), $targetPath)) {
            $err = error_get_last();
            $msg = $err['message'] ?? 'unknown error';
            throw new \RuntimeException("Не удалось сохранить файл в {$targetPath}: {$msg}");
        }

        // Возвращаем публичный путь, который затем сохранится в БД.
        return '/images/placeholders/' . $filename;
    }

    private function extractUploadedPath(mixed $state): ?string
    {
        if (is_string($state)) {
            return $state !== '' ? $state : null;
        }

        if (! is_array($state) || $state === []) {
            return null;
        }

        // Common: ['uuid' => '/images/placeholders/..jpg']
        $first = reset($state);
        if (is_string($first) && $first !== '') {
            return $first;
        }

        // Sometimes: ['uuid' => ['url' => '...']] or ['uuid' => ['path' => '...']]
        if (is_array($first)) {
            foreach (['url', 'path'] as $k) {
                if (isset($first[$k]) && is_string($first[$k]) && $first[$k] !== '') {
                    return $first[$k];
                }
            }
        }

        return null;
    }

    public function save(): void
    {
        $state = $this->form->getState();

        // Sometimes TextInput doesn't get updated from FileUpload state.
        // If a file was uploaded, derive the path from upload component state.
        $bookCover = $state['book_cover'] ?? null;
        $authorPhoto = $state['author_photo'] ?? null;

        if (blank($bookCover) && isset($this->data['book_cover_upload'])) {
            $bookCover = $this->extractUploadedPath($this->data['book_cover_upload']);
        }

        if (blank($authorPhoto) && isset($this->data['author_photo_upload'])) {
            $authorPhoto = $this->extractUploadedPath($this->data['author_photo_upload']);
        }

        ImagePlaceholderService::set(ImagePlaceholderService::KEY_BOOK_COVER, $bookCover);
        ImagePlaceholderService::set(ImagePlaceholderService::KEY_AUTHOR_PHOTO, $authorPhoto);

        Notification::make()
            ->title('Сохранено')
            ->success()
            ->send();
    }
}

