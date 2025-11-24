<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Discussion extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'user_id',
        'is_closed',
        'is_pinned',
        'views_count',
        'replies_count',
        'likes_count',
        'last_activity_at',
        'status',
        'moderated_at',
        'moderated_by',
        'moderation_reason',
        'is_draft',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'is_pinned' => 'boolean',
        'views_count' => 'integer',
        'replies_count' => 'integer',
        'likes_count' => 'integer',
        'last_activity_at' => 'datetime',
        'moderated_at' => 'datetime',
        'is_draft' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($discussion) {
            if (empty($discussion->slug)) {
                $baseSlug = Str::slug($discussion->title);
                $slug = $baseSlug;
                $counter = 1;
                
                // Проверяем уникальность slug
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $discussion->slug = $slug;
            }
            $discussion->last_activity_at = now();
        });

        static::updating(function ($discussion) {
            // Обновляем slug если изменился заголовок
            if ($discussion->isDirty('title')) {
                $baseSlug = Str::slug($discussion->title);
                $slug = $baseSlug;
                $counter = 1;
                
                // Проверяем уникальность slug (исключая текущую запись)
                while (static::where('slug', $slug)->where('id', '!=', $discussion->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $discussion->slug = $slug;
            }
            $discussion->last_activity_at = now();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(DiscussionReply::class)->orderBy('created_at', 'asc');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function getRouteKeyName()
    {
        return 'id';
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function updateLastActivity()
    {
        $this->update(['last_activity_at' => now()]);
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
     * Проверяет, может ли пользователь редактировать обсуждение
     */
    public function canBeEditedBy($user): bool
    {
        if (!$user) return false;
        
        return $this->user_id === $user->id || $user->isModerator();
    }

    /**
     * Проверяет, может ли пользователь закрыть обсуждение
     */
    public function canBeClosedBy($user): bool
    {
        if (!$user) return false;
        
        return $this->user_id === $user->id || $user->isModerator();
    }

    /**
     * Проверяет, может ли пользователь отвечать в обсуждении
     */
    public function canBeRepliedBy($user): bool
    {
        if (!$user) return false;
        
        return !$this->is_closed;
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
