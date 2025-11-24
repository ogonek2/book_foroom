<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'content',
        'page_number',
        'is_public',
        'status',
        'moderated_at',
        'moderated_by',
        'moderation_reason',
        'is_draft',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'moderated_at' => 'datetime',
            'is_draft' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function likes(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\App\Models\Like::class, 'likeable');
    }

    /**
     * Проверяет, лайкнул ли пользователь цитату
     */
    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->where('vote', 1)->exists();
    }

    public function approve($moderatorId, $reason = null)
    {
        $this->update([
            'status' => 'active',
            'moderated_at' => now(),
            'moderated_by' => $moderatorId,
            'moderation_reason' => $reason,
        ]);
    }

    public function block($moderatorId, $reason = null)
    {
        $this->update([
            'status' => 'blocked',
            'moderated_at' => now(),
            'moderated_by' => $moderatorId,
            'moderation_reason' => $reason,
        ]);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isBlocked()
    {
        return $this->status === 'blocked';
    }

    /**
     * Пользователи, которые добавили цитату в избранное
     */
    public function favoritedByUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_quotes')
                    ->withTimestamps()
                    ->orderBy('favorite_quotes.created_at', 'desc');
    }

    /**
     * Проверяет, добавлена ли цитата в избранное пользователем
     */
    public function isFavoritedBy($userId): bool
    {
        return $this->favoritedByUsers()->where('user_id', $userId)->exists();
    }
}
