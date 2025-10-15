<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAward extends Model
{
    protected $fillable = [
        'user_id',
        'award_id',
        'awarded_at',
        'note'
    ];

    protected $casts = [
        'awarded_at' => 'datetime'
    ];

    /**
     * Пользователь, получивший награду
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Награда
     */
    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class);
    }
}
