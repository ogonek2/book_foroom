<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    
    protected static ?string $navigationLabel = 'Жалобы';
    
    protected static ?string $modelLabel = 'Жалоба';
    
    protected static ?string $pluralModelLabel = 'Жалобы';
    
    protected static ?string $navigationGroup = 'Модерация';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Информация о жалобе')
                    ->schema([
                        Forms\Components\Select::make('reporter_id')
                            ->label('Кто пожаловался')
                            ->relationship('reporter', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        
                        Forms\Components\Select::make('reported_user_id')
                            ->label('На кого пожаловались')
                            ->relationship('reportedUser', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('reportable_type')
                            ->label('Тип контента')
                            ->disabled()
                            ->helperText('Тип контента (Review, Quote, Discussion и т.д.)'),
                        
                        Forms\Components\TextInput::make('reportable_id')
                            ->label('ID контента')
                            ->numeric()
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('content_url')
                            ->label('Ссылка на контент')
                            ->url()
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Детали жалобы')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Тип жалобы')
                            ->options(Report::getTypes())
                            ->required()
                            ->disabled(),
                        
                        Forms\Components\Textarea::make('reason')
                            ->label('Причина')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Модерация')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options(Report::getStatuses())
                            ->default(Report::STATUS_PENDING)
                            ->required()
                            ->live()
                            ->helperText('Измените статус после обработки жалобы'),
                        
                        Forms\Components\Select::make('moderator_id')
                            ->label('Модератор')
                            ->relationship('moderator', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Модератор, обрабатывающий жалобу'),
                        
                        Forms\Components\Textarea::make('moderator_comment')
                            ->label('Комментарий модератора')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Ваш комментарий по результатам рассмотрения'),
                        
                        Forms\Components\DateTimePicker::make('processed_at')
                            ->label('Дата обработки')
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Метаданные')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Дополнительные данные')
                            ->disabled(),
                    ])
                    ->collapsed()
                    ->collapsible(),
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
                
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип жалобы')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        Report::TYPE_SPAM => 'warning',
                        Report::TYPE_HARASSMENT => 'danger',
                        Report::TYPE_INAPPROPRIATE => 'warning',
                        Report::TYPE_HATE_SPEECH => 'danger',
                        Report::TYPE_VIOLENCE => 'danger',
                        Report::TYPE_ADULT_CONTENT => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Report::getTypes()[$state] ?? $state),
                
                Tables\Columns\TextColumn::make('reporter.name')
                    ->label('Кто пожаловался')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('reportedUser.name')
                    ->label('На кого')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('warning')
                    ->limit(20),
                
                Tables\Columns\TextColumn::make('reportable_type')
                    ->label('Тип контента')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'App\\Models\\Review' => 'Рецензия',
                        'App\\Models\\Quote' => 'Цитата',
                        'App\\Models\\Discussion' => 'Обсуждение',
                        'App\\Models\\DiscussionReply' => 'Ответ',
                        'App\\Models\\Fact' => 'Факт',
                        default => basename(str_replace('\\', '/', $state)),
                    }),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        Report::STATUS_PENDING => 'warning',
                        Report::STATUS_REVIEWED => 'info',
                        Report::STATUS_RESOLVED => 'success',
                        Report::STATUS_DISMISSED => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Report::getStatuses()[$state] ?? $state),
                
                Tables\Columns\TextColumn::make('moderator.name')
                    ->label('Модератор')
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->placeholder('Не назначен')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Обработано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('Не обработано')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options(Report::getStatuses())
                    ->multiple()
                    ->default([Report::STATUS_PENDING]),
                
                Tables\Filters\SelectFilter::make('type')
                    ->label('Тип жалобы')
                    ->options(Report::getTypes())
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('reportable_type')
                    ->label('Тип контента')
                    ->options([
                        'App\\Models\\Review' => 'Рецензии',
                        'App\\Models\\Quote' => 'Цитаты',
                        'App\\Models\\Discussion' => 'Обсуждения',
                        'App\\Models\\DiscussionReply' => 'Ответы',
                        'App\\Models\\Fact' => 'Факты',
                    ])
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('reporter')
                    ->label('Кто пожаловался')
                    ->relationship('reporter', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('reported_user')
                    ->label('На кого пожаловались')
                    ->relationship('reportedUser', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('moderator')
                    ->label('Модератор')
                    ->relationship('moderator', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('unprocessed')
                    ->label('Необработанные')
                    ->query(fn (Builder $query): Builder => $query->whereNull('processed_at')),
                
                Tables\Filters\Filter::make('my_reports')
                    ->label('Мои обработки')
                    ->query(fn (Builder $query): Builder => $query->where('moderator_id', auth()->id())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('review')
                    ->label('Рассмотреть')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Новый статус')
                            ->options([
                                Report::STATUS_REVIEWED => 'Рассмотрено',
                                Report::STATUS_RESOLVED => 'Разрешено',
                                Report::STATUS_DISMISSED => 'Отклонено',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('comment')
                            ->label('Комментарий')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Report $record, array $data) {
                        $record->update([
                            'status' => $data['status'],
                            'moderator_id' => auth()->id(),
                            'moderator_comment' => $data['comment'],
                            'processed_at' => now(),
                        ]);
                    })
                    ->visible(fn (Report $record): bool => $record->isPending()),
                
                Tables\Actions\Action::make('assign_to_me')
                    ->label('Взять в работу')
                    ->icon('heroicon-o-user')
                    ->color('success')
                    ->action(function (Report $record) {
                        $record->update([
                            'moderator_id' => auth()->id(),
                        ]);
                    })
                    ->visible(fn (Report $record): bool => !$record->moderator_id),
                
                Tables\Actions\Action::make('view_content')
                    ->label('Открыть контент')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn (Report $record): ?string => $record->content_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Report $record): bool => !empty($record->content_url)),
                
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('assign_to_me_bulk')
                        ->label('Взять в работу')
                        ->icon('heroicon-o-user')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['moderator_id' => auth()->id()]);
                            }
                        }),
                    
                    Tables\Actions\BulkAction::make('mark_as_reviewed')
                        ->label('Пометить как рассмотренные')
                        ->icon('heroicon-o-check')
                        ->color('info')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('comment')
                                ->label('Комментарий')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $record->update([
                                    'status' => Report::STATUS_REVIEWED,
                                    'moderator_id' => auth()->id(),
                                    'moderator_comment' => $data['comment'],
                                    'processed_at' => now(),
                                ]);
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
            'index' => Pages\ListReports::route('/'),
            'view' => Pages\ViewReport::route('/{record}'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', Report::STATUS_PENDING)->count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::where('status', Report::STATUS_PENDING)->count();
        
        if ($count > 10) {
            return 'danger';
        }
        
        if ($count > 5) {
            return 'warning';
        }
        
        return 'success';
    }
}

