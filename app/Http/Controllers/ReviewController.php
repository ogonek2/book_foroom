<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReviewController extends Controller
{
    use AuthorizesRequests;
    /**
     * Store a new review (для авторизованих користувачів)
     */
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'rating' => 'nullable|integer|min:1|max:10',
            'review_type' => 'nullable|in:review,opinion',
            'opinion_type' => 'nullable|in:positive,neutral,negative',
            'book_type' => 'nullable|in:paper,electronic,audio',
            'language' => 'nullable|in:uk,en',
            'contains_spoiler' => 'nullable|boolean',
        ]);

        // Проверяем, есть ли уже рецензия от этого пользователя на эту книгу
        $existingReview = Review::where('book_id', $book->getKey())
            ->where('user_id', Auth::id())
            ->whereNull('parent_id') // Только основные рецензии, не комментарии
            ->first();

        if ($existingReview) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ви вже залишили рецензію на цю книгу. Ви можете редагувати існуючу рецензію.',
                    'existing_review' => $existingReview
                ], 422);
            }

            return redirect()->back()->with('error', 'Ви вже залишили рецензію на цю книгу. Ви можете редагувати існуючу рецензію.');
        }

        // Получаем рейтинг из BookReadingStatus, если он не передан в запросе
        $rating = $request->input('rating');
        if (!$rating) {
            $userRating = $book->getUserRating(Auth::id());
            $rating = $userRating;
        }

        // Если нет рейтинга ни в запросе, ни в BookReadingStatus, возвращаем ошибку
        if (!$rating) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Спочатку поставте оцінку книзі, а потім напишіть рецензію.',
                ], 422);
            }

            return redirect()->back()->with('error', 'Спочатку поставте оцінку книзі, а потім напишіть рецензію.');
        }

        $review = Review::create([
            'content' => $request->input('content'),
            'rating' => $rating,
            'book_id' => $book->getKey(),
            'user_id' => Auth::id(),
            'parent_id' => null,
            'review_type' => $request->input('review_type', 'review'),
            'opinion_type' => $request->input('opinion_type', 'positive'),
            'book_type' => $request->input('book_type', 'paper'),
            'language' => $request->input('language', 'uk'),
            'contains_spoiler' => $request->boolean('contains_spoiler', false),
        ]);

        // Синхронизируем рейтинг с BookReadingStatus
        $readingStatus = \App\Models\BookReadingStatus::firstOrCreate(
            [
                'book_id' => $book->getKey(),
                'user_id' => Auth::id(),
            ],
            [
                'status' => 'read',
                'finished_at' => now(),
            ]
        );

        // Обновляем рейтинг в BookReadingStatus
        $readingStatus->update(['rating' => $rating]);

        // Обновляем средний рейтинг книги
        $book->updateRating();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Рецензію додано!',
                'review' => $review->load('user')
            ]);
        }

        return redirect()->back()->with('success', 'Рецензію додано!');
    }

    /**
     * Store a guest review (для гостей)
     */
    public function guestStore(Request $request, Book $book)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'rating' => 'required|integer|min:1|max:10',
            'review_type' => 'nullable|in:review,opinion',
            'opinion_type' => 'nullable|in:positive,neutral,negative',
            'book_type' => 'nullable|in:paper,electronic,audio',
            'language' => 'nullable|in:uk,en,de,other',
            'contains_spoiler' => 'nullable|boolean',
        ]);

        $review = Review::create([
            'content' => $request->input('content'),
            'rating' => $request->input('rating'),
            'book_id' => $book->getKey(),
            'user_id' => null, // Гість
            'parent_id' => null,
            'review_type' => $request->input('review_type', 'review'),
            'opinion_type' => $request->input('opinion_type', 'positive'),
            'book_type' => $request->input('book_type', 'paper'),
            'language' => $request->input('language', 'uk'),
            'contains_spoiler' => $request->boolean('contains_spoiler', false),
        ]);

        // Оновлюємо рейтинг книги
        $book->updateRating();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Рецензію додано!',
                'review' => $review
            ]);
        }

        return redirect()->back()->with('success', 'Рецензію додано!');
    }

    /**
     * Show a single review with its discussion thread
     */
    public function show(Book $book, $reviewIdentifier)
    {
        // Находим рецензию по ID
        $review = Review::where('id', $reviewIdentifier)
            ->where('book_id', $book->id)
            ->firstOrFail();

        // Загружаем рецензию с автором и первыми ответами (только 2 уровня)
        $review->load([
            'user',
            'book',
            'replies' => function ($query) {
                $query->with([
                    'user',
                    'replies' => function ($query) {
                        $query->with('user')->orderBy('created_at', 'desc');
                    }
                ])->orderBy('created_at', 'desc');
            }
        ]);

        // Получаем связанные книги
        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->orderBy('rating', 'desc')
            ->limit(4)
            ->get();

        return view('reviews.show', compact('review', 'book', 'relatedBooks'));
    }

    /**
     * Store a reply to a review (для всіх користувачів)
     */
    public function storeReply(Request $request, Book $book, Review $review)
    {
        // Логируем для отладки
        Log::info('storeReply called', [
            'book_id' => $book->id ?? 'null',
            'review_id' => $review->id ?? 'null',
            'request_data' => $request->all()
        ]);

        // Проверяем существование книги
        if (!$book) {
            Log::error('Book not found', ['book_id' => $book->id ?? 'null']);
            return response()->json([
                'success' => false,
                'message' => 'Книга не найдена'
            ], 404);
        }

        $request->validate([
            'content' => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:reviews,id'
        ]);

        // Визначаємо, чи авторизований користувач
        $userId = Auth::check() ? Auth::id() : null;
        
        // Определяем родительский отзыв (для вложенных ответов)
        // Если parent_id передан, используем его, иначе это ответ на главную рецензию
        $parentId = $request->input('parent_id');
        if (!$parentId) {
            $parentId = $review->getKey();
        }

        $reply = Review::create([
            'content' => $request->input('content'),
            'rating' => null, // Відповіді не мають рейтингу
            'book_id' => $book->getKey(),
            'user_id' => $userId, // null для гостей, ID користувача для авторизованих
            'parent_id' => $parentId,
        ]);

        // Оновлюємо лічильник відповідей для основного отзыва
        $review->updateRepliesCount();

        // Уведомление создается автоматически в событии created модели Review

        if ($request->expectsJson()) {
            // Загружаем связанные данные для фронтенда
            $reply->load('user');
            
            // Подготавливаем данные для фронтенда
            $replyData = [
                'id' => $reply->id,
                'content' => $reply->content,
                'created_at' => $reply->created_at->toISOString(),
                'user_id' => $reply->user_id,
                'parent_id' => $reply->parent_id,
                'is_guest' => $reply->isGuest(),
                'user' => $reply->user ? [
                    'id' => $reply->user->id,
                    'name' => $reply->user->name,
                    'username' => $reply->user->username,
                    'avatar_display' => $reply->user->avatar_display ?? null,
                ] : null,
                'is_liked_by_current_user' => false,
                'likes_count' => 0,
                'replies_count' => 0,
                'replies' => []
            ];

            return response()->json([
                'success' => true,
                'message' => 'Відповідь додано!',
                'reply' => $replyData
            ]);
        }

        return redirect()->back()->with('success', 'Відповідь додано!');
    }

    /**
     * Toggle like for a review
     */
    public function toggleLike(Book $book, Review $review)
    {
        $user = auth()->user();
        
        // Check if user already liked this review
        $existingLike = $review->likes()->where('user_id', $user->id)->first();
        
        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $isLiked = false;
        } else {
            // Like
            $review->likes()->create([
                'user_id' => $user->id,
                'vote' => 1
            ]);
            $isLiked = true;
        }
        
        // Update likes count
        $likesCount = $review->likes()->where('vote', 1)->count();
        
        return response()->json([
            'success' => true,
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }

    /**
     * Show the form for editing a review
     */
    public function edit(Book $book, Review $review)
    {
        $this->authorize('update', $review);
        
        return view('reviews.edit', compact('book', 'review'));
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, Book $book, Review $review)
    {
        $this->authorize('update', $review);
        
        $request->validate([
            'content' => 'required|string|max:5000',
            'rating' => 'nullable|integer|min:1|max:10',
            'review_type' => 'nullable|in:review,opinion',
            'opinion_type' => 'nullable|in:positive,neutral,negative',
            'book_type' => 'nullable|in:paper,electronic,audio',
            'language' => 'nullable|in:uk,en,de,other',
            'contains_spoiler' => 'nullable|boolean',
        ]);

        $review->update([
            'content' => $request->input('content'),
            'rating' => $request->input('rating', $review->rating),
            'review_type' => $request->input('review_type', $review->review_type),
            'opinion_type' => $request->input('opinion_type', $review->opinion_type),
            'book_type' => $request->input('book_type', $review->book_type),
            'language' => $request->input('language', $review->language),
            'contains_spoiler' => $request->boolean('contains_spoiler', $review->contains_spoiler),
        ]);

        // Оновлюємо рейтинг книги если рейтинг изменился
        if ($request->has('rating') && $request->input('rating') != $review->rating) {
            $book->updateRating();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Рецензію оновлено!',
                'review' => $review->fresh()->load('user')
            ]);
        }

        return redirect()->back()->with('success', 'Рецензію оновлено!');
    }

    /**
     * Remove the specified review
     */
    public function destroy(Book $book, Review $review)
    {
        $this->authorize('delete', $review);
        
        $review->delete();

        // Оновлюємо рейтинг книги
        $book->updateRating();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Рецензію видалено!'
            ]);
        }

        return redirect()->back()->with('success', 'Рецензію видалено!');
    }

    /**
     * Update a reply/comment
     */
    public function updateReply(Request $request, Book $book, Review $review)
    {
        // Проверяем, что это ответ (не основная рецензия)
        if (!$review->parent_id) {
            return response()->json([
                'success' => false,
                'message' => 'Це не відповідь, а основна рецензія'
            ], 400);
        }

        // Проверяем права доступа
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Потрібна авторизація'
            ], 401);
        }

        // Проверяем, что пользователь может редактировать этот ответ
        if ($review->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Немає прав для редагування цього коментаря'
            ], 403);
        }

        $request->validate([
            'content' => 'required|string|max:5000'
        ]);

        $review->update([
            'content' => $request->input('content')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Коментар оновлено!',
            'content' => $review->content
        ]);
    }

    /**
     * Delete a reply/comment
     */
    public function deleteReply(Request $request, Book $book, Review $review)
    {
        // Проверяем, что это ответ (не основная рецензия)
        if (!$review->parent_id) {
            return response()->json([
                'success' => false,
                'message' => 'Це не відповідь, а основна рецензія'
            ], 400);
        }

        // Проверяем права доступа
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Потрібна авторизація'
            ], 401);
        }

        // Проверяем, что пользователь может удалить этот ответ
        if ($review->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Немає прав для видалення цього коментаря'
            ], 403);
        }

        // Получаем основную рецензию для обновления счетчика
        $parentReview = Review::find($review->parent_id);
        
        $review->delete();

        // Обновляем счетчик ответов
        if ($parentReview) {
            $parentReview->updateRepliesCount();
        }

        return response()->json([
            'success' => true,
            'message' => 'Коментар видалено!'
        ]);
    }
}
