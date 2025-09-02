<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    protected $fillable = [
        'content',
        'topic_id',
        'user_id',
        'parent_id',
        'is_solution',
        'likes_count',
    ];

    protected $casts = [
        'is_solution' => 'boolean',
        'likes_count' => 'integer',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Post::class, 'parent_id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function markAsSolution()
    {
        // Remove solution from other posts in the same topic
        $this->topic->posts()->where('is_solution', true)->update(['is_solution' => false]);
        
        // Mark this post as solution
        $this->update(['is_solution' => true]);
    }
}
