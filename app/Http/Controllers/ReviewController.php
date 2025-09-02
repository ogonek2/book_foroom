<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Store a new review (для авторизованных пользователей)
     */
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review = Review::create([
            'content' => $request->content,
            'rating' => $request->rating,
            'book_id' => $book->id,
            'user_id' => Auth::id(),
            'parent_id' => null,
        ]);

        // Обновляем рейтинг книги
        $book->updateRating();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Рецензия добавлена!',
                'review' => $review->load('user')
            ]);
        }

        return redirect()->back()->with('success', 'Рецензия добавлена!');
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
            'content' => $request->content,
            'rating' => $request->rating,
            'book_id' => $book->id,
            'user_id' => null, // Гость
            'parent_id' => null,
        ]);

        // Обновляем рейтинг книги
        $book->updateRating();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Рецензия добавлена!',
                'review' => $review
            ]);
        }

        return redirect()->back()->with('success', 'Рецензия добавлена!');
    }

    /**
     * Store a reply to a review (для всех пользователей)
     */
    public function storeReply(Request $request, Book $book, Review $review)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        // Определяем, авторизован ли пользователь
        $userId = Auth::check() ? Auth::id() : null;

        $reply = Review::create([
            'content' => $request->content,
            'rating' => null, // Ответы не имеют рейтинга
            'book_id' => $book->id,
            'user_id' => $userId, // null для гостей, ID пользователя для авторизованных
            'parent_id' => $review->id,
        ]);

        // Обновляем счетчик ответов
        $review->updateRepliesCount();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ответ добавлен!',
                'reply' => $reply->load('user')
            ]);
        }

        return redirect()->back()->with('success', 'Ответ добавлен!');
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
            'content' => $request->content,
            'rating' => $request->rating,
        ]);

        // Обновляем рейтинг книги
        $review->book->updateRating();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Рецензия обновлена!',
                'review' => $review
            ]);
        }

        return redirect()->back()->with('success', 'Рецензия обновлена!');
    }

    /**
     * Remove the specified review
     */
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        
        $book = $review->book;
        $review->delete();

        // Обновляем рейтинг книги
        $book->updateRating();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Рецензия удалена!'
            ]);
        }

        return redirect()->back()->with('success', 'Рецензия удалена!');
    }
}
