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
        $query = Book::with('categories');

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->whereHas('categories', function ($q) use ($request) {
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
        
        // Get user's libraries for the add to library modal
        $userLibraries = collect();
        if (auth()->check()) {
            $userLibraries = auth()->user()->libraries()->orderBy('name')->get();
        }

        return view('books.index', compact('books', 'categories', 'userLibraries'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load('categories');
        
        // Get current user's reading status for this book
        $currentReadingStatus = null;
        $userLibraries = collect();
        $bookLibraries = collect();
        $userReview = null;
        
        if (auth()->check()) {
            $currentReadingStatus = $book->getReadingStatusForUser(auth()->id());
            
            // Get user's existing review for this book
            $userReview = $book->reviews()
                ->where('user_id', auth()->id())
                ->whereNull('parent_id') // Only main reviews, not comments
                ->first();
            
            // Get user's libraries
            try {
                $userLibraries = auth()->user()->libraries()->orderBy('name')->get();
                
                // Get libraries that contain this book
                $bookLibraries = $book->libraries()
                    ->where('user_id', auth()->id())
                    ->get();
            } catch (\Exception $e) {
                // If libraries table doesn't exist, use empty collections
                $userLibraries = collect();
                $bookLibraries = collect();
            }
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

        // Get quotes for this book
        $quotes = $book->quotes()
            ->with('user')
            ->where('status', 'active')
            ->where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get facts for this book
        $facts = $book->facts()
            ->with('user')
            ->where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get book prices
        $prices = $book->bookPrices()
            ->with('bookstore')
            ->where('is_available', true)
            ->orderBy('price', 'asc')
            ->get();

        // Get related books (based on shared categories)
        $categoryIds = $book->categories->pluck('id')->toArray();
        $relatedBooks = Book::whereHas('categories', function($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->where('id', '!=', $book->id)
            ->orderBy('rating', 'desc')
            ->limit(4)
            ->get();

        // Get real statistics
        $ratingDistribution = $book->getRatingDistribution();
        $readingStats = $book->getReadingStats();
        $userRating = auth()->check() ? $book->getUserRating(auth()->id()) : null;

        return view('books.show', compact(
            'book', 
            'reviews', 
            'quotes',
            'facts',
            'prices',
            'relatedBooks', 
            'currentReadingStatus', 
            'userLibraries', 
            'bookLibraries', 
            'userReview',
            'ratingDistribution',
            'readingStats',
            'userRating'
        ));
    }

    /**
     * Обновить рейтинг книги
     */
    public function updateRating(Request $request, Book $book)
    {
        \Log::info('Rating update request', [
            'book_id' => $book->id,
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
            'url' => $request->url()
        ]);

        $request->validate([
            'rating' => 'required|integer|min:1|max:10',
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Потрібна авторизація для оцінки книги'
            ], 401);
        }

        $userId = auth()->id();
        $rating = $request->input('rating');

        try {
            // Находим или создаем запись о статусе чтения книги
            $readingStatus = \App\Models\BookReadingStatus::firstOrCreate(
                [
                    'book_id' => $book->id,
                    'user_id' => $userId,
                ],
                [
                    'status' => 'read', // По умолчанию ставим статус "прочитано"
                    'finished_at' => now(),
                ]
            );

            // Обновляем рейтинг
            $readingStatus->update(['rating' => $rating]);
            
            // Синхронизируем с рецензией, если она существует
            $existingReview = \App\Models\Review::where('book_id', $book->id)
                ->where('user_id', $userId)
                ->whereNull('parent_id')
                ->first();
                
            if ($existingReview) {
                $existingReview->update(['rating' => $rating]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Помилка при оновленні оцінки: ' . $e->getMessage()
            ], 500);
        }

        $book->updateRating();

        return response()->json([
            'success' => true,
            'message' => 'Оцінку оновлено!',
            'rating' => $rating,
            'average_rating' => $book->fresh()->rating
        ]);
    }

    /**
     * Получить рейтинг пользователя для книги
     */
    public function getUserRating(Book $book)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Потрібна авторизація'
            ], 401);
        }

        $userRating = $book->getUserRating(auth()->id());

        return response()->json([
            'success' => true,
            'rating' => $userRating,
        ]);
    }

    /**
     * Get book ID by slug
     */
    public function getIdBySlug($slug)
    {
        $book = Book::where('slug', $slug)->first();
        
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }
        
        return response()->json(['id' => $book->id]);
    }

    /**
     * Add a new interesting fact to the book
     */
    public function addFact(Request $request, Book $book)
    {
        $request->validate([
            'fact' => 'required|string|max:1000|min:10',
        ]);

        // Get current facts or initialize empty array
        $facts = $book->interesting_facts ?? [];
        
        // Add new fact
        $facts[] = $request->input('fact');
        
        // Update book with new facts
        $book->update([
            'interesting_facts' => $facts
        ]);

        // Refresh the book to get updated data
        $book = $book->fresh();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Факт успішно додано!',
                'facts' => $book->interesting_facts,
                'debug' => [
                    'book_id' => $book->id,
                    'facts_count' => count($book->interesting_facts ?? []),
                    'raw_facts' => $book->interesting_facts
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Факт успішно додано!');
    }

}
