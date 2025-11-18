<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuthorResource\Pages;
use App\Filament\Resources\AuthorResource\RelationManagers;
use App\Helpers\CDNUploader;
use App\Models\Author;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    
    protected static ?string $navigationLabel = 'Авторы';
    
    protected static ?string $modelLabel = 'Автор';
    
    protected static ?string $pluralModelLabel = 'Авторы';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основная информация')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label('Имя (по умолчанию)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Фамилия (по умолчанию)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('middle_name')
                            ->label('Отчество (по умолчанию)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Украинские имена')
                    ->schema([
                        Forms\Components\TextInput::make('first_name_ua')
                            ->label('Имя (UA)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('middle_name_ua')
                            ->label('Отчество (UA)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name_ua')
                            ->label('Фамилия (UA)')
                            ->maxLength(255),
                    ])
                    ->columns(3)
                    ->collapsible(),
                
                Forms\Components\Section::make('Английские имена')
                    ->schema([
                        Forms\Components\TextInput::make('first_name_eng')
                            ->label('Имя (EN)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('middle_name_eng')
                            ->label('Отчество (EN)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name_eng')
                            ->label('Фамилия (EN)')
                            ->maxLength(255),
                    ])
                    ->columns(3)
                    ->collapsible(),
                
                Forms\Components\Section::make('Дополнительная информация')
                    ->schema([
                        Forms\Components\TextInput::make('pseudonym')
                            ->label('Псевдоним')
                            ->maxLength(255),
                        Forms\Components\TagsInput::make('synonyms')
                            ->label('Синонимы')
                            ->placeholder('Добавить синоним')
                            ->helperText('Нажмите Enter после каждого синонима'),
                        Forms\Components\Textarea::make('biography')
                            ->label('Биография')
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Дата рождения'),
                        Forms\Components\DatePicker::make('death_date')
                            ->label('Дата смерти'),
                        Forms\Components\TextInput::make('nationality')
                            ->label('Национальность')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Forms\Components\FileUpload::make('photo')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '1:1',
                        '4:3',
                        '16:9',
                    ])
                    ->helperText('Загрузите фотографию автора (будет сохранена на CDN)')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->maxSize(2048)
                    ->fetchFileInformation(false)
                    ->afterStateHydrated(function (Forms\Components\FileUpload $component, $state): void {
                        if (blank($state)) {
                            $component->state([]);

                            return;
                        }

                        $component->state([
                            (string) Str::uuid() => $state,
                        ]);
                    })
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                        $cdnUrl = CDNUploader::uploadFile($file, 'authors/photos');

                        if (! $cdnUrl) {
                            throw ValidationException::withMessages([
                                'photo' => 'Не вдалося зберегти фото автора на CDN. Спробуйте ще раз.',
                            ]);
                        }

                        return $cdnUrl;
                    })
                    ->getUploadedFileUsing(function (Forms\Components\FileUpload $component, string $file, string | array | null $storedFileNames): ?array {
                        if (blank($file)) {
                            return null;
                        }

                        $url = $file;
                        $name = null;

                        if (is_array($storedFileNames)) {
                            $name = $storedFileNames[$file] ?? null;
                        } elseif (is_string($storedFileNames)) {
                            $name = $storedFileNames;
                        }

                        $name ??= basename(parse_url($url, PHP_URL_PATH) ?: $url);

                        return [
                            'name' => $name,
                            'size' => null,
                            'type' => null,
                            'url' => $url,
                        ];
                    })
                    ->deleteUploadedFileUsing(function ($file): void {
                        $fileUrl = null;
                        
                        // Обрабатываем разные типы данных, которые может передать Filament
                        if (is_string($file)) {
                            $fileUrl = $file;
                        } elseif (is_array($file) && isset($file['url'])) {
                            $fileUrl = $file['url'];
                        } elseif (is_object($file) && isset($file->url)) {
                            $fileUrl = $file->url;
                        }
                        
                        if ($fileUrl && filter_var($fileUrl, FILTER_VALIDATE_URL)) {
                            CDNUploader::deleteFromBunnyCDN($fileUrl);
                        }
                    }),
                Forms\Components\Section::make('Контакты и награды')
                    ->schema([
                        Forms\Components\TextInput::make('website')
                            ->label('Веб-сайт')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('web_page')
                            ->label('Веб-страница (альтернативное)')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('awards')
                            ->label('Награды')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Рекомендуемый автор')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(url('/images/no-author.png')),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('death_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name_ua')
                    ->label('Имя (UA)')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('last_name_ua')
                    ->label('Фамилия (UA)')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pseudonym')
                    ->label('Псевдоним')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nationality')
                    ->label('Национальность')
                    ->searchable(),
                Tables\Columns\TextColumn::make('website')
                    ->label('Веб-сайт')
                    ->url(fn ($record) => $record->website ?: null)
                    ->openUrlInNewTab()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Рекомендуемый')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuthors::route('/'),
            'create' => Pages\CreateAuthor::route('/create'),
            'edit' => Pages\EditAuthor::route('/{record}/edit'),
        ];
    }
}
