<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserAwardResource\Pages;
use App\Filament\Resources\UserAwardResource\RelationManagers;
use App\Models\UserAward;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserAwardResource extends Resource
{
    protected static ?string $model = UserAward::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    
    protected static ?string $navigationGroup = 'Награды';
    
    protected static ?string $modelLabel = 'Назначение награды';
    
    protected static ?string $pluralModelLabel = 'Назначения наград';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Назначение награды')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Пользователь')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\Select::make('award_id')
                            ->label('Награда')
                            ->relationship('award', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\DateTimePicker::make('awarded_at')
                            ->label('Дата получения')
                            ->default(now())
                            ->required(),
                        
                        Forms\Components\Textarea::make('note')
                            ->label('Заметка')
                            ->rows(3)
                            ->helperText('Дополнительная информация о том, за что была получена награда'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Пользователь')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('award.name')
                    ->label('Награда')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\ImageColumn::make('award.image')
                    ->label('Изображение')
                    ->width(60)
                    ->height(60)
                    ->circular(),
                
                Tables\Columns\TextColumn::make('award.points')
                    ->label('Очки')
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('awarded_at')
                    ->label('Получена')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('note')
                    ->label('Заметка')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Пользователь')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('award_id')
                    ->label('Награда')
                    ->relationship('award', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('awarded_at')
                    ->form([
                        Forms\Components\DatePicker::make('awarded_from')
                            ->label('От даты'),
                        Forms\Components\DatePicker::make('awarded_until')
                            ->label('До даты'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['awarded_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('awarded_at', '>=', $date),
                            )
                            ->when(
                                $data['awarded_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('awarded_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('awarded_at', 'desc');
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
            'index' => Pages\ListUserAwards::route('/'),
            'create' => Pages\CreateUserAward::route('/create'),
            'edit' => Pages\EditUserAward::route('/{record}/edit'),
        ];
    }
}
