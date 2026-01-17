<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'review_type',
        'opinion_type',
        'book_type',
        'language',
        'contains_spoiler',
        'is_draft',
    ];

    protected $casts = [
        'rating' => 'integer',
        'replies_count' => 'integer',
        'moderated_at' => 'datetime',
        'contains_spoiler' => 'boolean',
        'is_draft' => 'boolean',
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
        return $this->hasMany(Review::class, 'parent_id')
            ->where('is_draft', false)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Рекурсивно загружает все ответы с их пользователями
     */
    public function repliesWithNested(): HasMany
    {
        return $this->hasMany(Review::class, 'parent_id')
            ->where('is_draft', false)
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
                
                // Создаем уведомление о новом ответе
                if ($review->user_id) {
                    \App\Services\NotificationService::createReviewReplyNotification($review, $review->user);
                }
            }
        });

        // При удалении ответа обновляем счетчик родительской рецензии
        static::deleted(function ($review) {
            if ($review->parent_id) {
                $review->parent->updateRepliesCount();
            }
            
            // Оновлюємо лічильники хештегів при видаленні рецензії
            foreach ($review->hashtags as $hashtag) {
                $hashtag->decrementUsage();
            }
        });

        // Валидация времени между рецензиями теперь происходит в контроллере
        // Эта проверка удалена, чтобы разрешить множественные рецензии с интервалом
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
     * Пользователи, которые добавили рецензию в избранное
     */
    public function favoritedByUsers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite_reviews')
                    ->withTimestamps()
                    ->orderBy('favorite_reviews.created_at', 'desc');
    }

    /**
     * Проверяет, добавлена ли рецензия в избранное пользователем
     */
    public function isFavoritedBy($userId): bool
    {
        return $this->favoritedByUsers()->where('user_id', $userId)->exists();
    }

    /**
     * Хештеги рецензії
     */
    public function hashtags(): BelongsToMany
    {
        return $this->belongsToMany(Hashtag::class, 'hashtag_review')
            ->withTimestamps()
            ->orderBy('hashtag_review.created_at', 'desc');
    }

    /**
     * Синхронізує хештеги рецензії
     */
    public function syncHashtags(array $hashtagNames): void
    {
        // Отримуємо поточні хештеги перед видаленням
        $currentHashtagIds = $this->hashtags()->pluck('hashtags.id')->toArray();
        
        // Видаляємо старі хештеги та оновлюємо лічильники (тільки для опублікованих)
        foreach ($currentHashtagIds as $hashtagId) {
            $hashtag = Hashtag::find($hashtagId);
            if ($hashtag) {
                // Зменшуємо лічильник тільки якщо рецензія опублікована
                if (!$this->is_draft && $this->status === 'active') {
                    $hashtag->decrementUsage();
                }
            }
        }
        $this->hashtags()->detach();

        // Додаємо нові хештеги
        $uniqueHashtagNames = array_unique(array_filter($hashtagNames, function($name) {
            return !empty(trim($name));
        }));
        
        $hashtagIdsToAttach = [];
        foreach ($uniqueHashtagNames as $name) {
            $name = trim($name);
            if (empty($name)) {
                continue;
            }
            
            $hashtag = Hashtag::findOrCreate($name);
            $hashtagIdsToAttach[] = $hashtag->id;
        }
        
        // Прив'язуємо всі хештеги одразу
        if (!empty($hashtagIdsToAttach)) {
            $this->hashtags()->attach($hashtagIdsToAttach);
            
            // Оновлюємо лічильники тільки для опублікованих рецензій
            if (!$this->is_draft && $this->status === 'active') {
                foreach ($hashtagIdsToAttach as $hashtagId) {
                    $hashtag = Hashtag::find($hashtagId);
                    if ($hashtag) {
                        $hashtag->incrementUsage();
                    }
                }
            }
        }
    }

    /**
     * Витягує хештеги з контенту та синхронізує їх
     */
    public function extractAndSyncHashtags(): void
    {
        // Отримуємо текст з контенту (може містити HTML)
        $text = $this->content;
        
        // Витягуємо хештеги з тексту
        $hashtagNames = Hashtag::extractFromText($text);
        
        // Завжди синхронізуємо хештеги (навіть якщо масив порожній - це очистить старі хештеги)
        $this->syncHashtags($hashtagNames);
    }
}
