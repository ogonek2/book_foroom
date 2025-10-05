<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
    ];

    /**
     * Get the user that owns the rating.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book that owns the rating.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
