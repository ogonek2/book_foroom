<?php

namespace App\Helpers;

use App\Models\Award;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AwardNotificationHelper
{
    public static function sendAwardAssignedEmail(User $user, Award $award, ?Carbon $awardedAt = null, ?string $note = null): void
    {
        if (blank($user->email)) {
            return;
        }

        if (isset($user->email_notifications) && ! $user->email_notifications) {
            return;
        }

        $awardedAt = $awardedAt ?: now();

        try {
            Mail::send('emails.awards.assigned', [
                'user' => $user,
                'award' => $award,
                'awardedAt' => $awardedAt,
                'note' => $note,
            ], function ($message) use ($user, $award) {
                $message->to($user->email, $user->name ?? $user->username ?? '');
                $message->subject('Ви отримали нову нагороду на Books Foroom');
            });

            Log::info('Award assignment email sent', [
                'user_id' => $user->id,
                'award_id' => $award->id,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Failed to send award assignment email', [
                'user_id' => $user->id,
                'award_id' => $award->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
