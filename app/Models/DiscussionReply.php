<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DiscussionReply extends Model
{
    protected $fillable = [
        'content',
        'discussion_id',
        'user_id',
        'parent_id',
        'replies_count',
        'likes_count',
        'status',
        'moderated_at',
        'moderated_by',
        'moderation_reason',
    ];

    protected $casts = [
        'replies_count' => 'integer',
        'likes_count' => 'integer',
        'moderated_at' => 'datetime',
    ];

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DiscussionReply::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(DiscussionReply::class, 'parent_id')
            ->orderBy('created_at', 'desc');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Рекурсивно загружает все ответы с их пользователями
     */
    public function repliesWithNested(): HasMany
    {
        return $this->hasMany(DiscussionReply::class, 'parent_id')
            ->with(['user', 'repliesWithNested'])
            ->orderBy('created_at', 'desc');
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
     * Проверяет, является ли ответ ответом на другой ответ
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
     * Проверяет, является ли ответ гостевой
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

        // При создании ответа обновляем счетчик родительского ответа
        static::created(function ($reply) {
            if ($reply->parent_id) {
                $reply->parent->updateRepliesCount();
            }
            // Обновляем счетчик ответов в обсуждении
            $reply->discussion->updateRepliesCount();
            $reply->discussion->updateLastActivity();
        });

        // При удалении ответа обновляем счетчик родительского ответа
        static::deleted(function ($reply) {
            if ($reply->parent_id) {
                $reply->parent->updateRepliesCount();
            }
            // Обновляем счетчик ответов в обсуждении
            $reply->discussion->updateRepliesCount();
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
