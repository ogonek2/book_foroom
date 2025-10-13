<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscussionResource\Pages;
use App\Models\Discussion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DiscussionResource extends Resource
{
    protected static ?string $model = Discussion::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationLabel = 'Обсуждения';
    
    protected static ?string $modelLabel = 'Обсуждение';
    
    protected static ?string $pluralModelLabel = 'Обсуждения';
    
    protected static ?string $navigationGroup = 'Контент';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Заголовок')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            })
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('slug')
                            ->label('URL-адрес')
                            ->required()
                            ->maxLength(255)
                            ->unique(Discussion::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->helperText('URL-дружественный идентификатор обсуждения')
                            ->columnSpanFull(),
                        
                        Forms\Components\RichEditor::make('content')
                            ->label('Содержание')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Основной текст обсуждения'),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Автор')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Пользователь, создавший обсуждение'),
                    ])
                    ->columns(2),

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
                            ->columnSpanFull()
                            ->helperText('Укажите причину блокировки или модерации'),
                        
                        Forms\Components\Toggle::make('is_pinned')
                            ->label('Закреплено')
                            ->helperText('Закрепить обсуждение вверху списка'),
                        
                        Forms\Components\Toggle::make('is_closed')
                            ->label('Закрыто')
                            ->helperText('Закрыть обсуждение для новых ответов'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Статистика')
                    ->schema([
                        Forms\Components\TextInput::make('views_count')
                            ->label('Просмотры')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('replies_count')
                            ->label('Ответы')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('likes_count')
                            ->label('Лайки')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ])
                    ->columns(3)
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
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50)
                    ->description(fn (Discussion $record): string => \Str::limit(strip_tags($record->content), 100)),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Автор')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),
                
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
                
                Tables\Columns\IconColumn::make('is_pinned')
                    ->label('Закреплено')
                    ->boolean()
                    ->trueIcon('heroicon-o-bookmark')
                    ->falseIcon('heroicon-o-bookmark')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                
                Tables\Columns\IconColumn::make('is_closed')
                    ->label('Закрыто')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('danger')
                    ->falseColor('success'),
                
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Просмотры')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-eye'),
                
                Tables\Columns\TextColumn::make('replies_count')
                    ->label('Ответы')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-chat-bubble-left'),
                
                Tables\Columns\TextColumn::make('likes_count')
                    ->label('Лайки')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-heart'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('last_activity_at')
                    ->label('Последняя активность')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                
                Tables\Filters\SelectFilter::make('user')
                    ->label('Автор')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\TernaryFilter::make('is_pinned')
                    ->label('Закреплено')
                    ->placeholder('Все обсуждения')
                    ->trueLabel('Только закрепленные')
                    ->falseLabel('Только незакрепленные'),
                
                Tables\Filters\TernaryFilter::make('is_closed')
                    ->label('Закрыто')
                    ->placeholder('Все обсуждения')
                    ->trueLabel('Только закрытые')
                    ->falseLabel('Только открытые'),
                
                Tables\Filters\Filter::make('popular')
                    ->label('Популярные')
                    ->query(fn (Builder $query): Builder => $query->where('views_count', '>=', 100)),
                
                Tables\Filters\Filter::make('active_discussions')
                    ->label('Активные дискуссии')
                    ->query(fn (Builder $query): Builder => $query->where('replies_count', '>=', 5)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('approve')
                    ->label('Одобрить')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (Discussion $record) {
                        $record->approve(auth()->id(), 'Одобрено через админ панель');
                    })
                    ->visible(fn (Discussion $record): bool => $record->status !== 'active'),
                
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
                    ->action(function (Discussion $record, array $data) {
                        $record->block(auth()->id(), $data['reason']);
                    })
                    ->visible(fn (Discussion $record): bool => $record->status !== 'blocked'),
                
                Tables\Actions\Action::make('toggle_pin')
                    ->label(fn (Discussion $record): string => $record->is_pinned ? 'Открепить' : 'Закрепить')
                    ->icon('heroicon-o-bookmark')
                    ->color('warning')
                    ->action(function (Discussion $record) {
                        $record->update(['is_pinned' => !$record->is_pinned]);
                    }),
                
                Tables\Actions\Action::make('toggle_close')
                    ->label(fn (Discussion $record): string => $record->is_closed ? 'Открыть' : 'Закрыть')
                    ->icon('heroicon-o-lock-closed')
                    ->color(fn (Discussion $record): string => $record->is_closed ? 'success' : 'danger')
                    ->action(function (Discussion $record) {
                        $record->update(['is_closed' => !$record->is_closed]);
                    }),
                
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
            'index' => Pages\ListDiscussions::route('/'),
            'create' => Pages\CreateDiscussion::route('/create'),
            'view' => Pages\ViewDiscussion::route('/{record}'),
            'edit' => Pages\EditDiscussion::route('/{record}/edit'),
        ];
    }
}

