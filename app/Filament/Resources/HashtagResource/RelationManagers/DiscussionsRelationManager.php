<?php

namespace App\Filament\Resources\HashtagResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DiscussionsRelationManager extends RelationManager
{
    protected static string $relationship = 'discussions';

    protected static ?string $title = 'Обговорення з цим хештегом';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Назва')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Автор')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('content')
                    ->label('Контент')
                    ->limit(60)
                    ->html()
                    ->wrap(),
                
                Tables\Columns\IconColumn::make('is_closed')
                    ->label('Закрито')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_pinned')
                    ->label('Закріплено')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('replies_count')
                    ->label('Відповіді')
                    ->numeric()
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Перегляди')
                    ->numeric()
                    ->badge()
                    ->color('gray'),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'closed' => 'gray',
                        'blocked' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('discussions.created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'active' => 'Активно',
                        'closed' => 'Закрито',
                        'blocked' => 'Заблокировано',
                        'pending' => 'Ожидает модерации',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_closed')
                    ->label('Закрито')
                    ->placeholder('Все')
                    ->trueLabel('Закрито')
                    ->falseLabel('Відкрито'),
                
                Tables\Filters\TernaryFilter::make('is_pinned')
                    ->label('Закріплено')
                    ->placeholder('Все')
                    ->trueLabel('Закріплено')
                    ->falseLabel('Не закріплено'),
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Переглянути')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('discussions.show', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
