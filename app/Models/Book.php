<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class Book extends Model
{
    protected $fillable = [
        'title',
        'book_name_ua',
        'slug',
        'annotation',
        'annotation_source',
        'author',
        'author_id',
        'isbn',
        'publication_year',
        'first_publish_year',
        'publisher',
        'cover_image',
        'language',
        'original_language',
        'pages',
        'rating',
        'reviews_count',
        'is_featured',
        'interesting_facts',
        'synonyms',
        'series',
        'series_number',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'reviews_count' => 'integer',
        'pages' => 'integer',
        'publication_year' => 'integer',
        'first_publish_year' => 'integer',
        'series_number' => 'integer',
        'is_featured' => 'boolean',
        'interesting_facts' => 'array',
        'synonyms' => 'array',
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

    /**
     * Книга может иметь несколько категорий (Many-to-Many)
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'book_category')
                    ->withTimestamps();
    }

    /**
     * Получить первую категорию книги (для обратной совместимости)
     */
    public function getCategoryAttribute()
    {
        return $this->categories()->first();
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
     * Получить рейтинг пользователя для книги
     */
    public function getUserRating($userId)
    {
        $readingStatus = $this->readingStatuses()->where('user_id', $userId)->first();
        return $readingStatus ? $readingStatus->rating : null;
    }

    /**
     * Получить полное имя автора для экспорта
     */
    public function getAuthorFullNameAttribute()
    {
        if ($this->author && is_object($this->author)) {
            return $this->author->first_name . ' ' . $this->author->last_name;
        }
        
        return $this->author ?? 'Не вказано';
    }

    /**
     * Обновить средний рейтинг книги
     */
    public function updateRating()
    {
        // Получаем рейтинги из BookReadingStatus (основной источник рейтингов)
        $avgRating = $this->readingStatuses()
            ->whereNotNull('rating')
            ->avg('rating');
        
        // Получаем количество рецензий (не ответов, не черновиков)
        $mainReviews = $this->reviews()
            ->whereNull('parent_id')
            ->where('is_draft', false);
        
        $this->update([
            'rating' => $avgRating ? round($avgRating, 2) : 0, // Рейтинги уже в 10-балльной системе
            'reviews_count' => $mainReviews->count(),
        ]);
    }

    /**
     * Получить распределение рейтингов
     */
    public function getRatingDistribution()
    {
        // Получаем рейтинги из BookReadingStatus (основной источник)
        $readingStatuses = $this->readingStatuses()
            ->whereNotNull('rating')
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->get();

        $distribution = [];
        for ($i = 1; $i <= 10; $i++) {
            $distribution[$i] = $readingStatuses->where('rating', $i)->first()->count ?? 0;
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
     * Аксессор для обратной совместимости с полем description
     */
    public function getDescriptionAttribute(): ?string
    {
        return $this->annotation;
    }

    public function setDescriptionAttribute($value): void
    {
        $this->attributes['annotation'] = $value;
    }

    public function getSynonymsAttribute($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value, fn ($item) => $item !== null && $item !== ''));
        }

        if (is_null($value) || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        if (is_array($decoded)) {
            return array_values(array_filter($decoded, fn ($item) => $item !== null && $item !== ''));
        }

        return [];
    }

    public function setSynonymsAttribute($value): void
    {
        if (is_string($value)) {
            $value = preg_split('/[,;|]/u', $value) ?: [];
        }

        if (is_array($value)) {
            $clean = array_values(array_filter(array_map('trim', $value), fn ($item) => $item !== ''));
            $this->attributes['synonyms'] = empty($clean)
                ? null
                : json_encode($clean, JSON_UNESCAPED_UNICODE);
            return;
        }

        $this->attributes['synonyms'] = null;
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

    /**
     * Get the facts for the book.
     */
    public function facts()
    {
        return $this->hasMany(Fact::class);
    }

    /**
     * Get the book prices for the book.
     */
    public function bookPrices()
    {
        return $this->hasMany(BookPrice::class);
    }

    /**
     * Отримати кешовані дані книги або завантажити з бази
     */
    public static function getCachedBookData($bookId)
    {
        $cacheKey = "book_data_{$bookId}";
        
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($bookId) {
            $book = self::with(['author', 'categories'])->find($bookId);
            
            if (!$book) {
                return null;
            }
            
            return [
                'id' => $book->id,
                'slug' => $book->slug,
                'title' => $book->title,
                'book_name_ua' => $book->book_name_ua,
                'author' => $book->author ? ($book->author->first_name ?? $book->author) : 'Автор невідомий',
                'author_id' => $book->author_id,
                'cover_image' => $book->cover_image,
                'rating' => (float) $book->rating,
                'reviews_count' => (int) $book->reviews_count,
                'pages' => (int) $book->pages,
                'publication_year' => $book->publication_year,
                'categories' => $book->categories->pluck('name')->toArray(),
                'cached_at' => now()->toIso8601String(),
            ];
        });
    }

    /**
     * Кешує дані книги для швидкого оновлення компонентів
     */
    public function cacheBookData()
    {
        $this->load(['author', 'categories']);
        
        $cacheKey = "book_data_{$this->id}";
        $cacheData = [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'book_name_ua' => $this->book_name_ua,
            'author' => $this->author ? ($this->author->first_name ?? $this->author) : 'Автор невідомий',
            'author_id' => $this->author_id,
            'cover_image' => $this->cover_image,
            'rating' => (float) $this->rating,
            'reviews_count' => (int) $this->reviews_count,
            'pages' => (int) $this->pages,
            'publication_year' => $this->publication_year,
            'categories' => $this->categories->pluck('name')->toArray(),
            'cached_at' => now()->toIso8601String(),
        ];

        // Кешуємо на 24 години
        Cache::put($cacheKey, $cacheData, now()->addHours(24));
    }

    /**
     * Очищає кеш книги
     */
    public function clearBookCache()
    {
        $cacheKey = "book_data_{$this->id}";
        Cache::forget($cacheKey);
    }

}
