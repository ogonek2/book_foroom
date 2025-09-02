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
     * Store a new review (для авторизованих користувачів)
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
            'content' => $request->content,
            'rating' => $request->rating,
            'book_id' => $book->id,
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
     * Store a reply to a review (для всіх користувачів)
     */
    public function storeReply(Request $request, Book $book, Review $review)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        // Визначаємо, чи авторизований користувач
        $userId = Auth::check() ? Auth::id() : null;

        $reply = Review::create([
            'content' => $request->content,
            'rating' => null, // Відповіді не мають рейтингу
            'book_id' => $book->id,
            'user_id' => $userId, // null для гостей, ID користувача для авторизованих
            'parent_id' => $review->id,
        ]);

        // Оновлюємо лічильник відповідей
        $review->updateRepliesCount();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Відповідь додано!',
                'reply' => $reply->load('user')
            ]);
        }

        return redirect()->back()->with('success', 'Відповідь додано!');
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
