<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'bio',
        'rating',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'rating' => 'decimal:1',
        ];
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }

    public function library(): HasMany
    {
        return $this->hasMany(UserLibrary::class);
    }

    public function savedBooks()
    {
        return $this->belongsToMany(Book::class, 'user_libraries');
    }

    /**
     * Библиотеки пользователя (доборки)
     */
    public function libraries(): HasMany
    {
        return $this->hasMany(Library::class);
    }

    /**
     * Публичные библиотеки пользователя
     */
    public function publicLibraries(): HasMany
    {
        return $this->libraries()->where('is_private', false);
    }

    /**
     * Приватные библиотеки пользователя
     */
    public function privateLibraries(): HasMany
    {
        return $this->libraries()->where('is_private', true);
    }

    public function readingStatuses(): HasMany
    {
        return $this->hasMany(BookReadingStatus::class);
    }

    public function readBooks()
    {
        return $this->belongsToMany(Book::class, 'book_reading_statuses')
                    ->wherePivot('status', 'read')
                    ->withPivot(['rating', 'review', 'started_at', 'finished_at'])
                    ->withTimestamps();
    }

    public function readingBooks()
    {
        return $this->belongsToMany(Book::class, 'book_reading_statuses')
                    ->wherePivot('status', 'reading')
                    ->withPivot(['rating', 'review', 'started_at', 'finished_at'])
                    ->withTimestamps();
    }

    public function wantToReadBooks()
    {
        return $this->belongsToMany(Book::class, 'book_reading_statuses')
                    ->wherePivot('status', 'want_to_read')
                    ->withPivot(['rating', 'review', 'started_at', 'finished_at'])
                    ->withTimestamps();
    }

    public function getRouteKeyName()
    {
        return 'username';
    }

    /**
     * Роли пользователя
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Разрешения пользователя (через роли)
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_roles')
                    ->join('role_permissions', 'user_roles.role_id', '=', 'role_permissions.role_id')
                    ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                    ->select('permissions.*');
    }

    /**
     * Проверяет, имеет ли пользователь определенную роль
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    /**
     * Проверяет, имеет ли пользователь любую из переданных ролей
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    /**
     * Проверяет, имеет ли пользователь все переданные роли
     */
    public function hasAllRoles(array $roles): bool
    {
        $userRoles = $this->roles()->pluck('slug')->toArray();
        return count(array_intersect($roles, $userRoles)) === count($roles);
    }

    /**
     * Проверяет, имеет ли пользователь определенное разрешение
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }

    /**
     * Проверяет, имеет ли пользователь любое из переданных разрешений
     */
    public function hasAnyPermission(array $permissions): bool
    {
        return $this->permissions()->whereIn('slug', $permissions)->exists();
    }

    /**
     * Проверяет, имеет ли пользователь все переданные разрешения
     */
    public function hasAllPermissions(array $permissions): bool
    {
        $userPermissions = $this->permissions()->pluck('slug')->toArray();
        return count(array_intersect($permissions, $userPermissions)) === count($permissions);
    }

    /**
     * Проверяет, является ли пользователь администратором
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['admin', 'super_admin']);
    }

    /**
     * Проверяет, является ли пользователь модератором
     */
    public function isModerator(): bool
    {
        return $this->hasAnyRole(['admin', 'moderator']);
    }

    /**
     * Назначает роль пользователю
     */
    public function assignRole(Role $role): void
    {
        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    /**
     * Удаляет роль у пользователя
     */
    public function removeRole(Role $role): void
    {
        $this->roles()->detach($role->id);
    }

    /**
     * Синхронизирует роли пользователя
     */
    public function syncRoles(array $roleIds): void
    {
        $this->roles()->sync($roleIds);
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        // Если аватар уже полный URL (CDN или локальный), возвращаем как есть
        if (str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }

        // Если это локальный путь, добавляем базовый URL приложения
        if (str_starts_with($this->avatar, 'storage/')) {
            return asset($this->avatar);
        }

        return null;
    }

    /**
     * Get avatar with fallback for display
     */
    public function getAvatarDisplayAttribute(): string
    {
        if ($this->avatar) {
            return $this->avatar_url ?: $this->avatar;
        }

        // Fallback на UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random&size=120';
    }
}
