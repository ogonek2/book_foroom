<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Review;
use App\Models\Quote;
use App\Models\Fact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $query = Author::withCount('books');

        // Поиск
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Фильтр по букве
        if ($request->filled('letter')) {
            $query->byLetter($request->letter);
        }

        // Фильтр по национальности
        if ($request->filled('nationality')) {
            $query->byNationality($request->nationality);
        }

        // Сортировка
        $sortBy = $request->get('sort', 'last_name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortBy, ['last_name', 'first_name', 'birth_date', 'books_count'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $authors = $query->paginate(24);

        // Получаем уникальные национальности для фильтра
        $nationalities = Author::select('nationality')
            ->whereNotNull('nationality')
            ->distinct()
            ->orderBy('nationality')
            ->pluck('nationality');

        // Получаем буквы для алфавитного указателя
        $letters = Author::selectRaw('UPPER(SUBSTRING(last_name, 1, 1)) as letter')
            ->distinct()
            ->orderBy('letter')
            ->pluck('letter');

        return view('authors.index', compact('authors', 'nationalities', 'letters'));
    }

    public function show(Author $author)
    {
        $author->loadCount('books');
        
        // Загружаем книги с категориями
        $books = $author->books()->with('categories')->paginate(12);
        
        // Получаем библиотеки текущего пользователя
        $userLibraries = [];
        if (auth()->check()) {
            $userLibraries = auth()->user()->libraries()->with('books')->get();
        }
        
        // Получаем все книги автора для статистики
        $allBooks = $author->books()->with([
            'categories',
            'reviews.user',
            'reviews.book',
            'quotes.user',
            'quotes.book',
            'facts.user',
            'facts.book'
        ])->withCount('reviews')->get();
        
        // Статистика из книг автора
        $stats = [
            'total_books' => $allBooks->count(),
            'total_reviews' => $allBooks->sum(function($book) {
                // Считаем только главные рецензии (не ответы)
                return $book->reviews->where('parent_id', null)->count();
            }),
            'total_quotes' => $allBooks->sum(function($book) {
                return $book->quotes->count();
            }),
            'total_facts' => $allBooks->sum(function($book) {
                return $book->facts->count();
            }),
            'average_rating' => $allBooks->avg('display_rating') ?? 0,
        ];
        
        // Получаем цитаты из всех книг автора (последние 4)
        $quotes = collect();
        foreach ($allBooks as $book) {
            $quotes = $quotes->merge($book->quotes->take(2)); // Берем по 2 цитаты с каждой книги
        }
        $quotes = $quotes->sortByDesc('created_at')->take(4);
        
        // Получаем факты из всех книг автора (последние 4)
        $facts = collect();
        foreach ($allBooks as $book) {
            $facts = $facts->merge($book->facts->take(2)); // Берем по 2 факта с каждой книги
        }
        $facts = $facts->sortByDesc('created_at')->take(4);
        
        // Получаем рецензии из всех книг автора (последние 5)
        // Только главные рецензии (не ответы), у которых есть оценка
        $reviews = collect();
        foreach ($allBooks as $book) {
            // Загружаем только главные рецензии (parent_id = null) с оценкой
            $bookReviews = $book->reviews()
                ->whereNull('parent_id') // Только главные рецензии, не ответы
                ->whereNotNull('rating') // Только те, у которых есть оценка
                ->with(['user', 'book'])
                ->withCount(['likes', 'replies'])
                ->take(2)
                ->get();
            $reviews = $reviews->merge($bookReviews);
        }
        $reviews = $reviews->sortByDesc('created_at')->take(5);
        
        return view('authors.show', compact('author', 'books', 'stats', 'quotes', 'facts', 'reviews', 'userLibraries'));
    }

    /**
     * Усі рецензії на книги автора
     */
    public function reviews(Request $request, Author $author)
    {
        $author->loadCount('books');

        $reviewsQuery = Review::whereHas('book', function ($q) use ($author) {
                $q->where('author_id', $author->id);
            })
            ->whereNull('parent_id')
            ->where('is_draft', false)
            ->with(['user', 'book'])
            ->orderByDesc('created_at');

        $perPage = 10;
        $reviews = $reviewsQuery->paginate($perPage);
        $reviewsCount = $reviews->total();

        $isAuthenticated = Auth::check();
        $currentUserId = Auth::id();

        $reviewsData = collect($reviews->items())->map(function ($review) use ($isAuthenticated, $currentUserId) {
            return [
                'id' => $review->id,
                'content' => $review->content,
                'rating' => $review->rating,
                'created_at' => $review->created_at->toISOString(),
                'user_id' => $review->user_id,
                'is_guest' => $review->isGuest(),
                'book_slug' => $review->book->slug,
                'user' => $review->user ? [
                    'id' => $review->user->id,
                    'name' => $review->user->name,
                    'username' => $review->user->username,
                    'avatar_display' => $review->user->avatar_display ?? null,
                ] : null,
                'book' => [
                    'id' => $review->book->id,
                    'title' => $review->book->title,
                    'slug' => $review->book->slug,
                ],
                'is_liked' => $isAuthenticated ? $review->isLikedBy($currentUserId) : false,
                'is_favorited' => $isAuthenticated ? $review->isFavoritedBy($currentUserId) : false,
                'likes_count' => $review->likes_count ?? 0,
                'replies_count' => $review->replies_count ?? 0,
                'contains_spoiler' => $review->contains_spoiler ?? false,
                'review_type' => $review->review_type ?? null,
                'opinion_type' => $review->opinion_type ?? null,
                'book_type' => $review->book_type ?? null,
                'language' => $review->language ?? null,
            ];
        })->values()->toArray();

        return view('authors.reviews', [
            'author' => $author,
            'reviewsData' => $reviewsData,
            'reviewsCount' => $reviewsCount,
            'reviewsPaginator' => $reviews,
        ]);
    }

    /**
     * Усі цитати з книг автора
     */
    public function quotes(Request $request, Author $author)
    {
        $author->loadCount('books');

        $quotesQuery = Quote::whereHas('book', function ($q) use ($author) {
                $q->where('author_id', $author->id);
            })
            ->where('status', 'active')
            ->where('is_public', true)
            ->where('is_draft', false)
            ->with(['user', 'book'])
            ->orderByDesc('created_at');

        $perPage = 10;
        $quotes = $quotesQuery->paginate($perPage);
        $quotesCount = $quotes->total();

        $isAuthenticated = Auth::check();
        $currentUserId = Auth::id();

        $quotesData = collect($quotes->items())->map(function ($quote) use ($isAuthenticated, $currentUserId) {
            return [
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
                'book' => [
                    'id' => $quote->book->id,
                    'title' => $quote->book->title,
                    'slug' => $quote->book->slug,
                ],
                'is_liked_by_current_user' => $isAuthenticated ? $quote->isLikedBy($currentUserId) : false,
                'is_favorited_by_current_user' => $isAuthenticated && method_exists($quote, 'isFavoritedBy')
                    ? $quote->isFavoritedBy($currentUserId)
                    : false,
                'likes_count' => $quote->likes()->where('vote', 1)->count(),
            ];
        })->values()->toArray();

        return view('authors.quotes', [
            'author' => $author,
            'quotesData' => $quotesData,
            'quotesCount' => $quotesCount,
            'quotesPaginator' => $quotes,
        ]);
    }

    /**
     * Усі факти з книг автора
     */
    public function facts(Request $request, Author $author)
    {
        $author->loadCount('books');

        $factsQuery = Fact::whereHas('book', function ($q) use ($author) {
                $q->where('author_id', $author->id);
            })
            ->where('is_public', true)
            ->with(['user', 'book'])
            ->orderByDesc('created_at');

        $perPage = 10;
        $facts = $factsQuery->paginate($perPage);
        $factsCount = $facts->total();

        $isAuthenticated = Auth::check();
        $currentUserId = Auth::id();

        $factsData = collect($facts->items())->map(function ($fact) use ($isAuthenticated, $currentUserId) {
            return [
                'id' => $fact->id,
                'content' => $fact->content,
                'user_id' => $fact->user_id,
                'user' => [
                    'id' => $fact->user->id,
                    'name' => $fact->user->name,
                    'username' => $fact->user->username,
                    'avatar_display' => $fact->user->avatar_display,
                ],
                'book' => [
                    'id' => $fact->book->id,
                    'title' => $fact->book->title,
                    'slug' => $fact->book->slug,
                ],
                'is_liked_by_current_user' => $isAuthenticated ? $fact->isLikedBy($currentUserId) : false,
                'likes_count' => $fact->likes()->where('vote', 1)->count(),
                'created_at' => $fact->created_at->toISOString(),
            ];
        })->values()->toArray();

        return view('authors.facts', [
            'author' => $author,
            'factsData' => $factsData,
            'factsCount' => $factsCount,
            'factsPaginator' => $facts,
        ]);
    }
}
