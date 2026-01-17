<?php

namespace App\Filament\Resources;

use App\Exports\BooksExport;
use App\Exports\BooksTemplateExport;
use App\Exports\SelectedBooksExport;
use App\Helpers\CDNUploader;
use App\Helpers\FileHelper;
use App\Imports\SimpleBooksImport;
use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Models\Book;
use App\Models\Category;
use App\Models\Author;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    
    protected static ?string $navigationLabel = 'Книги';
    
    protected static ?string $modelLabel = 'Книга';
    
    protected static ?string $pluralModelLabel = 'Книги';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная інформація')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Назва (основна)')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }),
                        Forms\Components\TextInput::make('book_name_ua')
                            ->label('Назва українською')
                            ->maxLength(255)
                            ->helperText('Якщо відрізняється від основної назви'),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Book::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->helperText('URL-дружественный идентификатор книги'),
                        Forms\Components\Textarea::make('annotation')
                            ->label('Анотація')
                            ->columnSpanFull()
                            ->rows(4)
                            ->helperText('Детальний опис або анотація книги'),
                        Forms\Components\TextInput::make('annotation_source')
                            ->label('Джерело анотації')
                            ->maxLength(255)
                            ->helperText('Посилання або вказівка на джерело опису'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Автор и издательство')
                    ->schema([
                        Forms\Components\TextInput::make('author')
                            ->label('Автор (старое поле)')
                            ->maxLength(255)
                            ->helperText('Для совместимости со старыми данными'),
                        Forms\Components\Select::make('author_id')
                            ->label('Автор')
                            ->relationship('author', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn (Author $record): string => $record->full_name)
                            ->searchable(['first_name', 'last_name', 'middle_name'])
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('biography')
                                    ->rows(3),
                            ])
                            ->helperText('Выберите или создайте нового автора'),
                        Forms\Components\TextInput::make('publisher')
                            ->maxLength(255)
                            ->helperText('Название издательства'),
                        Forms\Components\TextInput::make('isbn')
                            ->maxLength(255)
                            ->helperText('ISBN книги (13 цифр)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Детали книги')
                    ->schema([
                        Forms\Components\TextInput::make('publication_year')
                            ->numeric()
                            ->minValue(1000)
                            ->maxValue(date('Y') + 10)
                            ->helperText('Рік поточного видання'),
                        Forms\Components\TextInput::make('first_publish_year')
                            ->numeric()
                            ->minValue(1000)
                            ->maxValue(date('Y') + 10)
                            ->helperText('Рік першого видання'),
                        Forms\Components\TextInput::make('pages')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Количество страниц'),
                        Forms\Components\Select::make('language')
                            ->options([
                                'ru' => 'Русский',
                                'uk' => 'Украинский',
                                'en' => 'Английский',
                                'de' => 'Немецкий',
                                'fr' => 'Французский',
                                'es' => 'Испанский',
                            ])
                            ->default('ru')
                            ->helperText('Язык книги'),
                        Forms\Components\Select::make('original_language')
                            ->label('Мова оригіналу')
                            ->options([
                                'ru' => 'Русский',
                                'uk' => 'Українська',
                                'en' => 'English',
                                'de' => 'Deutsch',
                                'fr' => 'Français',
                                'es' => 'Español',
                                'it' => 'Italiano',
                                'pl' => 'Polski',
                                'ja' => '日本語',
                                'zh' => '中文',
                            ])
                            ->searchable()
                            ->preload()
                            ->helperText('Вкажіть мову оригіналу'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Додаткові дані')
                    ->schema([
                        Forms\Components\TagsInput::make('synonyms')
                            ->label('Синоніми / альтернативні назви')
                            ->placeholder('Додайте назву та натисніть Enter')
                            ->helperText('Використовуйте Enter для додавання кількох значень'),
                        Forms\Components\TextInput::make('series')
                            ->label('Серія')
                            ->maxLength(255)
                            ->helperText('Якщо книга входить до серії'),
                        Forms\Components\TextInput::make('series_number')
                            ->label('Номер у серії')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Номер книги в серії (наприклад, 1, 2, 3...)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Категорії та статус')
                    ->schema([
                        Forms\Components\CheckboxList::make('categories')
                            ->relationship('categories', 'name')
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2)
                            ->gridDirection('row')
                            ->helperText('Выберите одну или несколько категорий для книги')
                            ->required(),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Рекомендуемая')
                            ->helperText('Показывать в рекомендуемых книгах'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Обложка')
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Обкладинка')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->helperText('Загрузите обложку книги (будет сохранена на CDN)')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(2048)
                            ->fetchFileInformation(false)
                            ->afterStateHydrated(function (Forms\Components\FileUpload $component, $state): void {
                                if (blank($state)) {
                                    $component->state([]);

                                    return;
                                }

                                $component->state([
                                    (string) Str::uuid() => $state,
                                ]);
                            })
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                $cdnUrl = CDNUploader::uploadFile($file, 'book-covers');

                                if (!$cdnUrl) {
                                    throw ValidationException::withMessages([
                                        'cover_image' => 'Не вдалося зберегти обкладинку на CDN. Спробуйте ще раз.',
                                    ]);
                                }

                                return $cdnUrl;
                            })
                            ->getUploadedFileUsing(function (Forms\Components\FileUpload $component, string $file, string | array | null $storedFileNames): ?array {
                                if (blank($file)) {
                                    return null;
                                }

                                $url = $file;
                                $name = null;

                                if (is_array($storedFileNames)) {
                                    $name = $storedFileNames[$file] ?? null;
                                } elseif (is_string($storedFileNames)) {
                                    $name = $storedFileNames;
                                }

                                $name ??= basename(parse_url($url, PHP_URL_PATH) ?: $url);

                                return [
                                    'name' => $name,
                                    'size' => null,
                                    'type' => null,
                                    'url' => $url,
                                ];
                            })
                            ->deleteUploadedFileUsing(function ($file): void {
                                $fileUrl = null;
                                
                                // Обрабатываем разные типы данных, которые может передать Filament
                                if (is_string($file)) {
                                    $fileUrl = $file;
                                } elseif (is_array($file) && isset($file['url'])) {
                                    $fileUrl = $file['url'];
                                } elseif (is_object($file) && isset($file->url)) {
                                    $fileUrl = $file->url;
                                }
                                
                                if ($fileUrl && filter_var($fileUrl, FILTER_VALIDATE_URL)) {
                                    CDNUploader::deleteFromBunnyCDN($fileUrl);
                                }
                            }),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make('Цены в магазинах')
                    ->schema([
                        Forms\Components\Repeater::make('bookPrices')
                            ->relationship('bookPrices')
                            ->schema([
                                Forms\Components\Select::make('bookstore_id')
                                    ->label('Магазин')
                                    ->relationship('bookstore', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->distinct()
                                    ->helperText('Выберите магазин'),
                                Forms\Components\TextInput::make('price')
                                    ->label('Цена')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->suffix('грн')
                                    ->helperText('Цена книги'),
                                Forms\Components\TextInput::make('product_url')
                                    ->label('Ссылка на товар')
                                    ->url()
                                    ->required()
                                    ->maxLength(500)
                                    ->helperText('Полная ссылка на страницу товара в магазине'),
                                Forms\Components\Select::make('currency')
                                    ->label('Валюта')
                                    ->options([
                                        'UAH' => 'Гривна (UAH)',
                                        'USD' => 'Доллар (USD)',
                                        'EUR' => 'Евро (EUR)',
                                    ])
                                    ->default('UAH')
                                    ->required(),
                                Forms\Components\Toggle::make('is_available')
                                    ->label('В наличии')
                                    ->default(true)
                                    ->helperText('Товар доступен для покупки'),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => 
                                $state['bookstore_id'] ? 
                                    \App\Models\Bookstore::find($state['bookstore_id'])?->name ?? 'Новая цена' 
                                    : 'Новая цена'
                            )
                            ->collapsible()
                            ->collapsed()
                            ->addActionLabel('Добавить цену')
                            ->reorderable(false)
                            ->helperText('Добавьте цены на книгу в разных магазинах'),
                    ])
                    ->collapsible()
                    ->collapsed(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(url('/images/no-cover.png')),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Book $record): string => $record->annotation ? \Str::limit($record->annotation, 50) : ''),
                Tables\Columns\TextColumn::make('book_name_ua')
                    ->label('Назва (UA)')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('author_full_name')
                    ->label('Автор')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Категории')
                    ->badge()
                    ->separator(',')
                    ->color('info')
                    ->wrap()
                    ->limit(30),
                Tables\Columns\TextColumn::make('publication_year')
                    ->label('Год')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('first_publish_year')
                    ->label('Перший рік')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Рейтинг')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 1) . ' ⭐')
                    ->description('Розраховується за оцінками користувачів')
                    ->color(fn ($state): string => match (true) {
                        $state >= 4.5 => 'success',
                        $state >= 3.5 => 'warning',
                        $state >= 2.5 => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('reviews_count')
                    ->label('Отзывы')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('language')
                    ->label('Язык')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ru' => 'danger',
                        'uk' => 'warning',
                        'en' => 'success',
                        'de' => 'gray',
                        'fr' => 'info',
                        'es' => 'purple',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ru' => 'Русский',
                        'uk' => 'Украинский',
                        'en' => 'English',
                        'de' => 'Deutsch',
                        'fr' => 'Français',
                        'es' => 'Español',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Рекомендуемая')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('original_language')
                    ->label('Мова оригіналу')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('series')
                    ->label('Серія')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label('Категории'),
                Tables\Filters\SelectFilter::make('author')
                    ->relationship('author', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn (Author $record): string => $record->full_name)
                    ->searchable(['first_name', 'last_name', 'middle_name'])
                    ->preload(),
                Tables\Filters\SelectFilter::make('language')
                    ->options([
                        'ru' => 'Русский',
                        'uk' => 'Украинский',
                        'en' => 'English',
                        'de' => 'Deutsch',
                        'fr' => 'Français',
                        'es' => 'Español',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Рекомендуемые')
                    ->placeholder('Все книги')
                    ->trueLabel('Только рекомендуемые')
                    ->falseLabel('Только обычные'),
                Tables\Filters\Filter::make('high_rated')
                    ->label('Высокий рейтинг')
                    ->query(fn (Builder $query): Builder => $query->where('rating', '>=', 4.0)),
                Tables\Filters\Filter::make('recent')
                    ->label('Недавние')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(30))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export_selected')
                        ->label('Экспорт выбранных')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function ($records) {
                            $selectedIds = $records->pluck('id')->toArray();
                            return Excel::download(new SelectedBooksExport($selectedIds), 'selected-books-' . now()->format('Y-m-d-H-i-s') . '.xlsx');
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Экспорт в Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return Excel::download(new BooksExport, 'books-' . now()->format('Y-m-d-H-i-s') . '.xlsx');
                    }),
                Tables\Actions\Action::make('import')
                    ->label('Импорт из Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('info')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Excel файл')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->required()
                            ->helperText('Поддерживаются файлы .xlsx и .xls'),
                    ])
                    ->action(function (array $data) {
                        $file = $data['file'];
                        
                        // Получаем путь к файлу используя helper
                        $filePath = FileHelper::getFilePath($file);
                        
                        if (!$filePath || !file_exists($filePath)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Ошибка импорта')
                                ->body('Файл не найден. Попробуйте использовать отдельную страницу импорта: /admin11/import-books')
                                ->danger()
                                ->send();
                            return;
                        }
                        
                        try {
                            $import = new SimpleBooksImport();
                            Excel::import($import, $filePath);
                            
                            $importedCount = $import->getRowCount();
                            $errors = $import->getErrors();
                            $warnings = $import->getWarnings();
                            
                            $message = "Успешно импортировано {$importedCount} книг";
                            
                            if (!empty($warnings)) {
                                $message .= ". Предупреждений: " . count($warnings);
                            }
                            
                            if (!empty($errors)) {
                                $message .= ". Ошибок: " . count($errors);
                                \Filament\Notifications\Notification::make()
                                    ->title('Импорт завершен с ошибками')
                                    ->body($message)
                                    ->warning()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Импорт завершен')
                                    ->body($message)
                                    ->success()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Ошибка импорта')
                                ->body('Произошла ошибка при импорте: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('download_template')
                    ->label('Скачать шаблон')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function () {
                        return Excel::download(new BooksTemplateExport, 'books-template.xlsx');
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'view' => Pages\ViewBook::route('/{record}'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
