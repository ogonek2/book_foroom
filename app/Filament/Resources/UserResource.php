<?php

namespace App\Filament\Resources;

use App\Helpers\CDNUploader;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
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
use App\Helpers\AwardNotificationHelper;
use App\Models\Award;
use Illuminate\Support\Carbon;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Пользователи';
    
    protected static ?string $modelLabel = 'Пользователь';
    
    protected static ?string $pluralModelLabel = 'Пользователи';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('username')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('avatar')
                    ->label('Аватар')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '1:1',
                    ])
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->helperText('Максимальный размер: 2MB. Поддерживаемые форматы: JPEG, PNG, GIF, WebP')
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
                        $cdnUrl = CDNUploader::uploadFile($file, 'avatars');

                        if (! $cdnUrl) {
                            throw ValidationException::withMessages([
                                'avatar' => 'Не вдалося зберегти аватар на CDN. Спробуйте ще раз.',
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
                Forms\Components\Textarea::make('bio')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('rating')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                
                Forms\Components\Section::make('Роли')
                    ->schema([
                        Forms\Components\CheckboxList::make('roles')
                            ->label('Роли пользователя')
                            ->relationship('roles', 'name')
                            ->options(
                                \App\Models\Role::active()
                                    ->orderBy('name')
                                    ->get()
                                    ->pluck('name', 'id')
                            )
                            ->columns(2)
                            ->gridDirection('row')
                            ->bulkToggleable()
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Роли')
                    ->badge()
                    ->color('primary')
                    ->separator(', ')
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
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
                Tables\Actions\Action::make('award')
                    ->label('Назначить награду')
                    ->icon('heroicon-o-trophy')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('award_id')
                            ->label('Награда')
                            ->relationship('awards', 'name')
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
                    ->action(function (User $record, array $data): void {
                        if ($record->hasAward($data['award_id'])) {
                            \Filament\Notifications\Notification::make()
                                ->title('Помилка')
                                ->body('У користувача вже є ця нагорода')
                                ->danger()
                                ->send();

                            return;
                        }

                        $award = Award::find($data['award_id']);

                        if (! $award) {
                            \Filament\Notifications\Notification::make()
                                ->title('Помилка')
                                ->body('Нагороду не знайдено')
                                ->danger()
                                ->send();

                            return;
                        }

                        $awardedAt = isset($data['awarded_at']) ? Carbon::parse($data['awarded_at']) : now();
                        $note = $data['note'] ?? null;

                        $record->awards()->attach($award->id, [
                            'awarded_at' => $awardedAt,
                            'note' => $note,
                        ]);

                        AwardNotificationHelper::sendAwardAssignedEmail($record, $award, $awardedAt, $note);

                        \Filament\Notifications\Notification::make()
                            ->title('Нагороду призначено')
                            ->body('Користувач отримав нагороду та повідомлення на email')
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
