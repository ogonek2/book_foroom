<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookReadingStatusResource\Pages;
use App\Models\BookReadingStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookReadingStatusResource extends Resource
{
    protected static ?string $model = BookReadingStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    
    protected static ?string $navigationLabel = 'Статусы чтения';
    
    protected static ?string $modelLabel = 'Статус чтения';
    
    protected static ?string $pluralModelLabel = 'Статусы чтения';
    
    protected static ?string $navigationGroup = 'Контент';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Пользователь')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        
                        Forms\Components\Select::make('book_id')
                            ->label('Книга')
                            ->relationship('book', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'read' => 'Прочитано',
                                'reading' => 'Читаю',
                                'want_to_read' => 'Буду читать',
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('rating')
                            ->label('Рейтинг')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->helperText('Рейтинг от 1 до 10'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Даты')
                    ->schema([
                        Forms\Components\DatePicker::make('started_at')
                            ->label('Начало чтения'),
                        
                        Forms\Components\DatePicker::make('finished_at')
                            ->label('Окончание чтения'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Заметки')
                    ->schema([
                        Forms\Components\Textarea::make('review')
                            ->label('Заметки/рецензия')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->limit(25),
                
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Книга')
                    ->sortable()
                    ->searchable()
                    ->limit(40)
                    ->description(fn (BookReadingStatus $record): string => $record->book->author_full_name ?? ''),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'read' => 'success',
                        'reading' => 'warning',
                        'want_to_read' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'read' => 'Прочитано',
                        'reading' => 'Читаю',
                        'want_to_read' => 'Буду читать',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('rating')
                    ->label('Рейтинг')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 8 => 'success',
                        $state >= 6 => 'warning',
                        $state >= 4 => 'info',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state): string => $state ? $state . '/10 ⭐' : 'Без оценки')
                    ->placeholder('Без оценки'),
                
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Начало')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('—'),
                
                Tables\Columns\TextColumn::make('finished_at')
                    ->label('Окончание')
                    ->date('d.m.Y')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('—'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Добавлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'read' => 'Прочитано',
                        'reading' => 'Читаю',
                        'want_to_read' => 'Буду читать',
                    ])
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('user')
                    ->label('Пользователь')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('book')
                    ->label('Книга')
                    ->relationship('book', 'title')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('with_rating')
                    ->label('С оценкой')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('rating')),
                
                Tables\Filters\Filter::make('high_rating')
                    ->label('Высокий рейтинг (8+)')
                    ->query(fn (Builder $query): Builder => $query->where('rating', '>=', 8)),
                
                Tables\Filters\Filter::make('finished_recently')
                    ->label('Завершено недавно')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('status', 'read')
                              ->where('finished_at', '>=', now()->subDays(30))
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListBookReadingStatuses::route('/'),
            'view' => Pages\ViewBookReadingStatus::route('/{record}'),
            'edit' => Pages\EditBookReadingStatus::route('/{record}/edit'),
        ];
    }
}

