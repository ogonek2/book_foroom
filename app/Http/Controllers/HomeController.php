<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'books' => Book::count(),
            'reviews' => \App\Models\Review::count(),
            'topics' => Topic::count(),
            'users' => User::count(),
        ];

        $featuredBooks = Book::where('is_featured', true)
            ->with('category')
            ->orderBy('rating', 'desc')
            ->limit(6)
            ->get();

        $recentTopics = Topic::with(['user', 'category'])
            ->orderBy('last_activity_at', 'desc')
            ->limit(5)
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount('topics')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return view('home', compact('stats', 'featuredBooks', 'recentTopics', 'categories'));
    }
}
