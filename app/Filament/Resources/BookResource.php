<?php

namespace App\Filament\Resources;

use App\Exports\BooksExport;
use App\Exports\BooksTemplateExport;
use App\Exports\SelectedBooksExport;
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
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Book::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->helperText('URL-дружественный идентификатор книги'),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->rows(4)
                            ->helperText('Подробное описание книги'),
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
                            ->searchable()
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
                            ->helperText('Год издания'),
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
                        Forms\Components\TextInput::make('rating')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->step(0.1)
                            ->helperText('Рейтинг от 0 до 5'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Категория и статус')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->maxLength(255)
                                    ->unique(Category::class, 'slug'),
                                Forms\Components\Textarea::make('description')
                                    ->rows(3),
                                Forms\Components\ColorPicker::make('color')
                                    ->default('#3B82F6'),
                                Forms\Components\TextInput::make('icon')
                                    ->maxLength(255)
                                    ->default('heroicon-o-book-open'),
                            ])
                            ->helperText('Выберите или создайте новую категорию'),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Рекомендуемая')
                            ->helperText('Показывать в рекомендуемых книгах'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Обложка')
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image')
                            ->image()
                            ->directory('book-covers')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->helperText('Загрузите обложку книги'),
                    ])
                    ->collapsible(),
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
                    ->description(fn (Book $record): string => $record->description ? \Str::limit($record->description, 50) : ''),
                Tables\Columns\TextColumn::make('author_full_name')
                    ->label('Автор')
                    ->searchable(['author.first_name', 'author.last_name', 'author'])
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Категория')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (Book $record): string => $record->category?->color ?? 'gray'),
                Tables\Columns\TextColumn::make('publication_year')
                    ->label('Год')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Рейтинг')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => $state . ' ⭐')
                    ->color(fn (string $state): string => match (true) {
                        $state >= 4.5 => 'success',
                        $state >= 3.5 => 'warning',
                        $state >= 2.5 => 'danger',
                        default => 'gray',
                    }),
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('author')
                    ->relationship('author', 'first_name')
                    ->searchable()
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
