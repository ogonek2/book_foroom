<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    protected $fillable = [
        'reporter_id',
        'reported_user_id',
        'reportable_type',
        'reportable_id',
        'type',
        'reason',
        'status',
        'moderator_id',
        'moderator_comment',
        'processed_at',
        'content_url',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    // Типы жалоб
    const TYPE_SPAM = 'spam';
    const TYPE_HARASSMENT = 'harassment';
    const TYPE_INAPPROPRIATE = 'inappropriate';
    const TYPE_COPYRIGHT = 'copyright';
    const TYPE_FAKE = 'fake';
    const TYPE_HATE_SPEECH = 'hate_speech';
    const TYPE_VIOLENCE = 'violence';
    const TYPE_ADULT_CONTENT = 'adult_content';
    const TYPE_OTHER = 'other';

    // Статусы жалоб
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_DISMISSED = 'dismissed';

    /**
     * Пользователь, который подал жалобу
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Пользователь, на которого подали жалобу
     */
    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    /**
     * Модератор, который обработал жалобу
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * Полиморфное отношение к различным типам контента
     */
    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Получить массив всех типов жалоб
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_SPAM => 'Спам',
            self::TYPE_HARASSMENT => 'Булінг/травля',
            self::TYPE_INAPPROPRIATE => 'Неприємний контент',
            self::TYPE_COPYRIGHT => 'Порушення авторських прав',
            self::TYPE_FAKE => 'Фальшива інформація',
            self::TYPE_HATE_SPEECH => 'Розжигання ненависті',
            self::TYPE_VIOLENCE => 'Жахливість',
            self::TYPE_ADULT_CONTENT => 'Дорослий контент',
            self::TYPE_OTHER => 'Інше'
        ];
    }

    /**
     * Получить массив всех статусов
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Очікує розгляду',
            self::STATUS_REVIEWED => 'Розглянуто',
            self::STATUS_RESOLVED => 'Розв\'язано',
            self::STATUS_DISMISSED => 'Відхилене'
        ];
    }

    /**
     * Проверить, является ли жалоба новой
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Проверить, обработана ли жалоба
     */
    public function isProcessed(): bool
    {
        return $this->processed_at !== null;
    }

    /**
     * Получить URL типа жалобы
     */
    public function getTypeLabel(): string
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    /**
     * Получить URL статуса жалобы
     */
    public function getStatusLabel(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }
}
