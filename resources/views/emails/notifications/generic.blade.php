@php
    /** @var \App\Models\User $recipient */
    /** @var \App\Models\User $sender */
    /** @var string $messageText */
    /** @var string $eventKey */
    /** @var \App\Models\Notification $notification */
    /** @var array $data */
@endphp

@php
    $baseUrl = config('app.url') ?? url('/');
    $notificationUrl = null;

    if (isset($notification) && $notification && is_array($notification->data ?? null)) {
        $type = $notification->type;
        $d = $notification->data;

        if (in_array($type, ['review_reply', 'review_comment_reply', 'review_like', 'review_like_milestone'])) {
            $bookIdentifier = $d['book_slug'] ?? $d['book_id'] ?? null;
            $reviewId = $d['review_id'] ?? null;
            if ($bookIdentifier && $reviewId) {
                $notificationUrl = rtrim($baseUrl, '/') . "/books/{$bookIdentifier}/reviews/{$reviewId}";
            }
        } elseif (in_array($type, ['discussion_reply', 'discussion_comment_reply', 'discussion_like', 'discussion_like_milestone', 'discussion_comment_like'])) {
            $discussionIdentifier = $d['discussion_id'] ?? $d['discussion_slug'] ?? null;
            if ($discussionIdentifier) {
                $notificationUrl = rtrim($baseUrl, '/') . "/discussions/{$discussionIdentifier}";
            }
        } else {
            // Fallback — страница уведомлений
            $notificationUrl = rtrim($baseUrl, '/') . '/notifications';
        }
    } else {
        $notificationUrl = rtrim($baseUrl, '/') . '/notifications';
    }

    // Текст ответа (для реплаев/коментарів)
    $replyText = $data['reply_content'] ?? $data['content'] ?? null;
@endphp

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Нове сповіщення на Books Foroom</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background-color: #f3f4f6; padding: 24px;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; padding: 24px;">
                    <tr>
                        <td style="text-align: center; padding-bottom: 16px;">
                            <h1 style="margin: 0; font-size: 20px; color: #111827;">Привіт, {{ $recipient->name ?? $recipient->username ?? 'друже' }}!</h1>
                        </td>
                    </tr>

                    {{-- Карточка відправника --}}
                    @if(isset($sender))
                        <tr>
                            <td style="padding-bottom: 16px;">
                                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-radius: 12px; background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 12px;">
                                    <tr>
                                        <td width="48" valign="top" style="padding-right: 12px;">
                                            @php
                                                /** @var \App\Models\User $sender */
                                                $avatarUrl = $sender->avatar_display ?? null;
                                                $initials = mb_strtoupper(mb_substr($sender->name ?? $sender->username ?? 'U', 0, 1));
                                            @endphp
                                            @if($avatarUrl)
                                                <div style="width: 40px; height: 40px; border-radius: 9999px; overflow: hidden; background: #111827;">
                                                    <img src="{{ $avatarUrl }}" alt="{{ $sender->name ?? $sender->username ?? 'Avatar' }}" style="width: 40px; height: 40px; object-fit: cover; display: block;">
                                                </div>
                                            @else
                                                <div style="width: 40px; height: 40px; border-radius: 9999px; background: linear-gradient(135deg, #6366f1, #8b5cf6); display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 700; font-size: 16px;">
                                                    {{ $initials }}
                                                </div>
                                            @endif
                                        </td>
                                        <td valign="top">
                                            <p style="margin: 0; font-size: 14px; color: #111827; font-weight: 600;">
                                                {{ $sender->name ?? $sender->username ?? 'Користувач' }}
                                            </p>
                                            @if(!empty($sender->username))
                                                <p style="margin: 2px 0 0 0; font-size: 12px; color: #6b7280;">
                                                    @php
                                                        $profileUrl = route('users.public.profile', $sender->username);
                                                    @endphp
                                                    <a href="{{ $profileUrl }}" style="color: #6366f1; text-decoration: none;">{{ '@' . $sender->username }}</a>
                                                </p>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding-bottom: 16px;">
                            <p style="margin: 0; font-size: 14px; color: #374151;">
                                {{ $messageText }}
                            </p>
                        </td>
                    </tr>

                    {{-- Фрагмент відповіді / контенту --}}
                    @if(!empty($replyText))
                        <tr>
                            <td style="padding-bottom: 16px;">
                                <div style="border-left: 3px solid #6366f1; padding-left: 12px; font-size: 13px; color: #4b5563; background-color: #f9fafb; border-radius: 8px;">
                                    {{ mb_substr($replyText, 0, 220) }}@if(mb_strlen($replyText) > 220)…@endif
                                </div>
                            </td>
                        </tr>
                    @endif

                    @if(!empty($data['book_title']) || !empty($data['discussion_title']))
                        <tr>
                            <td style="padding-bottom: 16px;">
                                <p style="margin: 0; font-size: 13px; color: #6b7280;">
                                    @if(!empty($data['book_title']))
                                        Книга: <strong>{{ $data['book_title'] }}</strong><br>
                                    @endif
                                    @if(!empty($data['discussion_title']))
                                        Обговорення: <strong>{{ $data['discussion_title'] }}</strong>
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding-top: 8px; padding-bottom: 24px; text-align: center;">
                            @if(!empty($notificationUrl))
                                <a href="{{ $notificationUrl }}" style="display: inline-block; padding: 10px 18px; font-size: 13px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 9999px; background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                                    Перейти до сповіщення
                                </a>
                            @else
                                <p style="margin: 0; font-size: 13px; color: #6b7280;">
                                    Зайдіть на сайт, щоб переглянути деталі сповіщення.
                                </p>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td style="border-top: 1px solid #e5e7eb; padding-top: 16px; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                                Ви отримали це повідомлення, тому що у вас увімкнені email-сповіщення на Books Foroom.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>


