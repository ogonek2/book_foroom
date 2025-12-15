<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\DiscussionReply;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionLikeController extends Controller
{
    /**
     * Toggle like for discussion
     */
    public function toggleDiscussion(Request $request, Discussion $discussion)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Необходима авторизация'], 401);
        }

        $likesBefore = $discussion->likes()->where('vote', 1)->count();

        $existingLike = Like::where('user_id', $user->id)
            ->where('likeable_type', Discussion::class)
            ->where('likeable_id', $discussion->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => $user->id,
                'likeable_type' => Discussion::class,
                'likeable_id' => $discussion->id,
                'vote' => 1,
            ]);
            $liked = true;

            // Уведомление о лайке обговорення
            if ($discussion->user_id && $discussion->user_id !== $user->id) {
                \App\Services\NotificationService::createLikeNotification($discussion, $user);
            }
        }

        $likesCount = $discussion->likes()->where('vote', 1)->count();

        // Поріг 20 лайків для обговорення (email + notification)
        if ($liked && $likesBefore < 20 && $likesCount >= 20 && $discussion->user_id && $discussion->user_id !== $user->id) {
            \App\Helpers\UserNotificationHelper::send(
                'discussion_like_milestone',
                $discussion->user,
                $user,
                [
                    'likes_count'    => $likesCount,
                    'discussion_id'  => $discussion->id,
                    'discussion_slug'=> $discussion->slug ?? null,
                ],
                $discussion
            );
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $likesCount
        ]);
    }

    /**
     * Toggle like for discussion reply
     */
    public function toggleReply(Request $request, Discussion $discussion, DiscussionReply $reply)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Необходима авторизация'], 401);
        }

        $existingLike = Like::where('user_id', $user->id)
            ->where('likeable_type', DiscussionReply::class)
            ->where('likeable_id', $reply->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => $user->id,
                'likeable_type' => DiscussionReply::class,
                'likeable_id' => $reply->id,
                'vote' => 1,
            ]);
            $liked = true;

            // Уведомление о лайке коментаря в обговоренні
            if ($reply->user_id && $reply->user_id !== $user->id) {
                \App\Services\NotificationService::createLikeNotification($reply, $user);
            }
        }

        $likesCount = $reply->likes()->where('vote', 1)->count();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $likesCount
        ]);
    }
}
