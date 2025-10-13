<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscussionReplyResource\Pages;
use App\Models\DiscussionReply;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DiscussionReplyResource extends Resource
{
    protected static ?string $model = DiscussionReply::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left';
    
    protected static ?string $navigationLabel = 'Ответы на обсуждения';
    
    protected static ?string $modelLabel = 'Ответ на обсуждение';
    
    protected static ?string $pluralModelLabel = 'Ответы на обсуждения';
    
    protected static ?string $navigationGroup = 'Контент';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\Select::make('discussion_id')
                            ->label('Обсуждение')
                            ->relationship('discussion', 'title')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Обсуждение, к которому относится ответ'),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Автор')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Пользователь, создавший ответ'),
                        
                        Forms\Components\Select::make('parent_id')
                            ->label('Родительский ответ')
                            ->relationship('parent', 'id')
                            ->searchable()
                            ->preload()
                            ->helperText('Если это ответ на другой ответ, выберите родительский'),
                        
                        Forms\Components\RichEditor::make('content')
                            ->label('Содержание')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Текст ответа'),
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
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Статистика')
                    ->schema([
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
                    ->columns(2)
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
                
                Tables\Columns\TextColumn::make('discussion.title')
                    ->label('Обсуждение')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Автор')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('content')
                    ->label('Содержание')
                    ->limit(60)
                    ->html()
                    ->searchable()
                    ->wrap(),
                
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
                
                Tables\Columns\TextColumn::make('parent_id')
                    ->label('Ответ на')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state): string => $state ? "Ответ #{$state}" : 'Основной')
                    ->toggleable(),
                
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
                
                Tables\Filters\SelectFilter::make('discussion')
                    ->label('Обсуждение')
                    ->relationship('discussion', 'title')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('user')
                    ->label('Автор')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('is_reply')
                    ->label('Только ответы на ответы')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('parent_id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('approve')
                    ->label('Одобрить')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (DiscussionReply $record) {
                        $record->approve(auth()->id(), 'Одобрено через админ панель');
                    })
                    ->visible(fn (DiscussionReply $record): bool => $record->status !== 'active'),
                
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
                    ->action(function (DiscussionReply $record, array $data) {
                        $record->block(auth()->id(), $data['reason']);
                    })
                    ->visible(fn (DiscussionReply $record): bool => $record->status !== 'blocked'),
                
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
            'index' => Pages\ListDiscussionReplies::route('/'),
            'create' => Pages\CreateDiscussionReply::route('/create'),
            'view' => Pages\ViewDiscussionReply::route('/{record}'),
            'edit' => Pages\EditDiscussionReply::route('/{record}/edit'),
        ];
    }
}

