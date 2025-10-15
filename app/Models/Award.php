<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Award extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'color',
        'points',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points' => 'integer',
        'sort_order' => 'integer'
    ];

    /**
     * Пользователи, которые получили эту награду
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_awards')
                    ->withPivot(['awarded_at', 'note'])
                    ->withTimestamps();
    }

    /**
     * Scope для активных наград
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для сортировки по порядку
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
