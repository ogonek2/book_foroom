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
        'slug',
        'biography',
        'birth_date',
        'death_date',
        'nationality',
        'photo',
        'website',
        'awards',
        'is_featured',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($author) {
            if (empty($author->slug)) {
                $author->slug = Str::slug($author->first_name . ' ' . $author->last_name);
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
        if ($this->photo) {
            // Если photo уже является полным URL (начинается с http/https), возвращаем как есть
            if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                return $this->photo;
            }
            // Иначе добавляем storage/ для локальных файлов
            return asset('storage/' . $this->photo);
        }
        return null;
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
