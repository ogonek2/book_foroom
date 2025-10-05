<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    protected $fillable = [
        'content',
        'rating',
        'book_id',
        'user_id',
        'parent_id',
        'replies_count',
        'status',
        'moderated_at',
        'moderated_by',
        'moderation_reason',
    ];

    protected $casts = [
        'rating' => 'integer',
        'replies_count' => 'integer',
        'moderated_at' => 'datetime',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Review::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Review::class, 'parent_id')->orderBy('created_at', 'desc');
    }

    /**
     * Рекурсивно загружает все ответы с их пользователями
     */
    public function repliesWithNested(): HasMany
    {
        return $this->hasMany(Review::class, 'parent_id')
            ->with(['user', 'repliesWithNested'])
            ->orderBy('created_at', 'desc');
    }

    public function likes(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\App\Models\Like::class, 'likeable');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Получает количество лайков
     */
    public function getLikesCountAttribute()
    {
        return $this->likes()->where('vote', 1)->count();
    }

    /**
     * Получает количество дизлайков
     */
    public function getDislikesCountAttribute()
    {
        return $this->likes()->where('vote', -1)->count();
    }

    /**
     * Проверяет, лайкнул ли текущий пользователь
     */
    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->where('vote', 1)->exists();
    }

    /**
     * Проверяет, дизлайкнул ли текущий пользователь
     */
    public function isDislikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->where('vote', -1)->exists();
    }

    /**
     * Проверяет, является ли рецензия ответом
     */
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Получает имя автора (пользователь или гость)
     */
    public function getAuthorName(): string
    {
        if ($this->user) {
            return $this->user->name;
        }
        
        return 'Гость';
    }

    /**
     * Проверяет, является ли рецензия гостевой
     */
    public function isGuest(): bool
    {
        return is_null($this->user_id);
    }

    /**
     * Получает счет голосов (лайки - дизлайки)
     */
    public function getScoreAttribute()
    {
        return ($this->likes_count ?? 0) - ($this->dislikes_count ?? 0);
    }

    /**
     * Обновляет счетчик ответов
     */
    public function updateRepliesCount(): void
    {
        $this->update([
            'replies_count' => $this->replies()->count()
        ]);
    }

    /**
     * Boot method для автоматического обновления счетчика ответов
     */
    protected static function boot()
    {
        parent::boot();

        // При создании ответа обновляем счетчик родительской рецензии
        static::created(function ($review) {
            if ($review->parent_id) {
                $review->parent->updateRepliesCount();
            }
        });

        // При удалении ответа обновляем счетчик родительской рецензии
        static::deleted(function ($review) {
            if ($review->parent_id) {
                $review->parent->updateRepliesCount();
            }
        });

        // Валидация: один пользователь может оставить только одну основную рецензию на книгу
        static::creating(function ($review) {
            if ($review->user_id && is_null($review->parent_id)) {
                $existingReview = static::where('book_id', $review->book_id)
                    ->where('user_id', $review->user_id)
                    ->whereNull('parent_id')
                    ->first();
                
                if ($existingReview) {
                    throw new \Exception('Ви вже залишили рецензію на цю книгу. Ви можете редагувати існуючу рецензію.');
                }
            }
        });
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
}
