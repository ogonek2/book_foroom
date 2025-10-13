<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LibraryResource\Pages;
use App\Models\Library;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LibraryResource extends Resource
{
    protected static ?string $model = Library::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    
    protected static ?string $navigationLabel = 'Библиотеки';
    
    protected static ?string $modelLabel = 'Библиотека';
    
    protected static ?string $pluralModelLabel = 'Библиотеки';
    
    protected static ?string $navigationGroup = 'Контент';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Название')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Название подборки/библиотеки'),
                        
                        Forms\Components\Select::make('user_id')
                            ->label('Владелец')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Пользователь, создавший библиотеку'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Описание подборки'),
                        
                        Forms\Components\Toggle::make('is_private')
                            ->label('Приватная')
                            ->default(false)
                            ->helperText('Приватные библиотеки видны только владельцу'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Книги в библиотеке')
                    ->schema([
                        Forms\Components\Select::make('books')
                            ->label('Книги')
                            ->relationship('books', 'title')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Выберите книги для добавления в библиотеку'),
                    ])
                    ->collapsible()
                    ->collapsed(false),
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
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40)
                    ->description(fn (Library $record): string => $record->description ? \Str::limit($record->description, 60) : ''),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Владелец')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('books_count')
                    ->label('Книг')
                    ->counts('books')
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-book-open'),
                
                Tables\Columns\IconColumn::make('is_private')
                    ->label('Приватная')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-globe-alt')
                    ->trueColor('warning')
                    ->falseColor('success'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label('Владелец')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\TernaryFilter::make('is_private')
                    ->label('Приватные')
                    ->placeholder('Все библиотеки')
                    ->trueLabel('Только приватные')
                    ->falseLabel('Только публичные'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('toggle_private')
                    ->label(fn (Library $record): string => $record->is_private ? 'Сделать публичной' : 'Сделать приватной')
                    ->icon(fn (Library $record): string => $record->is_private ? 'heroicon-o-globe-alt' : 'heroicon-o-lock-closed')
                    ->color(fn (Library $record): string => $record->is_private ? 'success' : 'warning')
                    ->action(function (Library $record) {
                        $record->update(['is_private' => !$record->is_private]);
                    }),
                
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('make_public')
                        ->label('Сделать публичными')
                        ->icon('heroicon-o-globe-alt')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_private' => false]);
                            }
                        }),
                    
                    Tables\Actions\BulkAction::make('make_private')
                        ->label('Сделать приватными')
                        ->icon('heroicon-o-lock-closed')
                        ->color('warning')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_private' => true]);
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
            'index' => Pages\ListLibraries::route('/'),
            'create' => Pages\CreateLibrary::route('/create'),
            'view' => Pages\ViewLibrary::route('/{record}'),
            'edit' => Pages\EditLibrary::route('/{record}/edit'),
        ];
    }
}

