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

                                // Filament может вернуть строку или массив (uuid => path)
                                $path = null;

                                if (is_string($state)) {
                                    $path = $state;
                                } elseif (is_array($state)) {
                                    $first = reset($state);
                                    if (is_string($first)) {
                                        $path = $first;
                                    }
                                }

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

                                $path = null;

                                if (is_string($state)) {
                                    $path = $state;
                                } elseif (is_array($state)) {
                                    $first = reset($state);
                                    if (is_string($first)) {
                                        $path = $first;
                                    }
                                }

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

