<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
{
    /**
     * Store a new quote
     */
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'page_number' => 'nullable|integer|min:1',
            'is_public' => 'boolean',
        ]);

        $quote = Quote::create([
            'content' => $request->input('content'),
            'page_number' => $request->input('page_number'),
            'is_public' => $request->input('is_public', true),
            'book_id' => $book->id,
            'user_id' => Auth::id(),
            'status' => 'active',
        ]);

        // Загружаем связанные данные
        $quote->load('user');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Цитату додано!',
                'quote' => [
                    'id' => $quote->id,
                    'content' => $quote->content,
                    'page_number' => $quote->page_number,
                    'is_public' => $quote->is_public,
                    'created_at' => $quote->created_at->toISOString(),
                    'user_id' => $quote->user_id,
                    'user' => $quote->user ? [
                        'id' => $quote->user->id,
                        'name' => $quote->user->name,
                        'username' => $quote->user->username,
                        'avatar_display' => $quote->user->avatar_display ?? null,
                    ] : null,
                    'is_liked_by_current_user' => false,
                    'likes_count' => 0,
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Цитату додано!');
    }

    /**
     * Toggle like for a quote
     */
    public function toggleLike(Book $book, Quote $quote)
    {
        $user = auth()->user();
        
        // Check if user already liked this quote
        $existingLike = $quote->likes()->where('user_id', $user->id)->first();
        
        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $isLiked = false;
        } else {
            // Like
            $quote->likes()->create([
                'user_id' => $user->id,
                'vote' => 1
            ]);
            $isLiked = true;
        }
        
        // Update likes count
        $likesCount = $quote->likes()->where('vote', 1)->count();
        
        return response()->json([
            'success' => true,
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }

    /**
     * Update a quote
     */
    public function update(Request $request, Book $book, Quote $quote)
    {
        // Check authorization
        if ($quote->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Ви не маєте прав для редагування цієї цитати.'
            ], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'page_number' => 'nullable|integer|min:1',
            'is_public' => 'boolean',
        ]);

        $quote->update([
            'content' => $request->input('content'),
            'page_number' => $request->input('page_number'),
            'is_public' => $request->input('is_public', true),
        ]);

        // Загружаем связанные данные
        $quote->load('user');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Цитату оновлено!',
                'quote' => [
                    'id' => $quote->id,
                    'content' => $quote->content,
                    'page_number' => $quote->page_number,
                    'is_public' => $quote->is_public,
                    'created_at' => $quote->created_at->toISOString(),
                    'user_id' => $quote->user_id,
                    'user' => $quote->user ? [
                        'id' => $quote->user->id,
                        'name' => $quote->user->name,
                        'username' => $quote->user->username,
                        'avatar_display' => $quote->user->avatar_display ?? null,
                    ] : null,
                    'is_liked_by_current_user' => $quote->isLikedBy(Auth::id()),
                    'likes_count' => $quote->likes()->where('vote', 1)->count(),
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Цитату оновлено!');
    }

    /**
     * Delete a quote
     */
    public function destroy(Book $book, Quote $quote)
    {
        // Check authorization
        if ($quote->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Ви не маєте прав для видалення цієї цитати.'
            ], 403);
        }

        $quote->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Цитату видалено!'
            ]);
        }

        return redirect()->back()->with('success', 'Цитату видалено!');
    }
}
