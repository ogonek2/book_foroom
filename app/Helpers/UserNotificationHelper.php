<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserNotificationHelper
{
    /**
     * Отправка универсального уведомления:
     *  - создает запись в notifications (онлайн-уведомление)
     *  - при необходимости отправляет email
     *
     * @param string               $eventKey   тип события (review_reply, discussion_comment_reply, review_like и т.д.)
     * @param \App\Models\User     $recipient  кому отправляем
     * @param \App\Models\User     $sender     кто инициировал
     * @param array                $payload    дополнительные данные (ids, slugs, тексты)
     * @param \Illuminate\Database\Eloquent\Model|null $notifiable связанная модель (Review, Discussion, Quote, Fact, DiscussionReply и т.п.)
     */
    public static function send(
        string $eventKey,
        User $recipient,
        User $sender,
        array $payload = [],
        ?Model $notifiable = null
    ): void {
        // Не уведомляем самого себя
        if ($recipient->id === $sender->id) {
            return;
        }

        $config = self::eventsConfig()[$eventKey] ?? null;
        if (!$config) {
            Log::warning('UserNotificationHelper: unknown event key', [
                'event' => $eventKey,
                'recipient_id' => $recipient->id,
                'sender_id' => $sender->id,
            ]);
            return;
        }

        // Проверяем пользовательские настройки для определенных типов событий
        if (!self::passesUserSettings($eventKey, $recipient)) {
            return;
        }

        // Формируем текст уведомления
        $messageTemplate = $config['message'] ?? '';
        $message = strtr($messageTemplate, [
            ':senderName' => $sender->name ?? $sender->username ?? 'Користувач',
        ]);

        // Создаем запись в БД (notification на сайте)
        $notificationData = [
            'user_id'        => $recipient->id,
            'sender_id'      => $sender->id,
            'type'           => $config['type'] ?? $eventKey,
            'message'        => $message,
            'notifiable_type'=> $notifiable ? get_class($notifiable) : null,
            'notifiable_id'  => $notifiable ? $notifiable->getKey() : null,
            'data'           => $payload,
        ];

        /** @var Notification $notification */
        $notification = Notification::create($notificationData);

        // Пытаемся отправить email (если включены email-уведомления и тип события это разрешает)
        if (!self::shouldSendEmail($recipient, $eventKey)) {
            return;
        }

        try {
            $view = $config['view'] ?? 'emails.notifications.generic';
            $subject = $config['subject'] ?? 'Нове сповіщення на Books Foroom';

            Mail::send($view, [
                'recipient'   => $recipient,
                'sender'      => $sender,
                'messageText' => $message,
                'eventKey'    => $eventKey,
                'notification'=> $notification,
                'data'        => $payload,
            ], function ($mail) use ($recipient, $subject) {
                $mail->to($recipient->email, $recipient->name ?? $recipient->username ?? '');
                $mail->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::error('UserNotificationHelper: failed to send email notification', [
                'event'       => $eventKey,
                'recipient_id'=> $recipient->id,
                'sender_id'   => $sender->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    /**
     * Конфигурация событий: тексты для notification + email.
     *
     * Ключи соответствуют бизнес-событиям:
     *  - discussion_comment_reply
     *  - review_comment_reply
     *  - review_reply
     *  - discussion_reply
     *  - review_like
     *  - discussion_like
     *  - quote_like
     *  - fact_like
     *  - discussion_comment_like
     *  - review_comment_like
     *  - *_like_milestone (пороги лайков)
     */
    protected static function eventsConfig(): array
    {
        return [
            // Ответы
            'discussion_comment_reply' => [
                'type'    => 'discussion_comment_reply',
                'subject' => 'Новий коментар у вашому обговоренні',
                'message' => ':senderName відповів на ваш коментар в обговоренні',
            ],
            'review_comment_reply' => [
                'type'    => 'review_comment_reply',
                'subject' => 'Новий коментар до вашої рецензії',
                'message' => ':senderName відповів на ваш коментар до рецензії',
            ],
            'review_reply' => [
                'type'    => 'review_reply',
                'subject' => 'Нова відповідь на вашу рецензію',
                'message' => ':senderName відповів на вашу рецензію',
            ],
            'discussion_reply' => [
                'type'    => 'discussion_reply',
                'subject' => 'Нова відповідь у вашому обговоренні',
                'message' => ':senderName відповів у вашому обговоренні',
            ],

            // Лайки
            'review_like' => [
                'type'    => 'review_like',
                'subject' => 'Новий лайк вашої рецензії',
                'message' => ':senderName вподобав вашу рецензію',
            ],
            'discussion_like' => [
                'type'    => 'discussion_like',
                'subject' => 'Новий лайк вашого обговорення',
                'message' => ':senderName вподобав ваше обговорення',
            ],
            'quote_like' => [
                'type'    => 'quote_like',
                'subject' => 'Новий лайк вашої цитати',
                'message' => ':senderName вподобав вашу цитату',
            ],
            'fact_like' => [
                'type'    => 'fact_like',
                'subject' => 'Новий лайк вашого факту',
                'message' => ':senderName вподобав ваш цікавий факт',
            ],
            'discussion_comment_like' => [
                'type'    => 'discussion_comment_like',
                'subject' => 'Новий лайк вашого коментаря',
                'message' => ':senderName вподобав ваш коментар в обговоренні',
            ],

            // Упоминания
            'discussion_mention' => [
                'type'    => 'discussion_mention',
                'subject' => 'Вас згадали в обговоренні',
                'message' => ':senderName згадав вас в обговоренні',
            ],
            'discussion_reply_mention' => [
                'type'    => 'discussion_reply_mention',
                'subject' => 'Вас згадали в коментарі',
                'message' => ':senderName згадав вас в коментарі до обговорення',
            ],
            'review_comment_like' => [
                'type'    => 'review_comment_like',
                'subject' => 'Новий лайк вашого коментаря',
                'message' => ':senderName вподобав ваш коментар до рецензії',
            ],
            
            // Пороги лайков (email + notification)
            'review_like_milestone' => [
                'type'    => 'review_like_milestone',
                'subject' => 'Ваша рецензія набрала :likes вподобань',
                'message' => 'Вітаємо! Ваша рецензія набрала :likes вподобань на Books Foroom.',
            ],
            'discussion_like_milestone' => [
                'type'    => 'discussion_like_milestone',
                'subject' => 'Ваше обговорення набрало :likes вподобань',
                'message' => 'Вітаємо! Ваше обговорення набрало :likes вподобань на Books Foroom.',
            ],
            'quote_like_milestone' => [
                'type'    => 'quote_like_milestone',
                'subject' => 'Ваша цитата набрала :likes вподобань',
                'message' => 'Вітаємо! Ваша цитата набрала :likes вподобань на Books Foroom.',
            ],
            'fact_like_milestone' => [
                'type'    => 'fact_like_milestone',
                'subject' => 'Ваш факт набрав :likes вподобань',
                'message' => 'Вітаємо! Ваш цікавий факт набрав :likes вподобань на Books Foroom.',
            ],
        ];
    }

    /**
     * Проверка пользовательских настроек для разных типов событий.
     *
     * Для коментарів у нас є окремий прапор comments_notifications,
     * для загальних email-ів — email_notifications.
     */
    protected static function passesUserSettings(string $eventKey, User $recipient): bool
    {
        // Для ответов, комментариев и упоминаний — comments_notifications (если есть)
        if (str_contains($eventKey, 'reply') || str_contains($eventKey, 'comment') || str_contains($eventKey, 'mention')) {
            if (isset($recipient->comments_notifications) && !$recipient->comments_notifications) {
                return false;
            }
        }

        // Для лайков и прочих — достаточно наличия пользователя (сайт-уведомление),
        // email будет контролироваться отдельно через email_notifications / shouldSendEmail
        return true;
    }

    /**
     * Нужно ли отправлять email пользователю для конкретного события.
     */
    protected static function shouldSendEmail(User $recipient, string $eventKey): bool
    {
        // Обычные лайки (review_like, discussion_like, quote_like, fact_like, *_comment_like)
        // приходят только в notifications на сайте, без email
        if (str_contains($eventKey, '_like') && !str_contains($eventKey, '_like_milestone')) {
            return false;
        }

        if (blank($recipient->email)) {
            return false;
        }

        if (isset($recipient->email_notifications) && !$recipient->email_notifications) {
            return false;
        }

        return true;
    }
}


