<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_reading_status_id',
        'read_at',
        'language',
    ];

    protected $casts = [
        'read_at' => 'date',
    ];

    public function readingStatus(): BelongsTo
    {
        return $this->belongsTo(BookReadingStatus::class, 'book_reading_status_id');
    }
}
