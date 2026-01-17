<?php

namespace App\Filament\Resources\HashtagResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';

    protected static ?string $title = 'Рецензії з цим хештегом';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->modifyQueryUsing(function ($query) {
                return $query->orderBy('reviews.created_at', 'desc');
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Книга')
                    ->searchable()
                    ->limit(40)
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Автор')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->placeholder('Гость'),
                
                Tables\Columns\TextColumn::make('content')
                    ->label('Контент')
                    ->limit(60)
                    ->html()
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('rating')
                    ->label('Рейтинг')
                    ->numeric()
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 8 => 'success',
                        $state >= 6 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state): string => $state ? $state . '/10 ⭐' : '—'),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'blocked' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('reviews.created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'active' => 'Активно',
                        'blocked' => 'Заблокировано',
                        'pending' => 'Ожидает модерации',
                    ]),
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Переглянути')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('books.reviews.show', [$record->book, $record]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
