<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    /**
     * Категория может иметь несколько книг (Many-to-Many)
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_category')
                    ->withTimestamps();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
