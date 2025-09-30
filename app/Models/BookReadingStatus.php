<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookReadingStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'rating',
        'review',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'date',
        'finished_at' => 'date',
        'rating' => 'integer',
    ];

    // Константы для статусов
    const STATUS_READ = 'read';
    const STATUS_READING = 'reading';
    const STATUS_WANT_TO_READ = 'want_to_read';

    // Отношения
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    // Скоупы для фильтрации по статусам
    public function scopeRead($query)
    {
        return $query->where('status', self::STATUS_READ);
    }

    public function scopeReading($query)
    {
        return $query->where('status', self::STATUS_READING);
    }

    public function scopeWantToRead($query)
    {
        return $query->where('status', self::STATUS_WANT_TO_READ);
    }

    // Методы для получения статусов
    public static function getStatuses()
    {
        return [
            self::STATUS_READ => 'Прочитано',
            self::STATUS_READING => 'Читаю',
            self::STATUS_WANT_TO_READ => 'Буду читать',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    // Методы для обновления статуса
    public function markAsRead($rating = null, $review = null)
    {
        $this->update([
            'status' => self::STATUS_READ,
            'rating' => $rating,
            'review' => $review,
            'finished_at' => now(),
        ]);
    }

    public function markAsReading()
    {
        $this->update([
            'status' => self::STATUS_READING,
            'started_at' => now(),
        ]);
    }

    public function markAsWantToRead()
    {
        $this->update([
            'status' => self::STATUS_WANT_TO_READ,
            'started_at' => null,
            'finished_at' => null,
        ]);
    }
}