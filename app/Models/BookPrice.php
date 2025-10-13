<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookPrice extends Model
{
    protected $fillable = [
        'book_id',
        'bookstore_id',
        'price',
        'currency',
        'product_url',
        'is_available',
        'last_updated',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'last_updated' => 'datetime',
    ];

    /**
     * Get the book that owns the price.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the bookstore that owns the price.
     */
    public function bookstore(): BelongsTo
    {
        return $this->belongsTo(Bookstore::class);
    }

    /**
     * Get formatted price attribute.
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', ' ') . ' ' . $this->currency;
    }
}
