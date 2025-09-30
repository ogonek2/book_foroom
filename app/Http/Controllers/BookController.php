<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::with('category');

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sort = $request->get('sort', 'rating');
        switch ($sort) {
            case 'title':
                $query->orderBy('title');
                break;
            case 'author':
                $query->orderBy('author');
                break;
            case 'year':
                $query->orderBy('publication_year', 'desc');
                break;
            case 'reviews':
                $query->orderBy('reviews_count', 'desc');
                break;
            default:
                $query->orderBy('rating', 'desc');
        }

        $books = $query->paginate(12);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('books.index', compact('books', 'categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load('category');
        
        // Get current user's reading status for this book
        $currentReadingStatus = null;
        if (auth()->check()) {
            $currentReadingStatus = $book->getReadingStatusForUser(auth()->id());
        }
        
        // Get main reviews (not replies) with nested replies
        $reviews = $book->reviews()
            ->whereNull('parent_id')
            ->with([
                'user', 
                'replies.user',
                'replies.replies.user',
                'replies.replies.replies.user',
                'replies.replies.replies.replies.user' // Добавляем четвертый уровень
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get related books
        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->orderBy('rating', 'desc')
            ->limit(4)
            ->get();

        return view('books.show', compact('book', 'reviews', 'relatedBooks', 'currentReadingStatus'));
    }
}
