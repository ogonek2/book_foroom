<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Author extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'first_name_ua',
        'middle_name_ua',
        'last_name_ua',
        'first_name_eng',
        'middle_name_eng',
        'last_name_eng',
        'pseudonym',
        'synonyms',
        'slug',
        'biography',
        'birth_date',
        'death_date',
        'nationality',
        'photo',
        'website',
        'web_page',
        'awards',
        'is_featured',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
        'is_featured' => 'boolean',
        'synonyms' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($author) {
            if (empty($author->slug)) {
                $nameForSlug = $author->pseudonym ?: trim(($author->first_name ?? '') . ' ' . ($author->last_name ?? ''));
                $author->slug = Str::slug($nameForSlug);
            }
            // Синхронизация website и web_page
            if ($author->website && !$author->web_page) {
                $author->web_page = $author->website;
            } elseif ($author->web_page && !$author->website) {
                $author->website = $author->web_page;
            }
        });
        
        static::updating(function ($author) {
            // Синхронизация website и web_page при обновлении
            if ($author->isDirty('website') && !$author->isDirty('web_page')) {
                $author->web_page = $author->website;
            } elseif ($author->isDirty('web_page') && !$author->isDirty('website')) {
                $author->website = $author->web_page;
            }
        });
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function getFullNameAttribute(): string
    {
        $name = $this->first_name . ' ' . $this->last_name;
        if ($this->middle_name) {
            $name = $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
        }
        return $name;
    }

    public function getShortNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getBooksCountAttribute(): int
    {
        return $this->books()->count();
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        // Если изображение уже полный URL (CDN), возвращаем как есть
        if (str_starts_with($this->photo, 'http')) {
            return $this->photo;
        }

        // Если это локальный путь, добавляем базовый URL приложения
        if (str_starts_with($this->photo, 'storage/')) {
            return asset($this->photo);
        }

        return null;
    }

    /**
     * Get photo for display with fallback
     */
    public function getPhotoDisplayAttribute(): string
    {
        if ($this->photo_url) {
            return $this->photo_url;
        }

        // Fallback на изображение по умолчанию
        return asset('images/no-author.png');
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->birth_date) {
            return null;
        }
        
        $endDate = $this->death_date ?? now();
        return $this->birth_date->diffInYears($endDate);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByLetter($query, string $letter)
    {
        return $query->where('last_name', 'like', $letter . '%');
    }

    public function scopeByNationality($query, string $nationality)
    {
        return $query->where('nationality', $nationality);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', '%' . $search . '%')
              ->orWhere('last_name', 'like', '%' . $search . '%')
              ->orWhere('middle_name', 'like', '%' . $search . '%')
              ->orWhere('biography', 'like', '%' . $search . '%');
        });
    }
}
