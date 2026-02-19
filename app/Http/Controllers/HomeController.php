<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use App\Models\Review;
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
            ->where('review_type', 'review') // Только рецензии, без отзывов
            ->where('is_draft', false) // Без черновиков
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Получаем рекомендуемые книги для слайдера
        $recommendedBooks = Book::with('categories')
            ->orderBy('rating', 'desc')
            ->limit(3)
            ->get();

        return view('home', compact('stats', 'featuredBooks', 'recentReviews', 'recommendedBooks'));
    }

    public function contacts()
    {
        return view('contacts');
    }

    public function rules()
    {
        return view('rules');
    }

    public function privacy()
    {
        return view('privacy');
    }

    public function terms()
    {
        return view('terms');
    }

    public function sitemap()
    {
        $sitemapPath = public_path('sitemap.xml');
        
        if (!file_exists($sitemapPath)) {
            \Artisan::call('sitemap:generate');
        }
        
        return response()->file($sitemapPath, [
            'Content-Type' => 'application/xml'
        ]);
    }
}
