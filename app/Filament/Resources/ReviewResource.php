<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use App\Models\Hashtag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationLabel = 'Рецензии';
    
    protected static ?string $modelLabel = 'Рецензия';
    
    protected static ?string $pluralModelLabel = 'Рецензии';
    
    protected static ?string $navigationGroup = 'Контент';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\Select::make('book_id')
                            ->label('Книга')
                            ->relationship('book', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Книга, к которой относится рецензия'),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Пользователь')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Автор рецензии (если авторизован)'),
                        
                        Forms\Components\TextInput::make('rating')
                            ->label('Рейтинг')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10)
                            ->helperText('Рейтинг от 1 до 10'),
                        
                        Forms\Components\Select::make('parent_id')
                            ->label('Родительская рецензия')
                            ->relationship('parent', 'id')
                            ->searchable()
                            ->preload()
                            ->helperText('Если это ответ на рецензию'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Содержание')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('Текст рецензии')
                            ->required()
                            ->columnSpanFull(),
                        
                        Forms\Components\Toggle::make('contains_spoiler')
                            ->label('Содержит спойлеры')
                            ->helperText('Предупредить читателей о спойлерах'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Статус и модерация')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'active' => 'Активно',
                                'blocked' => 'Заблокировано',
                                'pending' => 'Ожидает модерации',
                            ])
                            ->default('active')
                            ->required(),
                        
                        Forms\Components\Textarea::make('moderation_reason')
                            ->label('Причина модерации')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Хештеги')
                    ->schema([
                        Forms\Components\TagsInput::make('hashtags')
                            ->label('Хештеги')
                            ->placeholder('Додайте хештеги')
                            ->suggestions(Hashtag::pluck('name')->toArray())
                            ->helperText('Введіть хештеги без символу #'),
                    ])
                    ->collapsible()
                    ->collapsed(true),

                Forms\Components\Section::make('Статистика')
                    ->schema([
                        Forms\Components\TextInput::make('replies_count')
                            ->label('Ответы')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ])
                    ->collapsible()
                    ->collapsed(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['parent.book', 'book', 'user']))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Книга')
                    ->sortable()
                    ->searchable()
                    ->limit(35)
                    ->badge()
                    ->color('primary')
                    ->description(fn (Review $record): string => $record->book->author_full_name ?? ''),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Автор')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->limit(20)
                    ->placeholder('Гость'),
                
                Tables\Columns\TextColumn::make('content')
                    ->label('Содержание')
                    ->limit(60)
                    ->html()
                    ->searchable()
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('rating')
                    ->label('Рейтинг')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 8 => 'success',
                        $state >= 6 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state): string => $state ? $state . '/10 ⭐' : '—')
                    ->placeholder('Без оценки'),
                
                Tables\Columns\TextColumn::make('parent_id')
                    ->label('Тип')
                    ->badge()
                    ->color(fn ($state): string => $state ? 'gray' : 'success')
                    ->formatStateUsing(function ($state, Review $record): string {
                        if ($state) {
                            $parentReview = $record->parent;
                            if ($parentReview && $parentReview->book) {
                                return 'Коментар до: ' . \Illuminate\Support\Str::limit($parentReview->book->title, 30);
                            }
                            return 'Коментар';
                        }
                        return 'Рецензія';
                    })
                    ->searchable(false)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('replies_count')
                    ->label('Ответы')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-chat-bubble-left'),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'blocked' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Активно',
                        'blocked' => 'Заблокировано',
                        'pending' => 'Ожидает модерации',
                        default => $state,
                    }),
                
                Tables\Columns\IconColumn::make('contains_spoiler')
                    ->label('Спойлер')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('hashtags.name')
                    ->label('Хештеги')
                    ->badge()
                    ->color('info')
                    ->separator(',')
                    ->limit(3)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'active' => 'Активно',
                        'blocked' => 'Заблокировано',
                        'pending' => 'Ожидает модерации',
                    ])
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('book')
                    ->label('Книга')
                    ->relationship('book', 'title')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('user')
                    ->label('Автор')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('is_reply')
                    ->label('Только ответы')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('parent_id')),
                
                Tables\Filters\Filter::make('is_main_review')
                    ->label('Только основные рецензии')
                    ->query(fn (Builder $query): Builder => $query->whereNull('parent_id')),
                
                Tables\Filters\Filter::make('high_rating')
                    ->label('Высокий рейтинг (8+)')
                    ->query(fn (Builder $query): Builder => $query->where('rating', '>=', 8)),
                
                Tables\Filters\TernaryFilter::make('contains_spoiler')
                    ->label('Спойлеры')
                    ->placeholder('Все')
                    ->trueLabel('Со спойлерами')
                    ->falseLabel('Без спойлеров'),
                
                Tables\Filters\SelectFilter::make('hashtag')
                    ->label('Хештег')
                    ->relationship('hashtags', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('approve')
                    ->label('Одобрить')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (Review $record) {
                        $record->approve(auth()->id(), 'Одобрено через админ панель');
                    })
                    ->visible(fn (Review $record): bool => $record->status !== 'active'),
                
                Tables\Actions\Action::make('block')
                    ->label('Заблокировать')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Причина блокировки')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Review $record, array $data) {
                        $record->block(auth()->id(), $data['reason']);
                    })
                    ->visible(fn (Review $record): bool => $record->status !== 'blocked'),
                
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Одобрить выбранные')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->approve(auth()->id(), 'Массовое одобрение через админ панель');
                            }
                        }),
                    
                    Tables\Actions\BulkAction::make('block_selected')
                        ->label('Заблокировать выбранные')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Причина блокировки')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $record->block(auth()->id(), $data['reason']);
                            }
                        }),
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'view' => Pages\ViewReview::route('/{record}'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
