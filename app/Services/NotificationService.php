<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Helpers\UserNotificationHelper;

class NotificationService
{
    /**
     * Создать уведомление о новом ответе на рецензию
     */
    public static function createReviewReplyNotification($review, $sender)
    {
        $parentReview = $review->parent;
        
        if (!$parentReview || !$parentReview->user_id || $parentReview->user_id === $sender->id) {
            return; // Не создаем уведомление если нет родителя, пользователя или это свой ответ
        }

        $recipient = $parentReview->user;

        $parentReview->load('book');

        // Определяем, это ответ на рецензию или на комментарий к рецензии
        $eventKey = $parentReview->parent_id ? 'review_comment_reply' : 'review_reply';

        $payload = [
            'book_id'       => $parentReview->book_id,
            'book_slug'     => $parentReview->book->slug,
            'book_title'    => $parentReview->book->title ?? 'Книга',
            'review_id'     => $parentReview->id,
            'reply_id'      => $review->id,
            'reply_content' => mb_substr($review->content, 0, 100),
        ];

        UserNotificationHelper::send($eventKey, $recipient, $sender, $payload, $review);
    }

    /**
     * Создать уведомление о новом ответе в обсуждении
     */
    public static function createDiscussionReplyNotification($reply, $sender)
    {
        $discussion = $reply->discussion;

        if ($reply->parent_id) {
            $parentReply = $reply->parent;
            
            if (!$parentReply || !$parentReply->user_id || $parentReply->user_id === $sender->id) {
                return;
            }

            $recipient = $parentReply->user;

            $payload = [
                'discussion_id'    => $discussion->id,
                'discussion_title' => $discussion->title,
                'discussion_slug'  => $discussion->slug,
                'parent_reply_id'  => $parentReply->id,
                'reply_id'         => $reply->id,
                'reply_content'    => mb_substr($reply->content, 0, 100),
            ];

            UserNotificationHelper::send(
                'discussion_comment_reply',
                $recipient,
                $sender,
                $payload,
                $reply
            );
        } else {
            if (!$discussion->user_id || $discussion->user_id === $sender->id) {
                return;
            }

            $recipient = $discussion->user;

            $payload = [
                'discussion_id'    => $discussion->id,
                'discussion_title' => $discussion->title,
                'discussion_slug'  => $discussion->slug,
                'reply_id'         => $reply->id,
                'reply_content'    => mb_substr($reply->content, 0, 100),
            ];

            UserNotificationHelper::send(
                'discussion_reply',
                $recipient,
                $sender,
                $payload,
                $reply
            );
        }
    }

    /**
     * Создать уведомление о новом лайке
     */
    public static function createLikeNotification($likeable, $sender)
    {
        $recipient = null;
        $eventKey = null;

        if ($likeable instanceof \App\Models\Review) {
            $recipient = $likeable->user;
            // Если это ответ (коментар на рецензію)
            if (!empty($likeable->parent_id)) {
                $eventKey = 'review_comment_like';
            } else {
                $eventKey = 'review_like';
            }
        } elseif ($likeable instanceof \App\Models\Discussion) {
            $recipient = $likeable->user;
            $eventKey = 'discussion_like';
        } elseif ($likeable instanceof \App\Models\DiscussionReply) {
            $recipient = $likeable->user;
            $eventKey = 'discussion_comment_like';
        } elseif ($likeable instanceof \App\Models\Quote) {
            $recipient = $likeable->user;
            $eventKey = 'quote_like';
        } elseif ($likeable instanceof \App\Models\Fact) {
            $recipient = $likeable->user;
            $eventKey = 'fact_like';
        }

        if (!$recipient || !$eventKey) {
            return;
        }

        $payload = [
            'likeable_type' => get_class($likeable),
            'likeable_id'   => $likeable->id,
        ];

        // Добавляем данные для рецензий
        if ($likeable instanceof \App\Models\Review) {
            $likeable->load('book');
            $payload['book_id'] = $likeable->book_id;
            $payload['book_slug'] = $likeable->book->slug ?? null;
            $payload['book_title'] = $likeable->book->title ?? 'Книга';
            $payload['review_id'] = $likeable->id;
        } elseif ($likeable instanceof \App\Models\Discussion) {
            $payload['discussion_id'] = $likeable->id;
            $payload['discussion_slug'] = $likeable->slug;
            $payload['discussion_title'] = $likeable->title;
        } elseif ($likeable instanceof \App\Models\DiscussionReply) {
            $discussion = $likeable->discussion;
            $payload['discussion_id'] = $discussion->id;
            $payload['discussion_slug'] = $discussion->slug;
            $payload['discussion_title'] = $discussion->title;
            $payload['reply_id'] = $likeable->id;
        }

        UserNotificationHelper::send($eventKey, $recipient, $sender, $payload, $likeable);
    }

    /**
     * Создать уведомление об упоминании в обсуждении
     */
    public static function createMentionNotification($discussion, $mentionedUser, $sender)
    {
        $payload = [
            'discussion_id'    => $discussion->id,
            'discussion_slug'  => $discussion->slug,
            'discussion_title' => $discussion->title,
        ];

        UserNotificationHelper::send('discussion_mention', $mentionedUser, $sender, $payload, $discussion);
    }

    /**
     * Создать уведомление об упоминании в ответе на обсуждение
     */
    public static function createReplyMentionNotification($reply, $mentionedUser, $sender)
    {
        $discussion = $reply->discussion;
        
        $payload = [
            'discussion_id'    => $discussion->id,
            'discussion_slug'  => $discussion->slug,
            'discussion_title' => $discussion->title,
            'reply_id'         => $reply->id,
            'reply_content'    => mb_substr($reply->content, 0, 100),
        ];

        UserNotificationHelper::send('discussion_reply_mention', $mentionedUser, $sender, $payload, $reply);
    }
}

