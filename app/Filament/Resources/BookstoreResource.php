<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookstoreResource\Pages;
use App\Models\Bookstore;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BookstoreResource extends Resource
{
    protected static ?string $model = Bookstore::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    
    protected static ?string $navigationLabel = 'Магазины';
    
    protected static ?string $modelLabel = 'Магазин';
    
    protected static ?string $pluralModelLabel = 'Магазины';
    
    protected static ?string $navigationGroup = 'Справочники';

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
                            ->helperText('Название магазина'),
                        
                        Forms\Components\TextInput::make('website_url')
                            ->label('Сайт')
                            ->url()
                            ->maxLength(500)
                            ->helperText('URL сайта магазина')
                            ->prefixIcon('heroicon-o-globe-alt'),
                        
                        Forms\Components\FileUpload::make('logo')
                            ->label('Логотип')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->disk('public')
                            ->directory('bookstore-logos')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(1024)
                            ->helperText('Максимум 1MB. Рекомендуемое соотношение сторон: 1:1 или 16:9'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Краткое описание магазина'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активный')
                            ->default(true)
                            ->helperText('Отображать магазин на сайте'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Логотип')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(url('/images/no-store.png')),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Bookstore $record): string => $record->description ? \Str::limit($record->description, 60) : ''),
                
                Tables\Columns\TextColumn::make('website_url')
                    ->label('Сайт')
                    ->url(fn (Bookstore $record): ?string => $record->website_url)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->limit(40)
                    ->placeholder('Не указан'),
                
                Tables\Columns\TextColumn::make('bookPrices_count')
                    ->label('Товаров')
                    ->counts('bookPrices')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-book-open'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активный')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активные')
                    ->placeholder('Все магазины')
                    ->trueLabel('Только активные')
                    ->falseLabel('Только неактивные'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn (Bookstore $record): string => $record->is_active ? 'Деактивировать' : 'Активировать')
                    ->icon(fn (Bookstore $record): string => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Bookstore $record): string => $record->is_active ? 'warning' : 'success')
                    ->action(function (Bookstore $record) {
                        $record->update(['is_active' => !$record->is_active]);
                    }),
                
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Активировать')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_active' => true]);
                            }
                        }),
                    
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Деактивировать')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_active' => false]);
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
            'index' => Pages\ListBookstores::route('/'),
            'create' => Pages\CreateBookstore::route('/create'),
            'view' => Pages\ViewBookstore::route('/{record}'),
            'edit' => Pages\EditBookstore::route('/{record}/edit'),
        ];
    }
}

