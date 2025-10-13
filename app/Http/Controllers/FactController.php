<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Fact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FactController extends Controller
{
    /**
     * Store a newly created fact.
     */
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'content' => 'required|string|max:1000|min:10',
        ]);

        $fact = Fact::create([
            'content' => $request->input('content'),
            'book_id' => $book->id,
            'user_id' => Auth::id(),
            'is_public' => true,
        ]);

        // Load relationships for response
        $fact->load('user');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Факт успішно додано!',
                'fact' => [
                    'id' => $fact->id,
                    'content' => $fact->content,
                    'user_id' => $fact->user_id,
                    'user' => [
                        'id' => $fact->user->id,
                        'name' => $fact->user->name,
                        'username' => $fact->user->username,
                        'avatar_display' => $fact->user->avatar_display,
                    ],
                    'is_liked_by_current_user' => false,
                    'likes_count' => 0,
                    'created_at' => $fact->created_at->toISOString(),
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Факт успішно додано!');
    }

    /**
     * Toggle like for a fact.
     */
    public function toggleLike(Request $request, Book $book, Fact $fact)
    {
        $user = Auth::user();
        $existingLike = $fact->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            if ($existingLike->vote === 1) {
                // Unlike
                $existingLike->delete();
                $isLiked = false;
            } else {
                // Change from dislike to like
                $existingLike->update(['vote' => 1]);
                $isLiked = true;
            }
        } else {
            // Create new like
            $fact->likes()->create([
                'user_id' => $user->id,
                'vote' => 1,
            ]);
            $isLiked = true;
        }

        // Update likes count
        $likesCount = $fact->likes()->where('vote', 1)->count();

        return response()->json([
            'success' => true,
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }

    /**
     * Update the specified fact.
     */
    public function update(Request $request, Book $book, Fact $fact)
    {
        $this->authorize('update', $fact);

        $request->validate([
            'content' => 'required|string|max:1000|min:10',
        ]);

        $fact->update([
            'content' => $request->input('content'),
        ]);

        // Загружаем обновленные данные
        $fact->load('user');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Факт успішно оновлено!',
                'fact' => [
                    'id' => $fact->id,
                    'content' => $fact->content,
                    'user_id' => $fact->user_id,
                    'user' => [
                        'id' => $fact->user->id,
                        'name' => $fact->user->name,
                        'username' => $fact->user->username,
                        'avatar_display' => $fact->user->avatar_display,
                    ],
                    'is_liked_by_current_user' => Auth::check() ? $fact->isLikedBy(Auth::id()) : false,
                    'likes_count' => $fact->likes()->where('vote', 1)->count(),
                    'created_at' => $fact->created_at->toISOString(),
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Факт успішно оновлено!');
    }

    /**
     * Remove the specified fact.
     */
    public function destroy(Request $request, Book $book, Fact $fact)
    {
        $this->authorize('delete', $fact);

        $fact->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Факт успішно видалено!'
            ]);
        }

        return redirect()->back()->with('success', 'Факт успішно видалено!');
    }
}
