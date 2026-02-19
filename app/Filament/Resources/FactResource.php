<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FactResource\Pages;
use App\Filament\Resources\FactResource\RelationManagers;
use App\Models\Fact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FactResource extends Resource
{
    protected static ?string $model = Fact::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';
    
    protected static ?string $navigationLabel = 'Интересные факты';
    
    protected static ?string $modelLabel = 'Интересный факт';
    
    protected static ?string $pluralModelLabel = 'Интересные факты';
    
    protected static ?string $navigationGroup = 'Контент';
    
    protected static ?int $navigationSort = 7;
    
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

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
                            ->helperText('Книга, к которой относится факт'),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Автор')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Пользователь, добавивший факт'),
                        
                        Forms\Components\Textarea::make('content')
                            ->label('Содержание')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull()
                            ->helperText('Текст интересного факта'),
                        
                        Forms\Components\Toggle::make('is_public')
                            ->label('Публичный')
                            ->default(true)
                            ->helperText('Отображать факт публично'),
                        
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options([
                                'active' => 'Активно',
                                'blocked' => 'Заблокировано',
                                'pending' => 'Ожидает модерации',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),
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
                
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Книга')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->badge()
                    ->color('primary')
                    ->description(fn (Fact $record): string => $record->book->author_full_name ?? ''),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Автор')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('content')
                    ->label('Содержание')
                    ->limit(80)
                    ->searchable()
                    ->wrap(),
                
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Публичный')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
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
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
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
                
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Публичные')
                    ->placeholder('Все факты')
                    ->trueLabel('Только публичные')
                    ->falseLabel('Только приватные'),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'active' => 'Активно',
                        'blocked' => 'Заблокировано',
                        'pending' => 'Ожидает модерации',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('approve')
                    ->label('Одобрить')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (Fact $record) {
                        $record->approve(auth()->id(), 'Одобрено через админ панель');
                    })
                    ->visible(fn (Fact $record): bool => $record->status !== 'active'),
                
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
                    ->action(function (Fact $record, array $data) {
                        $record->block(auth()->id(), $data['reason']);
                    })
                    ->visible(fn (Fact $record): bool => $record->status !== 'blocked'),
                
                Tables\Actions\Action::make('toggle_public')
                    ->label(fn (Fact $record): string => $record->is_public ? 'Скрыть' : 'Опубликовать')
                    ->icon(fn (Fact $record): string => $record->is_public ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (Fact $record): string => $record->is_public ? 'warning' : 'success')
                    ->action(function (Fact $record) {
                        $record->update(['is_public' => !$record->is_public]);
                    }),
                
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('make_public')
                        ->label('Сделать публичными')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_public' => true]);
                            }
                        }),
                    
                    Tables\Actions\BulkAction::make('make_private')
                        ->label('Сделать приватными')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_public' => false]);
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
            'index' => Pages\ListFacts::route('/'),
            'create' => Pages\CreateFact::route('/create'),
            'view' => Pages\ViewFact::route('/{record}'),
            'edit' => Pages\EditFact::route('/{record}/edit'),
        ];
    }
}
