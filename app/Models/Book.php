<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Book extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'author',
        'author_id',
        'isbn',
        'publication_year',
        'publisher',
        'cover_image',
        'language',
        'pages',
        'rating',
        'reviews_count',
        'category_id',
        'is_featured',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'reviews_count' => 'integer',
        'pages' => 'integer',
        'publication_year' => 'integer',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($book) {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function updateRating()
    {
        // Получаем только основные рецензии (не ответы)
        $mainReviews = $this->reviews()->whereNull('parent_id');
        
        $avgRating = $mainReviews
            ->whereNotNull('rating')
            ->avg('rating');
        
        $this->update([
            'rating' => round($avgRating, 2),
            'reviews_count' => $mainReviews->count(),
        ]);
    }

    /**
     * Получить все основные рецензии (без ответов)
     */
    public function mainReviews(): HasMany
    {
        return $this->hasMany(Review::class)->whereNull('parent_id');
    }

    /**
     * Получить все ответы на рецензии
     */
    public function reviewReplies(): HasMany
    {
        return $this->hasMany(Review::class)->whereNotNull('parent_id');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function userLibraries(): HasMany
    {
        return $this->hasMany(UserLibrary::class);
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_libraries');
    }
}
