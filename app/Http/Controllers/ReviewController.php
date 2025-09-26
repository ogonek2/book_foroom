<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
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
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review = Review::create([
            'content' => $request->input('content'),
            'rating' => $request->input('rating'),
            'book_id' => $book->getKey(),
            'user_id' => Auth::id(),
            'parent_id' => null,
        ]);

        // Оновлюємо рейтинг книги
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
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review = Review::create([
            'content' => $request->input('content'),
            'rating' => $request->input('rating'),
            'book_id' => $book->getKey(),
            'user_id' => null, // Гість
            'parent_id' => null,
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
    public function show(Book $book, Review $review)
    {
        // Загружаем рецензию с автором и всеми ответами
        $review->load([
            'user',
            'book',
            'replies' => function ($query) {
                $query->with([
                    'user',
                    'replies' => function ($query) {
                        $query->with([
                            'user',
                            'replies' => function ($query) {
                                $query->with('user');
                            }
                        ]);
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
        $request->validate([
            'content' => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:reviews,id'
        ]);

        // Визначаємо, чи авторизований користувач
        $userId = Auth::check() ? Auth::id() : null;
        
        // Определяем родительский отзыв (для вложенных ответов)
        $parentId = $request->input('parent_id') ?: $review->getKey();

        $reply = Review::create([
            'content' => $request->input('content'),
            'rating' => null, // Відповіді не мають рейтингу
            'book_id' => $book->getKey(),
            'user_id' => $userId, // null для гостей, ID користувача для авторизованих
            'parent_id' => $parentId,
        ]);

        // Оновлюємо лічильник відповідей для основного отзыва
        $review->updateRepliesCount();

        if ($request->expectsJson()) {
            // Подготавливаем данные для фронтенда
            $replyData = [
                'id' => $reply->id,
                'content' => $reply->content,
                'created_at' => $reply->created_at,
                'is_guest' => $reply->isGuest(),
                'author_name' => $reply->getAuthorName(),
                'likes_count' => 0,
                'replies_count' => 0
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
    public function edit(Review $review)
    {
        $this->authorize('update', $review);
        
        return view('reviews.edit', compact('review'));
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, Review $review)
    {
        $this->authorize('update', $review);
        
        $request->validate([
            'content' => 'required|string|max:5000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review->update([
            'content' => $request->input('content'),
            'rating' => $request->input('rating'),
        ]);

        // Оновлюємо рейтинг книги
        $review->book->updateRating();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Рецензію оновлено!',
                'review' => $review
            ]);
        }

        return redirect()->back()->with('success', 'Рецензію оновлено!');
    }

    /**
     * Remove the specified review
     */
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        
        $book = $review->book;
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
}
