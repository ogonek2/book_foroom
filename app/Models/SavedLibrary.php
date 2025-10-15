<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedLibrary extends Model
{
    protected $table = 'saved_libraries';
    
    protected $fillable = [
        'user_id',
        'library_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Пользователь, который сохранил библиотеку
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Сохраненная библиотека
     */
    public function library(): BelongsTo
    {
        return $this->belongsTo(Library::class);
    }
}
