<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HashtagResource\Pages;
use App\Filament\Resources\HashtagResource\RelationManagers;
use App\Models\Hashtag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HashtagResource extends Resource
{
    protected static ?string $model = Hashtag::class;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';
    
    protected static ?string $navigationLabel = 'Хештеги';
    
    protected static ?string $modelLabel = 'Хештег';
    
    protected static ?string $pluralModelLabel = 'Хештеги';
    
    protected static ?string $navigationGroup = 'Контент';
    
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна інформація')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Назва хештегу без символу #'),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('URL-friendly версія назви'),
                        Forms\Components\TextInput::make('usage_count')
                            ->label('Кількість використань')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('Автоматично оновлюється при використанні'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state): string => '#' . $state)
                    ->copyable()
                    ->copyMessage('Хештег скопійовано!'),
                
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('reviews_count')
                    ->label('Рецензії')
                    ->counts('reviews')
                    ->numeric()
                    ->badge()
                    ->color('success')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('discussions_count')
                    ->label('Обговорення')
                    ->counts('discussions')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Всього використань')
                    ->numeric()
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->default(fn ($record) => $record->reviews()->count() + $record->discussions()->count()),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('usage_count', 'desc')
            ->filters([
                Tables\Filters\Filter::make('has_reviews')
                    ->label('Тільки з рецензіями')
                    ->query(fn (Builder $query): Builder => $query->has('reviews')),
                
                Tables\Filters\Filter::make('has_discussions')
                    ->label('Тільки з обговореннями')
                    ->query(fn (Builder $query): Builder => $query->has('discussions')),
                
                Tables\Filters\Filter::make('popular')
                    ->label('Популярні (10+ використань)')
                    ->query(fn (Builder $query): Builder => $query->where('usage_count', '>=', 10)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            RelationManagers\ReviewsRelationManager::class,
            RelationManagers\DiscussionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHashtags::route('/'),
            'create' => Pages\CreateHashtag::route('/create'),
            'view' => Pages\ViewHashtag::route('/{record}'),
            'edit' => Pages\EditHashtag::route('/{record}/edit'),
        ];
    }
}
