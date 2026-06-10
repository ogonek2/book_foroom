<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use App\Services\CategoryTreeService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Категорії';

    protected static ?string $modelLabel = 'Категорія';

    protected static ?string $pluralModelLabel = 'Категорії';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->label('Батьківська категорія')
                    ->options(function (?Category $record): array {
                        $categories = Category::query()
                            ->orderBy('sort_order')
                            ->orderBy('name')
                            ->get();

                        $exclude = [];
                        if ($record) {
                            $descendants = CategoryTreeService::descendantIdsMap($categories);
                            $exclude = array_merge([$record->id], $descendants[$record->id] ?? []);
                        }

                        $options = [];
                        foreach ($categories as $category) {
                            if (in_array($category->id, $exclude, true)) {
                                continue;
                            }

                            $prefix = '';
                            $parent = $category->parent_id
                                ? $categories->firstWhere('id', $category->parent_id)
                                : null;
                            if ($parent) {
                                $prefix = '— ';
                            }

                            $options[$category->id] = $prefix . $category->name;
                        }

                        return $options;
                    })
                    ->searchable()
                    ->nullable()
                    ->helperText('Залиште порожнім для кореневої категорії'),
                Forms\Components\TextInput::make('name')
                    ->label('Назва')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, ?string $state, Forms\Set $set) {
                        if ($operation !== 'create' || !$state) {
                            return;
                        }
                        $set('slug', \Illuminate\Support\Str::slug($state));
                    }),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('color')
                    ->required()
                    ->maxLength(7)
                    ->default('#3B82F6'),
                Forms\Components\TextInput::make('icon')
                    ->maxLength(255),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Порядок')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('Активна')
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->formatStateUsing(function (string $state, Category $record): string {
                        return ($record->parent_id ? '— ' : '') . $state;
                    }),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Батьківська')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('books_count')
                    ->label('Книг')
                    ->counts('books'),
                Tables\Columns\TextColumn::make('children_count')
                    ->label('Підкатегорій')
                    ->counts('children'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Активна'),
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Батьківська')
                    ->relationship('parent', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
