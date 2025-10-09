<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Создать уведомление о новом ответе на рецензию
     */
    public static function createReviewReplyNotification($review, $sender)
    {
        // Находим автора родительской рецензии
        $parentReview = $review->parent;
        
        if (!$parentReview || !$parentReview->user_id || $parentReview->user_id === $sender->id) {
            return; // Не создаем уведомление если нет родителя, пользователя или это свой ответ
        }

        $recipient = $parentReview->user;
        
        // Проверяем настройки уведомлений
        if (!$recipient->comments_notifications) {
            return;
        }

        Notification::create([
            'user_id' => $recipient->id,
            'sender_id' => $sender->id,
            'type' => 'review_reply',
            'message' => $sender->name . ' відповів на вашу рецензію',
            'notifiable_type' => get_class($review),
            'notifiable_id' => $review->id,
            'data' => [
                'book_id' => $parentReview->book_id,
                'book_title' => $parentReview->book->title ?? 'Книга',
                'review_id' => $parentReview->id,
                'reply_id' => $review->id,
                'reply_content' => mb_substr($review->content, 0, 100),
            ],
        ]);
    }

    /**
     * Создать уведомление о новом ответе в обсуждении
     */
    public static function createDiscussionReplyNotification($reply, $sender)
    {
        $discussion = $reply->discussion;
        
        // Если это ответ на другой ответ
        if ($reply->parent_id) {
            $parentReply = $reply->parent;
            
            if (!$parentReply || !$parentReply->user_id || $parentReply->user_id === $sender->id) {
                return;
            }

            $recipient = $parentReply->user;
            
            // Проверяем настройки уведомлений
            if (!$recipient->comments_notifications) {
                return;
            }

            Notification::create([
                'user_id' => $recipient->id,
                'sender_id' => $sender->id,
                'type' => 'discussion_reply',
                'message' => $sender->name . ' відповів на ваш коментар',
                'notifiable_type' => get_class($reply),
                'notifiable_id' => $reply->id,
                'data' => [
                    'discussion_id' => $discussion->id,
                    'discussion_title' => $discussion->title,
                    'discussion_slug' => $discussion->slug,
                    'parent_reply_id' => $parentReply->id,
                    'reply_id' => $reply->id,
                    'reply_content' => mb_substr($reply->content, 0, 100),
                ],
            ]);
        } else {
            // Если это ответ на обсуждение, уведомляем автора обсуждения
            if (!$discussion->user_id || $discussion->user_id === $sender->id) {
                return;
            }

            $recipient = $discussion->user;
            
            // Проверяем настройки уведомлений
            if (!$recipient->comments_notifications) {
                return;
            }

            Notification::create([
                'user_id' => $recipient->id,
                'sender_id' => $sender->id,
                'type' => 'discussion_reply',
                'message' => $sender->name . ' відповів у вашому обговоренні',
                'notifiable_type' => get_class($reply),
                'notifiable_id' => $reply->id,
                'data' => [
                    'discussion_id' => $discussion->id,
                    'discussion_title' => $discussion->title,
                    'discussion_slug' => $discussion->slug,
                    'reply_id' => $reply->id,
                    'reply_content' => mb_substr($reply->content, 0, 100),
                ],
            ]);
        }
    }

    /**
     * Создать уведомление о новом лайке
     */
    public static function createLikeNotification($likeable, $sender)
    {
        // Определяем получателя в зависимости от типа лайкнутого объекта
        $recipient = null;
        $message = '';
        $type = 'like';

        if ($likeable instanceof \App\Models\Review) {
            $recipient = $likeable->user;
            $message = $sender->name . ' вподобав вашу рецензію';
        } elseif ($likeable instanceof \App\Models\Discussion) {
            $recipient = $likeable->user;
            $message = $sender->name . ' вподобав ваше обговорення';
        } elseif ($likeable instanceof \App\Models\DiscussionReply) {
            $recipient = $likeable->user;
            $message = $sender->name . ' вподобав ваш коментар';
        } elseif ($likeable instanceof \App\Models\Quote) {
            $recipient = $likeable->user;
            $message = $sender->name . ' вподобав вашу цитату';
        }

        if (!$recipient || $recipient->id === $sender->id) {
            return; // Не создаем уведомление если это свой лайк
        }

        // Проверяем настройки уведомлений (пока используем общие настройки)
        if (!$recipient->email_notifications) {
            return;
        }

        Notification::create([
            'user_id' => $recipient->id,
            'sender_id' => $sender->id,
            'type' => $type,
            'message' => $message,
            'notifiable_type' => get_class($likeable),
            'notifiable_id' => $likeable->id,
            'data' => [
                'likeable_type' => get_class($likeable),
                'likeable_id' => $likeable->id,
            ],
        ]);
    }
}

