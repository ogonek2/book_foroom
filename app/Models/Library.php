<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Library extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_private',
        'user_id',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($library) {
            if (empty($library->slug)) {
                $baseSlug = Str::slug($library->name);
                $slug = $baseSlug;
                $counter = 1;
                
                // Проверяем уникальность slug
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $library->slug = $slug;
            }
        });

        static::updating(function ($library) {
            // Обновляем slug если изменилось название
            if ($library->isDirty('name') && empty($library->slug)) {
                $baseSlug = Str::slug($library->name);
                $slug = $baseSlug;
                $counter = 1;
                
                // Проверяем уникальность slug (исключая текущую запись)
                while (static::where('slug', $slug)->where('id', '!=', $library->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $library->slug = $slug;
            }
        });
    }

    /**
     * Получить имя маршрута для модели
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Владелец библиотеки
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Книги в библиотеке
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'library_book')
                    ->withTimestamps()
                    ->orderBy('library_book.created_at', 'desc');
    }

    /**
     * Количество книг в библиотеке
     */
    public function getBooksCountAttribute(): int
    {
        return $this->books()->count();
    }

    /**
     * Проверяет, является ли библиотека публичной
     */
    public function isPublic(): bool
    {
        return !$this->is_private;
    }

    /**
     * Проверяет, может ли пользователь просматривать библиотеку
     */
    public function canBeViewedBy(?User $user): bool
    {
        // Публичные библиотеки может просматривать любой
        if ($this->isPublic()) {
            return true;
        }

        // Приватные библиотеки может просматривать только владелец
        return $user && $user->id === $this->user_id;
    }

    /**
     * Проверяет, может ли пользователь редактировать библиотеку
     */
    public function canBeEditedBy(?User $user): bool
    {
        return $user && $user->id === $this->user_id;
    }

    /**
     * Пользователи, которые сохранили эту библиотеку
     */
    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_libraries')
                    ->withTimestamps()
                    ->orderBy('saved_libraries.created_at', 'desc');
    }

    /**
     * Пользователи, которые лайкнули эту библиотеку
     */
    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_liked_libraries')
                    ->withTimestamps();
    }

    /**
     * Лайки библиотеки
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_liked_libraries');
    }

    /**
     * Количество лайков
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    /**
     * Количество сохранений
     */
    public function getSavedCountAttribute(): int
    {
        return $this->savedByUsers()->count();
    }
}
