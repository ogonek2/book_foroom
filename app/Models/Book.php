<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    /**
     * Get cover image URL with CDN support
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image) {
            return null;
        }

        // Если изображение уже полный URL (CDN), возвращаем как есть
        if (str_starts_with($this->cover_image, 'http')) {
            return $this->cover_image;
        }

        // Если это локальный путь, добавляем базовый URL приложения
        if (str_starts_with($this->cover_image, 'storage/')) {
            return asset($this->cover_image);
        }

        return null;
    }

    /**
     * Get cover image for display with fallback
     */
    public function getCoverImageDisplayAttribute(): string
    {
        if ($this->cover_image_url) {
            return $this->cover_image_url;
        }

        // Fallback на изображение по умолчанию
        return asset('images/no-cover.png');
    }

    public function userLibraries(): HasMany
    {
        return $this->hasMany(UserLibrary::class);
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_libraries');
    }

    /**
     * Библиотеки, в которых находится книга
     */
    public function libraries(): BelongsToMany
    {
        return $this->belongsToMany(Library::class, 'library_book')
                    ->withTimestamps()
                    ->orderBy('library_book.created_at', 'desc');
    }

    public function readingStatuses(): HasMany
    {
        return $this->hasMany(BookReadingStatus::class);
    }


    public function readByUsers()
    {
        return $this->belongsToMany(User::class, 'book_reading_statuses')
                    ->wherePivot('status', 'read')
                    ->withPivot(['rating', 'review', 'started_at', 'finished_at'])
                    ->withTimestamps();
    }

    public function readingByUsers()
    {
        return $this->belongsToMany(User::class, 'book_reading_statuses')
                    ->wherePivot('status', 'reading')
                    ->withPivot(['rating', 'review', 'started_at', 'finished_at'])
                    ->withTimestamps();
    }

    public function wantToReadByUsers()
    {
        return $this->belongsToMany(User::class, 'book_reading_statuses')
                    ->wherePivot('status', 'want_to_read')
                    ->withPivot(['rating', 'review', 'started_at', 'finished_at'])
                    ->withTimestamps();
    }

    /**
     * Получить статус чтения для конкретного пользователя
     */
    public function getReadingStatusForUser($userId)
    {
        return $this->readingStatuses()->where('user_id', $userId)->first();
    }

    /**
     * Получить полное имя автора для экспорта
     */
    public function getAuthorFullNameAttribute()
    {
        if ($this->author && is_object($this->author)) {
            return $this->author->first_name . ' ' . $this->author->last_name;
        }
        
        return $this->author ?? 'Не указан';
    }

    /**
     * Обновить средний рейтинг книги
     */
    public function updateRating()
    {
        // Получаем только основные рецензии (не ответы)
        $mainReviews = $this->reviews()->whereNull('parent_id');
        
        $avgRating = $mainReviews
            ->whereNotNull('rating')
            ->avg('rating');
        
        $this->update([
            'rating' => $avgRating ? round($avgRating * 2, 2) : 0, // Конвертируем в 10-балльную систему
            'reviews_count' => $mainReviews->count(),
        ]);
    }

    /**
     * Получить распределение рейтингов
     */
    public function getRatingDistribution()
    {
        $reviews = $this->reviews()
            ->whereNull('parent_id')
            ->whereNotNull('rating')
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->get();

        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $reviews->where('rating', $i)->first()->count ?? 0;
        }

        return $distribution;
    }

    /**
     * Получить статистику чтения
     */
    public function getReadingStats()
    {
        $stats = $this->readingStatuses()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'read' => $stats->get('read')->count ?? 0,
            'reading' => $stats->get('reading')->count ?? 0,
            'want_to_read' => $stats->get('want_to_read')->count ?? 0,
        ];
    }

    /**
     * Получить рейтинг пользователя для этой книги
     */
    public function getUserRating($userId)
    {
        $review = $this->reviews()
            ->where('user_id', $userId)
            ->whereNull('parent_id')
            ->first();

        return $review ? $review->rating : null;
    }

    /**
     * Получить отображаемый рейтинг (0-10)
     */
    public function getDisplayRatingAttribute()
    {
        return $this->rating ? round((float)$this->rating, 1) : 0;
    }

    /**
     * Получить звездный рейтинг (0-5)
     */
    public function getStarRatingAttribute()
    {
        return $this->rating ? round($this->rating / 2, 1) : 0;
    }

}
