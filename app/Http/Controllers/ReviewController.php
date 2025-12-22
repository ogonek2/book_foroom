<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReviewController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display reviews list for a book or redirect to the book page.
     */
    public function index(Request $request, Book $book)
    {
        $book->load(['categories', 'author']);

        $reviewsQuery = $book->reviews()
            ->whereNull('parent_id')
            ->where('is_draft', false)
            ->with(['user'])
            ->orderByDesc('created_at');

        if ($request->expectsJson()) {
            $perPage = (int) $request->input('per_page', 10);
            $perPage = max(1, min(50, $perPage));

            return response()->json(
                $reviewsQuery->paginate($perPage)
            );
        }

        // Для веб-версії используем пагинацию
        $perPage = 10;
        $reviews = $reviewsQuery->paginate($perPage);
        $reviewsCount = $reviews->total();

        $userReview = null;
        if (Auth::check()) {
            $userReview = $book->reviews()
                ->where('user_id', Auth::id())
                ->whereNull('parent_id')
                ->where('is_draft', false)
                ->first();
        }

        $isAuthenticated = Auth::check();
        $currentUserId = Auth::id();

        $reviewsData = collect($reviews->items())->map(function ($review) use ($isAuthenticated, $currentUserId, $book) {
            return [
                'id' => $review->id,
                'content' => $review->content,
                'rating' => $review->rating,
                'created_at' => $review->created_at->toISOString(),
                'user_id' => $review->user_id,
                'is_guest' => $review->isGuest(),
                'book_slug' => $book->slug,
                'user' => $review->user
                    ? [
                        'id' => $review->user->id,
                        'name' => $review->user->name,
                        'username' => $review->user->username,
                        'avatar_display' => $review->user->avatar_display ?? null,
                    ]
                    : null,
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

        $userReviewData = $userReview ? [
            'id' => $userReview->id,
            'content' => $userReview->content,
            'rating' => $userReview->rating,
            'book_slug' => $book->slug,
        ] : null;

        $ratingDistribution = $book->getRatingDistribution();
        $readingStats = $book->getReadingStats();

        $authorModel = $book->getRelation('author');

        return view('books.reviews', [
            'book' => $book,
            'authorModel' => $authorModel,
            'reviewsData' => $reviewsData,
            'reviewsCount' => $reviewsCount,
            'userReviewData' => $userReviewData,
            'ratingDistribution' => $ratingDistribution,
            'readingStats' => $readingStats,
            'reviewsPaginator' => $reviews,
        ]);
    }
    
    /**
     * Show the form for creating a new review
     */
    public function create(Book $book)
    {
        // Проверяем, есть ли уже рецензия от этого пользователя на эту книгу
        $existingReview = Review::where('book_id', $book->getKey())
            ->where('user_id', Auth::id())
            ->whereNull('parent_id')
            ->where('is_draft', false)
            ->first();

        if ($existingReview) {
            return redirect()->route('books.reviews.show', [$book, $existingReview])
                ->with('info', 'Ви вже залишили рецензію на цю книгу. Ви можете редагувати існуючу рецензію.');
        }

        // Получаем статистику книги
        $ratingDistribution = $book->getRatingDistribution();
        $readingStats = $book->getReadingStats();
        $userRating = $book->getUserRating(Auth::id());
        
        // Загружаем связь с автором
        $book->load('author');
        $authorModel = $book->getRelation('author');

        return view('reviews.create', compact('book', 'ratingDistribution', 'readingStats', 'userRating', 'authorModel'));
    }
    
    /**
     * Store a new review (для авторизованих користувачів)
     */
    public function store(Request $request, Book $book)
    {
        $isDraft = $request->boolean('is_draft', false);
        
        // Определяем минимальное и максимальное количество символов в зависимости от типа
        $reviewType = $request->input('review_type', 'review');
        $minChars = ($reviewType === 'opinion') ? 25 : 800;
        $maxChars = ($reviewType === 'opinion') ? 1000 : 15000;
        
        // Validate content length (text only, not HTML)
        $content = $request->input('content');
        
        if (empty($content)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Контент рецензії не може бути порожнім.'
                ], 422);
            }
            return redirect()->back()->withErrors(['content' => 'Контент рецензії не може бути порожнім.'])->withInput();
        }
        
        // Удаляем HTML-теги, но сохраняем переносы строк как пробелы
        $contentText = strip_tags($content);
        
        // Удаляем HTML entities
        $contentText = html_entity_decode($contentText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Удаляем неразрывные пробелы и другие специальные символы
        $contentText = str_replace(["\xc2\xa0", "\u{00A0}", "\u{2009}", "\u{200A}", "\u{202F}", "\u{205F}"], ' ', $contentText);
        
        // Нормализуем пробелы: заменяем множественные пробелы, переносы строк, табы на один пробел
        // НО сохраняем пробелы между словами
        $contentText = preg_replace('/[\s\n\r\t]+/u', ' ', $contentText);
        
        // Убираем пробелы в начале и конце
        $contentText = trim($contentText);
        
        // Подсчитываем длину (используем mb_strlen для правильной работы с UTF-8)
        $contentLength = mb_strlen($contentText, 'UTF-8');
        
        if ($contentLength < $minChars || $contentLength > $maxChars) {
            $errorMessage = $reviewType === 'opinion' 
                ? ($contentLength < $minChars 
                    ? 'Відгук повинен містити мінімум 25 символів.' 
                    : 'Відгук повинен містити максимум 1000 символів.')
                : ($contentLength < $minChars 
                    ? 'Рецензія повинна містити мінімум 800 символів.' 
                    : 'Рецензія повинна містити максимум 15000 символів.');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'debug' => [
                        'content_length' => $contentLength,
                        'min_chars' => $minChars,
                        'max_chars' => $maxChars,
                        'review_type' => $reviewType
                    ]
                ], 422);
            }
            
            return redirect()->back()->withErrors(['content' => $errorMessage])->withInput();
        }
        
        $request->validate([
            'content' => 'required|string',
            'rating' => $isDraft ? 'nullable|integer|min:1|max:10' : 'nullable|integer|min:1|max:10',
            'review_type' => 'nullable|in:review,opinion',
            'opinion_type' => 'nullable|in:positive,neutral,negative',
            'book_type' => 'nullable|in:paper,electronic,audio',
            'language' => 'nullable|in:uk,en',
            'contains_spoiler' => 'nullable|boolean',
            'is_draft' => 'nullable|boolean',
        ]);

        // Для черновиков не проверяем существующие рецензии
        if (!$isDraft) {
            // Проверяем, есть ли уже рецензия от этого пользователя на эту книгу
            $existingReview = Review::where('book_id', $book->getKey())
                ->where('user_id', Auth::id())
                ->whereNull('parent_id') // Только основные рецензии, не комментарии
                ->where('is_draft', false) // Исключаем черновики
                ->first();

            if ($existingReview) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ви вже залишили рецензію на цю книгу. Ви можете редагувати існуючу рецензію.',
                        'existing_review' => $existingReview
                    ], 422);
                }

                return redirect()->back()->with('error', 'Ви вже залишили рецензію на цю книгу. Ви можете редагувати існуючу рецензію.');
            }
        }

        // Получаем рейтинг из BookReadingStatus, если он не передан в запросе
        $rating = $request->input('rating');
        if (!$rating && !$isDraft) {
            $userRating = $book->getUserRating(Auth::id());
            $rating = $userRating;
        }

        // Если нет рейтинга ни в запросе, ни в BookReadingStatus, возвращаем ошибку (только для не-черновиков)
        if (!$rating && !$isDraft) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Спочатку поставте оцінку книзі, а потім напишіть рецензію.',
                ], 422);
            }

            return redirect()->back()->with('error', 'Спочатку поставте оцінку книзі, а потім напишіть рецензію.');
        }

        // Sanitize HTML content
        $content = $this->sanitizeHTML($request->input('content'));

        $review = Review::create([
            'content' => $content,
            'rating' => $rating,
            'book_id' => $book->getKey(),
            'user_id' => Auth::id(),
            'parent_id' => null,
            'review_type' => $request->input('review_type', 'review'),
            'opinion_type' => $request->input('opinion_type', 'positive'),
            'book_type' => $request->input('book_type', 'paper'),
            'language' => $request->input('language', 'uk'),
            'contains_spoiler' => $request->boolean('contains_spoiler', false),
            'is_draft' => $isDraft,
        ]);

        // Для черновиков не обновляем рейтинги
        if (!$isDraft) {
            // Синхронизируем рейтинг с BookReadingStatus
            $readingStatus = \App\Models\BookReadingStatus::firstOrCreate(
                [
                    'book_id' => $book->getKey(),
                    'user_id' => Auth::id(),
                ],
                [
                    'status' => 'read',
                    'finished_at' => now(),
                ]
            );

            // Обновляем рейтинг в BookReadingStatus
            $readingStatus->update(['rating' => $rating]);

            // Обновляем средний рейтинг книги
            $book->updateRating();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $isDraft ? 'Чернетку збережено!' : 'Рецензію додано!',
                'review' => $review->load('user')
            ]);
        }

        return redirect()->back()->with('success', $isDraft ? 'Чернетку збережено!' : 'Рецензію додано!');
    }

    /**
     * Store a guest review (для гостей)
     */
    public function guestStore(Request $request, Book $book)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'rating' => 'required|integer|min:1|max:10',
            'review_type' => 'nullable|in:review,opinion',
            'opinion_type' => 'nullable|in:positive,neutral,negative',
            'book_type' => 'nullable|in:paper,electronic,audio',
            'language' => 'nullable|in:uk,en,de,other',
            'contains_spoiler' => 'nullable|boolean',
        ]);

        $review = Review::create([
            'content' => $request->input('content'),
            'rating' => $request->input('rating'),
            'book_id' => $book->getKey(),
            'user_id' => null, // Гість
            'parent_id' => null,
            'review_type' => $request->input('review_type', 'review'),
            'opinion_type' => $request->input('opinion_type', 'positive'),
            'book_type' => $request->input('book_type', 'paper'),
            'language' => $request->input('language', 'uk'),
            'contains_spoiler' => $request->boolean('contains_spoiler', false),
        ]);

        // Оновлюємо рейтинг книги
        $book->updateRating();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Рецензію додано!',
                'review' => $review
            ]);
        }

        return redirect()->back()->with('success', 'Рецензію додано!');
    }

    /**
     * Get review data (including drafts) for editing
     */
    public function getReviewData(Book $book, Review $review)
    {
        // Проверяем права доступа - только владелец может редактировать черновик
        if ($review->user_id !== Auth::id()) {
            abort(403, 'У вас немає прав для редагування цієї рецензії');
        }

        // Проверяем, что рецензия принадлежит этой книге
        if ($review->book_id !== $book->id) {
            abort(404, 'Рецензію не знайдено');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'review' => [
                    'id' => $review->id,
                    'content' => $review->content,
                    'rating' => $review->rating,
                    'review_type' => $review->review_type,
                    'opinion_type' => $review->opinion_type,
                    'book_type' => $review->book_type,
                    'language' => $review->language,
                    'contains_spoiler' => $review->contains_spoiler,
                    'is_draft' => $review->is_draft,
                    'book_slug' => $book->slug, // Добавляем slug книги для удобства
                ]
            ]);
        }

        return redirect()->back();
    }

    /**
     * Show a single review with its discussion thread
     */
    public function show(Book $book, $reviewIdentifier)
    {
        // Находим рецензию по ID
        $review = Review::where('id', $reviewIdentifier)
            ->where('book_id', $book->id)
            ->firstOrFail();

        // Загружаем рецензию с автором и первыми ответами (только 2 уровня, исключая черновики)
        $review->load([
            'user',
            'book.author',
            'replies' => function ($query) {
                $query->where('is_draft', false)
                    ->with([
                        'user',
                        'replies' => function ($query) {
                            $query->where('is_draft', false)
                                ->with('user')
                                ->orderBy('created_at', 'desc');
                        }
                    ])->orderBy('created_at', 'desc');
            }
        ]);

        // Получаем связанные книги (based on shared categories)
        $categoryIds = $book->categories->pluck('id')->toArray();
        $relatedBooks = Book::whereHas('categories', function($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->where('id', '!=', $book->id)
            ->orderBy('rating', 'desc')
            ->limit(4)
            ->get();

        return view('reviews.show', compact('review', 'book', 'relatedBooks'));
    }

    /**
     * Store a reply to a review (для всіх користувачів)
     */
    public function storeReply(Request $request, Book $book, Review $review)
    {
        // Логируем для отладки
        Log::info('storeReply called', [
            'book_id' => $book->id ?? 'null',
            'review_id' => $review->id ?? 'null',
            'request_data' => $request->all()
        ]);

        // Проверяем существование книги
        if (!$book) {
            Log::error('Book not found', ['book_id' => $book->id ?? 'null']);
            return response()->json([
                'success' => false,
                'message' => 'Книга не найдена'
            ], 404);
        }

        $request->validate([
            'content' => 'required|string|min:1|max:400',
            'parent_id' => 'nullable|exists:reviews,id'
        ], [
            'content.min' => 'Коментар повинен містити мінімум 1 символ.',
            'content.max' => 'Коментар повинен містити максимум 400 символів.',
        ]);

        // Визначаємо, чи авторизований користувач
        $userId = Auth::check() ? Auth::id() : null;
        
        // Определяем родительский отзыв (для вложенных ответов)
        // Если parent_id передан, используем его, иначе это ответ на главную рецензию
        $parentId = $request->input('parent_id');
        if (!$parentId) {
            $parentId = $review->getKey();
        }

        $reply = Review::create([
            'content' => $request->input('content'),
            'rating' => null, // Відповіді не мають рейтингу
            'book_id' => $book->getKey(),
            'user_id' => $userId, // null для гостей, ID користувача для авторизованих
            'parent_id' => $parentId,
        ]);

        // Оновлюємо лічильник відповідей для основного отзыва
        $review->updateRepliesCount();

        // Уведомление создается автоматически в событии created модели Review

        if ($request->expectsJson()) {
            // Загружаем связанные данные для фронтенда
            $reply->load('user');
            
            // Подготавливаем данные для фронтенда
            $replyData = [
                'id' => $reply->id,
                'content' => $reply->content,
                'created_at' => $reply->created_at->toISOString(),
                'user_id' => $reply->user_id,
                'parent_id' => $reply->parent_id,
                'is_guest' => $reply->isGuest(),
                'user' => $reply->user ? [
                    'id' => $reply->user->id,
                    'name' => $reply->user->name,
                    'username' => $reply->user->username,
                    'avatar_display' => $reply->user->avatar_display ?? null,
                ] : null,
                'is_liked_by_current_user' => false,
                'likes_count' => 0,
                'replies_count' => 0,
                'replies' => []
            ];

            return response()->json([
                'success' => true,
                'message' => 'Відповідь додано!',
                'reply' => $replyData
            ]);
        }

        return redirect()->back()->with('success', 'Відповідь додано!');
    }

    /**
     * Toggle like for a review
     */
    public function toggleLike(Book $book, Review $review)
    {
        $user = auth()->user();
        
        // Check if user already liked this review
        $existingLike = $review->likes()->where('user_id', $user->id)->first();
        
        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $isLiked = false;
        } else {
            // Like
            $review->likes()->create([
                'user_id' => $user->id,
                'vote' => 1
            ]);
            $isLiked = true;
        }
        
        // Update likes count
        $likesCount = $review->likes()->where('vote', 1)->count();
        
        return response()->json([
            'success' => true,
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }

    /**
     * Toggle favorite for a review
     */
    public function toggleFavorite(Book $book, Review $review)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Необхідно авторизуватись'
            ], 401);
        }
        
        $isFavorited = $review->isFavoritedBy($user->id);
        
        if ($isFavorited) {
            // Remove from favorites
            $review->favoritedByUsers()->detach($user->id);
            $isFavorited = false;
        } else {
            // Add to favorites
            $review->favoritedByUsers()->attach($user->id);
            $isFavorited = true;
        }
        
        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited,
            'message' => $isFavorited ? 'Рецензію додано до избранного!' : 'Рецензію видалено з избранного!'
        ]);
    }

    /**
     * Show the form for editing a review
     */
    public function edit(Book $book, Review $review)
    {
        $this->authorize('update', $review);
        
        // Проверяем, что рецензия принадлежит этой книге
        if ($review->book_id !== $book->id) {
            abort(404, 'Рецензію не знайдено');
        }
        
        // Получаем статистику книги
        $ratingDistribution = $book->getRatingDistribution();
        $readingStats = $book->getReadingStats();
        $userRating = $book->getUserRating(Auth::id());
        
        // Загружаем связь с автором
        $book->load('author');
        $authorModel = $book->getRelation('author');
        
        return view('reviews.create', compact('book', 'review', 'ratingDistribution', 'readingStats', 'userRating', 'authorModel'));
    }

    /**
     * Show the form for editing a draft review (alternative page without modal)
     */
    public function editDraft(Book $book, Review $review)
    {
        try {
            // Проверяем права доступа - только владелец может редактировать черновик
            if ($review->user_id !== Auth::id()) {
                abort(403, 'У вас немає прав для редагування цієї рецензії');
            }

            // Проверяем, что это черновик
            if (!$review->is_draft) {
                return redirect()->route('books.reviews.edit', [$book, $review]);
            }

            // Проверяем, что рецензия принадлежит этой книге
            if ($review->book_id !== $book->id) {
                abort(404, 'Рецензію не знайдено');
            }

            // Загружаем связь с книгой, если она не загружена
            if (!$review->relationLoaded('book')) {
                $review->load('book');
            }

            // Убеждаемся, что книга существует
            if (!$book || !$book->exists) {
                abort(404, 'Книгу не знайдено');
            }

            return view('profile.private.edit-draft-review', compact('book', 'review'));
        } catch (\Exception $e) {
            \Log::error('Error in editDraft: ' . $e->getMessage(), [
                'book_id' => $book->id ?? null,
                'review_id' => $review->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, Book $book, Review $review)
    {
        $this->authorize('update', $review);
        
        // Определяем действие: publish или save
        $action = $request->input('action');
        $isDraft = ($action === 'save' || ($request->boolean('is_draft', false) && $action !== 'publish')) ? true : false;
        
        // Определяем минимальное и максимальное количество символов в зависимости от типа
        $reviewType = $request->input('review_type', $review->review_type ?? 'review');
        $minChars = ($reviewType === 'opinion') ? 25 : 800;
        $maxChars = ($reviewType === 'opinion') ? 1000 : 15000;
        
        // Validate content length (text only, not HTML)
        $content = $request->input('content');
        
        // Логируем для отладки
        \Log::info('Review update - content received', [
            'content_length' => strlen($content ?? ''),
            'content_preview' => substr($content ?? '', 0, 100),
            'is_empty' => empty($content),
            'is_null' => is_null($content),
            'all_inputs' => array_keys($request->all()),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type')
        ]);
        
        if (empty($content) || is_null($content)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Контент рецензії не може бути порожнім.',
                    'debug' => [
                        'content_received' => !empty($content),
                        'content_length' => strlen($content ?? ''),
                        'request_keys' => array_keys($request->all()),
                        'form_data_keys' => array_keys($request->all())
                    ]
                ], 422);
            }
            return redirect()->back()->withErrors(['content' => 'Контент рецензії не може бути порожнім.'])->withInput();
        }
        
        // Удаляем HTML-теги, но сохраняем переносы строк как пробелы
        $contentText = strip_tags($content);
        
        // Удаляем HTML entities
        $contentText = html_entity_decode($contentText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Удаляем неразрывные пробелы и другие специальные символы
        $contentText = str_replace(["\xc2\xa0", "\u{00A0}", "\u{2009}", "\u{200A}", "\u{202F}", "\u{205F}"], ' ', $contentText);
        
        // Нормализуем пробелы: заменяем множественные пробелы, переносы строк, табы на один пробел
        // НО сохраняем пробелы между словами
        $contentText = preg_replace('/[\s\n\r\t]+/u', ' ', $contentText);
        
        // Убираем пробелы в начале и конце
        $contentText = trim($contentText);
        
        // Подсчитываем длину
        $contentLength = mb_strlen($contentText);
        
        if ($contentLength < $minChars || $contentLength > $maxChars) {
            $errorMessage = $reviewType === 'opinion' 
                ? ($contentLength < $minChars 
                    ? 'Відгук повинен містити мінімум 25 символів.' 
                    : 'Відгук повинен містити максимум 1000 символів.')
                : ($contentLength < $minChars 
                    ? 'Рецензія повинна містити мінімум 800 символів.' 
                    : 'Рецензія повинна містити максимум 15000 символів.');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'debug' => [
                        'content_length' => $contentLength,
                        'min_chars' => $minChars,
                        'max_chars' => $maxChars,
                        'review_type' => $reviewType
                    ]
                ], 422);
            }
            
            return redirect()->back()->withErrors(['content' => $errorMessage])->withInput();
        }
        
        $validationRules = [
            'content' => 'required|string',
            'review_type' => 'nullable|in:review,opinion',
            'opinion_type' => 'nullable|in:positive,neutral,negative',
            'book_type' => 'nullable|in:paper,electronic,audio',
            'language' => 'nullable|in:uk,en,de,other',
            'contains_spoiler' => 'nullable|boolean',
            'is_draft' => 'nullable|boolean',
        ];
        
        // Если публикуем (не черновик), rating обязателен
        if (!$isDraft) {
            $validationRules['rating'] = 'required|integer|min:1|max:10';
        } else {
            $validationRules['rating'] = 'nullable|integer|min:1|max:10';
        }
        
        $request->validate($validationRules);

        $wasDraft = $review->is_draft;
        
        // Sanitize HTML content
        $content = $this->sanitizeHTML($request->input('content'));
        
        // Подготавливаем данные для обновления
        $updateData = [
            'content' => $content,
            'review_type' => $request->input('review_type', $review->review_type),
            'opinion_type' => $request->input('opinion_type', $review->opinion_type),
            'book_type' => $request->input('book_type', $review->book_type),
            'language' => $request->input('language', $review->language),
            'contains_spoiler' => $request->boolean('contains_spoiler', $review->contains_spoiler),
            'is_draft' => $isDraft,
        ];
        
        // Обрабатываем rating в зависимости от того, публикуем ли мы
        if (!$isDraft) {
            // При публикации rating обязателен (валидация уже проверила)
            $updateData['rating'] = $request->input('rating');
        } else {
            // Для черновика rating опционален - используем переданное значение или оставляем старое
            if ($request->has('rating') && $request->input('rating') !== null) {
                $updateData['rating'] = $request->input('rating');
            } else {
                // Если rating не передан, оставляем старое значение
                $updateData['rating'] = $review->rating;
            }
        }
        
        $review->update($updateData);

        // Если черновик был опубликован, обновляем рейтинги
        if ($wasDraft && !$isDraft) {
            // Синхронизируем рейтинг с BookReadingStatus
            $readingStatus = \App\Models\BookReadingStatus::firstOrCreate(
                [
                    'book_id' => $book->getKey(),
                    'user_id' => Auth::id(),
                ],
                [
                    'status' => 'read',
                    'finished_at' => now(),
                ]
            );

            // Обновляем рейтинг в BookReadingStatus
            if ($review->rating) {
                $readingStatus->update(['rating' => $review->rating]);
            }

            // Обновляем средний рейтинг книги
            $book->updateRating();
        } elseif (!$isDraft) {
            // Оновлюємо рейтинг книги если рейтинг изменился
            if ($request->has('rating') && $request->input('rating') != $review->rating) {
                $book->updateRating();
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $isDraft ? 'Чернетку оновлено!' : 'Рецензію оновлено!',
                'review' => $review->fresh()->load('user')
            ]);
        }

        // Редирект в зависимости от действия
        if ($isDraft) {
            return redirect()->route('profile.show', ['tab' => 'drafts'])->with('success', 'Чернетку оновлено!');
        } else {
            return redirect()->route('books.reviews.show', [$book, $review])->with('success', 'Рецензію оновлено!');
        }
    }

    /**
     * Remove the specified review
     */
    public function destroy(Book $book, Review $review)
    {
        $this->authorize('delete', $review);
        
        $review->delete();

        // Оновлюємо рейтинг книги
        $book->updateRating();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Рецензію видалено!'
            ]);
        }

        return redirect()->back()->with('success', 'Рецензію видалено!');
    }

    /**
     * Update a reply/comment
     */
    public function updateReply(Request $request, Book $book, Review $review)
    {
        // Проверяем, что это ответ (не основная рецензия)
        if (!$review->parent_id) {
            return response()->json([
                'success' => false,
                'message' => 'Це не відповідь, а основна рецензія'
            ], 400);
        }

        // Проверяем права доступа
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Потрібна авторизація'
            ], 401);
        }

        // Проверяем, что пользователь может редактировать этот ответ
        if ($review->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Немає прав для редагування цього коментаря'
            ], 403);
        }

        $request->validate([
            'content' => 'required|string|max:5000'
        ]);

        $review->update([
            'content' => $request->input('content')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Коментар оновлено!',
            'content' => $review->content
        ]);
    }

    /**
     * Delete a reply/comment
     */
    public function deleteReply(Request $request, Book $book, Review $review)
    {
        // Проверяем, что это ответ (не основная рецензия)
        if (!$review->parent_id) {
            return response()->json([
                'success' => false,
                'message' => 'Це не відповідь, а основна рецензія'
            ], 400);
        }

        // Проверяем права доступа
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Потрібна авторизація'
            ], 401);
        }

        // Проверяем, что пользователь может удалить этот ответ
        if ($review->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Немає прав для видалення цього коментаря'
            ], 403);
        }

        // Получаем основную рецензию для обновления счетчика
        $parentReview = Review::find($review->parent_id);
        
        $review->delete();

        // Обновляем счетчик ответов
        if ($parentReview) {
            $parentReview->updateRepliesCount();
        }

        return response()->json([
            'success' => true,
            'message' => 'Коментар видалено!'
        ]);
    }

    /**
     * Sanitize HTML content - remove dangerous tags and attributes
     */
    private function sanitizeHTML($html)
    {
        if (empty($html)) {
            return $html;
        }
        
        // Allowed tags
        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><a><img>';
        
        // Strip all tags except allowed ones
        $html = strip_tags($html, $allowedTags);
        
        // Remove all style attributes and event handlers using regex
        $html = preg_replace('/\s*style\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        
        // Validate and clean links
        $html = preg_replace_callback('/<a\s+([^>]*)>(.*?)<\/a>/is', function($matches) {
            $attrs = $matches[1];
            $text = $matches[2];
            
            // Extract href
            if (preg_match('/href\s*=\s*["\']([^"\']*)["\']/i', $attrs, $hrefMatch)) {
                $href = $hrefMatch[1];
                // Only allow http, https, or relative URLs
                if (preg_match('/^(https?:\/\/|\/)/i', $href)) {
                    return '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">' . $text . '</a>';
                }
            }
            // If href is invalid, return just the text
            return $text;
        }, $html);
        
        // Validate and clean images
        $html = preg_replace_callback('/<img\s+([^>]*)>/is', function($matches) {
            $attrs = $matches[1];
            
            // Extract src
            if (preg_match('/src\s*=\s*["\']([^"\']*)["\']/i', $attrs, $srcMatch)) {
                $src = $srcMatch[1];
                // Only allow http, https, relative URLs, or data URIs for images
                if (preg_match('/^(https?:\/\/|\/|data:image)/i', $src)) {
                    $alt = '';
                    if (preg_match('/alt\s*=\s*["\']([^"\']*)["\']/i', $attrs, $altMatch)) {
                        $alt = htmlspecialchars($altMatch[1], ENT_QUOTES, 'UTF-8');
                    }
                    return '<img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" alt="' . $alt . '">';
                }
            }
            // If src is invalid, remove the image
            return '';
        }, $html);
        
        return $html;
    }
}
