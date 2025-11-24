<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        if ($request->filled('search')) {
            $search = $request->string('search')->trim();
            $likeSearch = "%{$search}%";
            $lowerSearch = '%' . Str::lower($search) . '%';

            $query->where(function ($q) use ($likeSearch, $lowerSearch) {
                $q->where('title', 'like', $likeSearch)
                    ->orWhere('book_name_ua', 'like', $likeSearch)
                    ->orWhere('author', 'like', $likeSearch)
                    ->orWhere('annotation', 'like', $likeSearch)
                    ->orWhere(function ($synonymsQuery) use ($likeSearch, $lowerSearch) {
                        $synonymsQuery->whereNotNull('synonyms')
                            ->where(function ($inner) use ($likeSearch, $lowerSearch) {
                                $inner->where('synonyms', 'like', $likeSearch)
                                    ->orWhereRaw("JSON_SEARCH(LOWER(synonyms), 'one', ?) IS NOT NULL", [$lowerSearch]);
                            });
                    });
            });
        }

        // Rating range filter
        $ratingMin = (int) $request->input('rating_min', 1);
        $ratingMax = (int) $request->input('rating_max', 10);

        if ($ratingMin > $ratingMax) {
            [$ratingMin, $ratingMax] = [$ratingMax, $ratingMin];
        }

        $ratingMin = max(1, min(10, $ratingMin));
        $ratingMax = max(1, min(10, $ratingMax));

        if ($ratingMin > 1 || $ratingMax < 10) {
            $query->whereBetween('rating', [$ratingMin, $ratingMax]);
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
        $categories = Category::where('is_active', true)
            ->withCount('books')
            ->orderBy('name')
            ->get();
        
        // Get user's libraries for the add to library modal
        $userLibraries = collect();
        if (auth()->check()) {
            $userLibraries = auth()->user()->libraries()->orderBy('name')->get();
        }

        if ($request->expectsJson()) {
            $booksCollection = $books->getCollection()->map(function (Book $book) {
                return [
                    'id' => $book->id,
                    'slug' => $book->slug,
                    'title' => $book->title,
                    'book_name_ua' => $book->book_name_ua,
                    'author' => $book->author,
                    'cover_image' => $book->cover_image,
                    'rating' => (float) $book->rating,
                    'reviews_count' => (int) $book->reviews_count,
                    'pages' => (int) $book->pages,
                    'publication_year' => $book->publication_year,
                    'categories' => $book->categories->pluck('name'),
                ];
            });

            return response()->json([
                'data' => $booksCollection,
                'meta' => [
                    'current_page' => $books->currentPage(),
                    'last_page' => $books->lastPage(),
                    'per_page' => $books->perPage(),
                    'total' => $books->total(),
                ],
            ]);
        }

        return view('books.index', compact('books', 'categories', 'userLibraries'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load(['categories', 'author']);

        $authorModel = $book->getRelation('author');
        
        // Get current user's reading status for this book
        $currentReadingStatus = null;
        $userLibraries = collect();
        $bookLibraries = collect();
        $userReview = null;
        
        if (auth()->check()) {
            $currentReadingStatus = $book->getReadingStatusForUser(auth()->id());
            
            // Get user's existing review for this book (excluding drafts)
            $userReview = $book->reviews()
                ->where('user_id', auth()->id())
                ->whereNull('parent_id') // Only main reviews, not comments
                ->where('is_draft', false) // Exclude drafts
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
        
        // Get main reviews (not replies) with nested replies (excluding drafts)
        $reviews = $book->reviews()
            ->whereNull('parent_id')
            ->where('is_draft', false) // Exclude drafts
            ->with([
                'user', 
                'replies' => function($query) {
                    $query->where('is_draft', false); // Exclude draft replies
                },
                'replies.user',
                'replies.replies' => function($query) {
                    $query->where('is_draft', false);
                },
                'replies.replies.user',
                'replies.replies.replies' => function($query) {
                    $query->where('is_draft', false);
                },
                'replies.replies.replies.user',
                'replies.replies.replies.replies' => function($query) {
                    $query->where('is_draft', false);
                },
                'replies.replies.replies.replies.user' // Добавляем четвертый уровень
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get quotes for this book (excluding drafts)
        $quotes = $book->quotes()
            ->with('user')
            ->where('status', 'active')
            ->where('is_public', true)
            ->where('is_draft', false) // Exclude drafts
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
            'authorModel',
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

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Потрібна авторизація для оцінки книги'
            ], 401);
        }

        try {
            $request->validate([
                'rating' => 'required|integer|min:1|max:10',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Невірне значення рейтингу',
                'errors' => $e->errors()
            ], 422);
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
            
            // Синхронизируем с рецензией, если она существует (excluding drafts)
            $existingReview = \App\Models\Review::where('book_id', $book->id)
                ->where('user_id', $userId)
                ->whereNull('parent_id')
                ->where('is_draft', false) // Exclude drafts
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

    /**
     * Search suggestions for autocomplete
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q', '');
        $limit = (int) $request->get('limit', 5);

        if (strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $searchTerm = trim($query);
        $likeSearch = "%{$searchTerm}%";
        $lowerSearch = '%' . Str::lower($searchTerm) . '%';

        $books = Book::where(function ($q) use ($likeSearch, $lowerSearch, $searchTerm) {
                // Exact match on title (highest priority)
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
                    ELSE 4
                END
            ", ["%{$searchTerm}%", "%{$searchTerm}%", "%{$searchTerm}%"])
            ->limit($limit)
            ->get();

        $results = $books->map(function (Book $book) {
            return [
                'id' => $book->id,
                'slug' => $book->slug,
                'title' => $book->title,
                'book_name_ua' => $book->book_name_ua,
                'author' => $book->author,
                'cover_image' => $book->cover_image_display ?? $book->cover_image,
                'rating' => (float) ($book->rating ?? 0),
                'reviews_count' => (int) ($book->reviews_count ?? 0),
            ];
        });

        return response()->json(['data' => $results]);
    }

}
