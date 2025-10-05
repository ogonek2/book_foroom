<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Library extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_private',
        'user_id',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

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
}
