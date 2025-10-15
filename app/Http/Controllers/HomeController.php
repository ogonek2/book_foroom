<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use App\Models\Review;
use App\Models\Quote;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'books' => Book::count(),
            'reviews' => \App\Models\Review::count(),
            'users' => User::count(),
        ];

        $featuredBooks = Book::where('is_featured', true)
            ->with('categories')
            ->orderBy('rating', 'desc')
            ->limit(6)
            ->get();

        $recentReviews = Review::with(['book.categories', 'user'])
            ->whereNull('parent_id') // Только основные рецензии, не ответы
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Получаем рекомендуемые книги для слайдера
        $recommendedBooks = Book::with('categories')
            ->orderBy('rating', 'desc')
            ->limit(3)
            ->get();

        // Получаем публичные цитаты
        $featuredQuotes = Quote::where('is_public', true)
            ->with(['user', 'book'])
            ->withCount('likes')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($quote) {
                return [
                    'id' => $quote->id,
                    'content' => $quote->content,
                    'page_number' => $quote->page_number,
                    'is_public' => $quote->is_public,
                    'created_at' => $quote->created_at,
                    'user' => $quote->user ? [
                        'id' => $quote->user->id,
                        'name' => $quote->user->name,
                        'avatar_display' => $quote->user->avatar_display
                    ] : null,
                    'book_title' => $quote->book ? $quote->book->title : 'Без назви книги',
                    'book_slug' => $quote->book ? $quote->book->slug : null,
                    'likes_count' => $quote->likes_count,
                    'is_liked' => auth()->check() ? $quote->likes()->where('user_id', auth()->id())->exists() : false
                ];
            });

        return view('home', compact('stats', 'featuredBooks', 'recentReviews', 'recommendedBooks', 'featuredQuotes'));
    }
}
