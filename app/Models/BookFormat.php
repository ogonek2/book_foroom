<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class BookFormat extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (BookFormat $format) {
            if (empty($format->slug)) {
                $format->slug = Str::slug($format->name);
            }
        });
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_format')
            ->withTimestamps();
    }
}
