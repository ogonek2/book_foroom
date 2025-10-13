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
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return view('home', compact('stats', 'featuredBooks', 'recentReviews', 'recommendedBooks', 'featuredQuotes'));
    }
}
