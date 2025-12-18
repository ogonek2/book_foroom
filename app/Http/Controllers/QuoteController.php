<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
{
    /**
     * Display all quotes for a book (page with list).
     */
    public function index(Request $request, Book $book)
    {
        $book->load(['categories', 'author']);

        $quotesQuery = $book->quotes()
            ->where('is_draft', false)
            ->where('is_public', true)
            ->with('user')
            ->orderByDesc('created_at');

        if ($request->expectsJson()) {
            $perPage = (int) $request->input('per_page', 10);
            $perPage = max(1, min(50, $perPage));

            return response()->json(
                $quotesQuery->paginate($perPage)
            );
        }

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
                'is_liked_by_current_user' => $isAuthenticated ? $quote->isLikedBy($currentUserId) : false,
                'is_favorited_by_current_user' => $isAuthenticated ? $quote->isFavoritedBy($currentUserId) : false,
                'likes_count' => $quote->likes()->where('vote', 1)->count(),
            ];
        })->values()->toArray();

        $ratingDistribution = $book->getRatingDistribution();
        $readingStats = $book->getReadingStats();
        $authorModel = $book->getRelation('author');

        return view('books.quotes', [
            'book' => $book,
            'authorModel' => $authorModel,
            'quotesData' => $quotesData,
            'quotesCount' => $quotesCount,
            'ratingDistribution' => $ratingDistribution,
            'readingStats' => $readingStats,
            'quotesPaginator' => $quotes,
        ]);
    }

    /**
     * Store a new quote
     */
    public function store(Request $request, Book $book)
    {
        $isDraft = $request->boolean('is_draft', false);

        $request->validate([
            'content' => 'required|string|min:20|max:500',
            'page_number' => 'nullable|integer|min:1',
            'is_public' => 'boolean',
        ], [
            'content.min' => 'Цитата повинна містити мінімум 20 символів.',
            'content.max' => 'Цитата повинна містити максимум 500 символів.',
        ]);

        $quote = Quote::create([
            'content' => $request->input('content'),
            'page_number' => $request->input('page_number'),
            'is_public' => $request->input('is_public', true),
            'book_id' => $book->id,
            'user_id' => Auth::id(),
            'status' => 'active',
            'is_draft' => $isDraft,
        ]);

        // Загружаем связанные данные
        $quote->load('user');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $isDraft ? 'Чернетку збережено!' : 'Цитату додано!',
                'quote' => [
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
                    'is_liked_by_current_user' => false,
                    'likes_count' => 0,
                ]
            ]);
        }

        return redirect()->back()->with('success', $isDraft ? 'Чернетку збережено!' : 'Цитату додано!');
    }

    /**
     * Toggle like for a quote
     */
    public function toggleLike(Book $book, Quote $quote)
    {
        $user = auth()->user();
        $likesBefore = $quote->likes()->where('vote', 1)->count();
        
        // Check if user already liked this quote
        $existingLike = $quote->likes()->where('user_id', $user->id)->first();
        
        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $isLiked = false;
        } else {
            // Like
            $quote->likes()->create([
                'user_id' => $user->id,
                'vote' => 1
            ]);
            $isLiked = true;

            // Уведомление о лайке цитати
            if ($quote->user_id && $quote->user_id !== $user->id) {
                \App\Services\NotificationService::createLikeNotification($quote, $user);
            }
        }
        
        // Update likes count
        $likesCount = $quote->likes()->where('vote', 1)->count();

        // Поріг 20 лайків для цитати (email + notification)
        if ($isLiked && $likesBefore < 20 && $likesCount >= 20 && $quote->user_id && $quote->user_id !== $user->id) {
            \App\Helpers\UserNotificationHelper::send(
                'quote_like_milestone',
                $quote->user,
                $user,
                [
                    'likes_count' => $likesCount,
                    'quote_id'    => $quote->id,
                    'book_id'     => $quote->book_id ?? null,
                ],
                $quote
            );
        }
        
        return response()->json([
            'success' => true,
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }

    /**
     * Get quote data (including drafts) for editing
     */
    public function getQuoteData(Book $book, Quote $quote)
    {
        // Проверяем права доступа - только владелец может редактировать черновик
        if ($quote->user_id !== Auth::id()) {
            abort(403, 'У вас немає прав для редагування цієї цитати');
        }

        // Проверяем, что цитата принадлежит этой книге
        if ($quote->book_id !== $book->id) {
            abort(404, 'Цитату не знайдено');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'quote' => [
                    'id' => $quote->id,
                    'content' => $quote->content,
                    'page_number' => $quote->page_number,
                    'is_public' => $quote->is_public,
                    'is_draft' => $quote->is_draft,
                    'book_slug' => $book->slug, // Добавляем slug книги для удобства
                ]
            ]);
        }

        return redirect()->back();
    }

    /**
     * Update a quote
     */
    public function update(Request $request, Book $book, Quote $quote)
    {
        // Check authorization
        if ($quote->user_id !== Auth::id()) {
            if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Ви не маєте прав для редагування цієї цитати.'
            ], 403);
            }
            abort(403, 'Ви не маєте прав для редагування цієї цитати.');
        }

        // Определяем действие: publish или save
        $action = $request->input('action');
        $isDraft = ($action === 'save' || ($request->boolean('is_draft', false) && $action !== 'publish')) ? true : false;

        $request->validate([
            'content' => 'required|string|min:20|max:500',
            'page_number' => 'nullable|integer|min:1',
            'is_public' => 'boolean',
        ], [
            'content.min' => 'Цитата повинна містити мінімум 20 символів.',
            'content.max' => 'Цитата повинна містити максимум 500 символів.',
        ]);

        $quote->update([
            'content' => $request->input('content'),
            'page_number' => $request->input('page_number'),
            'is_public' => $request->input('is_public', true),
            'is_draft' => $isDraft,
        ]);

        // Загружаем связанные данные
        $quote->load('user');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $isDraft ? 'Чернетку оновлено!' : 'Цитату оновлено!',
                'quote' => [
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
                    'is_liked_by_current_user' => $quote->isLikedBy(Auth::id()),
                    'likes_count' => $quote->likes()->where('vote', 1)->count(),
                ]
            ]);
        }

        // Редирект в зависимости от действия
        if ($isDraft) {
            return redirect()->route('profile.show', ['tab' => 'drafts'])->with('success', 'Чернетку оновлено!');
        } else {
            return redirect()->route('books.show', $book->slug)->with('success', 'Цитату оновлено!');
        }
    }

    /**
     * Delete a quote
     */
    public function destroy(Book $book, Quote $quote)
    {
        // Check authorization
        if ($quote->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Ви не маєте прав для видалення цієї цитати.'
            ], 403);
        }

        $quote->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Цитату видалено!'
            ]);
        }

        return redirect()->back()->with('success', 'Цитату видалено!');
    }

    /**
     * Toggle favorite for a quote
     */
    public function toggleFavorite(Book $book, Quote $quote)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Необхідно авторизуватись'
            ], 401);
        }
        
        $isFavorited = $quote->isFavoritedBy($user->id);
        
        if ($isFavorited) {
            // Remove from favorites
            $quote->favoritedByUsers()->detach($user->id);
            $isFavorited = false;
        } else {
            // Add to favorites
            $quote->favoritedByUsers()->attach($user->id);
            $isFavorited = true;
        }
        
        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited,
            'message' => $isFavorited ? 'Цитату додано до избранного!' : 'Цитату видалено з избранного!'
        ]);
    }

    /**
     * Show the form for editing a draft quote (alternative page without modal)
     */
    public function editDraft(Book $book, Quote $quote)
    {
        try {
            // Проверяем права доступа - только владелец может редактировать черновик
            if ($quote->user_id !== Auth::id()) {
                abort(403, 'У вас немає прав для редагування цієї цитати');
            }

            // Проверяем, что это черновик
            if (!$quote->is_draft) {
                return redirect()->route('books.show', $book);
            }

            // Проверяем, что цитата принадлежит этой книге
            if ($quote->book_id !== $book->id) {
                abort(404, 'Цитату не знайдено');
            }

            // Загружаем связь с книгой, если она не загружена
            if (!$quote->relationLoaded('book')) {
                $quote->load('book');
            }

            // Убеждаемся, что книга существует
            if (!$book || !$book->exists) {
                abort(404, 'Книгу не знайдено');
            }

            return view('profile.private.edit-draft-quote', compact('book', 'quote'));
        } catch (\Exception $e) {
            \Log::error('Error in editDraft: ' . $e->getMessage(), [
                'book_id' => $book->id ?? null,
                'quote_id' => $quote->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
