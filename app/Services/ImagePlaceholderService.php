<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class ImagePlaceholderService
{
    public const KEY_BOOK_COVER = 'placeholders.book_cover';
    public const KEY_AUTHOR_PHOTO = 'placeholders.author_photo';

    public static function bookCoverUrl(): string
    {
        return self::getUrl(self::KEY_BOOK_COVER, asset('images/placeholders/book-cover.svg'));
    }

    public static function authorPhotoUrl(): string
    {
        return self::getUrl(self::KEY_AUTHOR_PHOTO, asset('images/placeholders/author-photo.svg'));
    }

    /**
     * Frontend-friendly config to avoid passing via controllers.
     */
    public static function forFrontend(): array
    {
        return [
            'bookCover' => self::bookCoverUrl(),
            'authorPhoto' => self::authorPhotoUrl(),
        ];
    }

    public static function set(string $key, ?string $value): void
    {
        SiteSetting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => blank($value) ? null : $value],
        );

        Cache::forget(self::cacheKey($key));
    }

    public static function getRaw(string $key): ?string
    {
        return Cache::rememberForever(self::cacheKey($key), function () use ($key) {
            return SiteSetting::query()->where('key', $key)->value('value');
        });
    }

    private static function getUrl(string $key, string $defaultUrl): string
    {
        $value = self::getRaw($key);
        if (blank($value)) {
            return $defaultUrl;
        }

        $value = trim((string) $value);

        // Full URL (CDN / external)
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        // Data URI
        if (str_starts_with($value, 'data:image/')) {
            return $value;
        }

        // Absolute path within app
        if (str_starts_with($value, '/')) {
            return url($value);
        }

        // Relative path (e.g. "images/foo.svg")
        return asset($value);
    }

    private static function cacheKey(string $key): string
    {
        return "site_setting:{$key}";
    }
}

