<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Get replies for a review with pagination
     */
    public function getReplies(Request $request, Review $review)
    {
        $replies = $review->replies()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        // Добавляем счетчики лайков/дизлайков для каждого ответа
        $replies->getCollection()->transform(function ($reply) {
            $reply->likes_count = $reply->likes()->where('vote', 1)->count();
            $reply->dislikes_count = $reply->likes()->where('vote', -1)->count();
            return $reply;
        });

        return response()->json([
            'success' => true,
            'data' => $replies->items(),
            'next_page_url' => $replies->nextPageUrl(),
            'current_page' => $replies->currentPage(),
            'last_page' => $replies->lastPage(),
            'total' => $replies->total()
        ]);
    }

    /**
     * Like a review
     */
    public function like(Request $request, Review $review)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Необхідно авторизуватись для лайку'
            ], 401);
        }

        // Проверяем, не лайкнул ли уже пользователь
        $existingLike = \App\Models\Like::where('user_id', $userId)
            ->where('likeable_id', $review->id)
            ->where('likeable_type', \App\Models\Review::class)
            ->where('vote', 1)
            ->first();

        if ($existingLike) {
            // Убираем лайк
            $existingLike->delete();
            $isLiked = false;
        } else {
            // Убираем дизлайк если есть
            \App\Models\Like::where('user_id', $userId)
                ->where('likeable_id', $review->id)
                ->where('likeable_type', \App\Models\Review::class)
                ->where('vote', -1)
                ->delete();
            
            // Создаем лайк
            \App\Models\Like::create([
                'user_id' => $userId,
                'likeable_id' => $review->id,
                'likeable_type' => \App\Models\Review::class,
                'vote' => 1
            ]);
            $isLiked = true;
        }

        return response()->json([
            'success' => true,
            'isLiked' => $isLiked,
            'likesCount' => $review->fresh()->likes_count,
            'dislikesCount' => $review->fresh()->dislikes_count,
            'message' => $isLiked ? 'Лайк додано!' : 'Лайк прибрано!'
        ]);
    }

    /**
     * Dislike a review
     */
    public function dislike(Request $request, Review $review)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Необхідно авторизуватись для дизлайка'
            ], 401);
        }

        // Проверяем, не дизлайкнул ли уже пользователь
        $existingDislike = \App\Models\Like::where('user_id', $userId)
            ->where('likeable_id', $review->id)
            ->where('likeable_type', \App\Models\Review::class)
            ->where('vote', -1)
            ->first();

        if ($existingDislike) {
            // Убираем дизлайк
            $existingDislike->delete();
            $isDisliked = false;
        } else {
            // Убираем лайк если есть
            \App\Models\Like::where('user_id', $userId)
                ->where('likeable_id', $review->id)
                ->where('likeable_type', \App\Models\Review::class)
                ->where('vote', 1)
                ->delete();
            
            // Создаем дизлайк
            \App\Models\Like::create([
                'user_id' => $userId,
                'likeable_id' => $review->id,
                'likeable_type' => \App\Models\Review::class,
                'vote' => -1
            ]);
            $isDisliked = true;
        }

        return response()->json([
            'success' => true,
            'isDisliked' => $isDisliked,
            'likesCount' => $review->fresh()->likes_count,
            'dislikesCount' => $review->fresh()->dislikes_count,
            'message' => $isDisliked ? 'Дизлайк додано!' : 'Дизлайк прибрано!'
        ]);
    }

    /**
     * Delete a review or reply
     */
    public function destroy(Review $review)
    {
        // Проверяем права на удаление
        if (Auth::check() && Auth::id() === $review->user_id) {
            $review->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Вилучено успішно!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Немає прав на вилучення'
        ], 403);
    }
}
