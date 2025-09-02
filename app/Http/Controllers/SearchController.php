<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Topic;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $results = collect();

        if ($query) {
            // Search books
            $books = Book::where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('author', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })->with('category')->limit(10)->get();

            // Search topics
            $topics = Topic::where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })->with(['category', 'user'])->limit(10)->get();

            $results = $results->merge($books)->merge($topics);
        }

        return view('search.index', compact('query', 'results'));
    }
}
