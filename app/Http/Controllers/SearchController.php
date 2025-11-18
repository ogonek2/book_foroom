<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $books = collect();

        if ($query && strlen(trim($query)) >= 2) {
            $searchTerm = trim($query);
            $likeSearch = "%{$searchTerm}%";
            $lowerSearch = '%' . Str::lower($searchTerm) . '%';

            // Smart search with fuzzy matching
            $books = Book::where(function ($q) use ($likeSearch, $lowerSearch, $searchTerm) {
                    // Search in multiple fields
                    $q->where('title', 'like', $likeSearch)
                        ->orWhere('book_name_ua', 'like', $likeSearch)
                        ->orWhere('author', 'like', $likeSearch)
                        ->orWhere('publisher', 'like', $likeSearch)
                        ->orWhere('annotation', 'like', $likeSearch)
                        ->orWhere(function ($synonymsQuery) use ($likeSearch, $lowerSearch) {
                            $synonymsQuery->whereNotNull('synonyms')
                                ->where(function ($inner) use ($likeSearch, $lowerSearch) {
                                    $inner->where('synonyms', 'like', $likeSearch)
                                        ->orWhereRaw("JSON_SEARCH(LOWER(synonyms), 'one', ?) IS NOT NULL", [$lowerSearch]);
                                });
                        });
                })
                ->with('categories')
                ->orderByRaw("
                    CASE 
                        WHEN title LIKE ? THEN 1
                        WHEN book_name_ua LIKE ? THEN 2
                        WHEN author LIKE ? THEN 3
                        WHEN publisher LIKE ? THEN 4
                        ELSE 5
                    END
                ", ["%{$searchTerm}%", "%{$searchTerm}%", "%{$searchTerm}%", "%{$searchTerm}%"])
                ->orderBy('rating', 'desc')
                ->paginate(20);
        }

        // Get user's libraries for the add to library modal
        $userLibraries = collect();
        if (auth()->check()) {
            $userLibraries = auth()->user()->libraries()->orderBy('name')->get();
        }

        return view('search.index', compact('query', 'books', 'userLibraries'));
    }
}
