<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscussionMention extends Model
{
    protected $fillable = [
        'discussion_id',
        'user_id',
        'notified',
        'notified_at',
    ];

    protected $casts = [
        'notified' => 'boolean',
        'notified_at' => 'datetime',
    ];

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
