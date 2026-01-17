<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Hashtag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'usage_count',
    ];

    protected $casts = [
        'usage_count' => 'integer',
    ];

    /**
     * Рецензії, що містять цей хештег
     */
    public function reviews(): BelongsToMany
    {
        return $this->belongsToMany(Review::class, 'hashtag_review')
            ->withTimestamps()
            ->orderBy('reviews.created_at', 'desc');
    }

    /**
     * Обговорення, що містять цей хештег
     */
    public function discussions(): BelongsToMany
    {
        return $this->belongsToMany(Discussion::class, 'hashtag_discussion')
            ->withTimestamps()
            ->orderBy('discussions.created_at', 'desc');
    }

    /**
     * Отримує загальну кількість використань (рецензії + обговорення)
     */
    public function getTotalUsageCountAttribute(): int
    {
        return $this->reviews()->count() + $this->discussions()->count();
    }

    /**
     * Створює або знаходить хештег за назвою
     */
    public static function findOrCreate(string $name): self
    {
        // Видаляємо # якщо є
        $name = ltrim($name, '#');
        $name = trim($name);
        
        // Створюємо slug
        $slug = Str::slug($name);
        
        $hashtag = self::where('slug', $slug)->first();
        
        if (!$hashtag) {
            $hashtag = self::create([
                'name' => $name,
                'slug' => $slug,
                'usage_count' => 0,
            ]);
        }
        
        return $hashtag;
    }

    /**
     * Оновлює лічильник використань
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Зменшує лічильник використань
     */
    public function decrementUsage(): void
    {
        if ($this->usage_count > 0) {
            $this->decrement('usage_count');
        }
    }

    /**
     * Витягує хештеги з тексту
     */
    public static function extractFromText(string $text): array
    {
        // Видаляємо HTML теги перед пошуком хештегів
        $text = strip_tags($text);
        
        // Паттерн для пошуку хештегів: # після якого йдуть літери (латиниця/кирилиця), цифри та підкреслення
        // Підтримуємо українські літери: а-яА-ЯёЁіІїЇєЄ
        preg_match_all('/#([a-zA-Zа-яА-ЯёЁіІїЇєЄ0-9_]+)/u', $text, $matches);
        
        if (empty($matches[1])) {
            return [];
        }
        
        // Видаляємо дублікати та нормалізуємо
        $hashtags = array_unique($matches[1]);
        
        // Нормалізуємо (прибираємо порожні значення)
        return array_filter(array_map(function($tag) {
            $tag = trim($tag);
            return !empty($tag) ? $tag : null;
        }, $hashtags));
    }
}
