<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\DiscussionReply;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DiscussionController extends Controller
{
    /**
     * Display a listing of discussions
     */
    public function index(Request $request)
    {
        // Получаем параметры фильтрации
        $filter = $request->get('filter', 'all');
        $sortBy = $request->get('sort', 'newest');
        $perPage = $request->get('per_page', 15);

        // Загружаем обсуждения
        $discussionsQuery = Discussion::with(['user'])
            ->withCount(['replies', 'likes'])
            ->where('status', 'active')
            ->whereNotNull('user_id') // Исключаем обсуждения без пользователя
            ->where('is_draft', false); // Исключаем черновики

        // Загружаем рецензии
        $reviewsQuery = \App\Models\Review::with(['user', 'book'])
            ->withCount(['replies', 'likes'])
            ->where('status', 'active')
            ->whereNull('parent_id') // Только основные рецензии, не ответы
            ->whereNotNull('user_id') // Исключаем рецензии без пользователя
            ->where('is_draft', false); // Исключаем черновики

        // Поиск
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            
            $discussionsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });

            $reviewsQuery->where(function ($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhereHas('book', function ($bookQuery) use ($search) {
                      $bookQuery->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Сортировка
        switch ($sortBy) {
            case 'newest':
                $discussionsQuery->orderBy('created_at', 'desc');
                $reviewsQuery->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $discussionsQuery->orderBy('created_at', 'asc');
                $reviewsQuery->orderBy('created_at', 'asc');
                break;
            case 'popular':
                $discussionsQuery->orderBy('likes_count', 'desc')->orderBy('replies_count', 'desc');
                $reviewsQuery->orderBy('likes_count', 'desc')->orderBy('replies_count', 'desc');
                break;
            case 'trending':
                // Сортировка по активности за последние 7 дней
                $weekAgo = now()->subDays(7);
                $discussionsQuery->where('updated_at', '>=', $weekAgo)
                    ->orderBy('likes_count', 'desc')->orderBy('replies_count', 'desc');
                $reviewsQuery->where('updated_at', '>=', $weekAgo)
                    ->orderBy('likes_count', 'desc')->orderBy('replies_count', 'desc');
                break;
        }

        // Получаем данные в зависимости от фильтра
        if ($filter === 'discussions') {
            $discussions = $discussionsQuery->paginate($perPage);
            $reviews = collect();
        } elseif ($filter === 'reviews') {
            $reviews = $reviewsQuery->paginate($perPage);
            $discussions = collect();
        } else {
            // Для обычного запроса (не API) загружаем только первую страницу для быстрой загрузки
            // Остальные данные будут загружаться через API при скролле
            // Загружаем немного больше для правильной сортировки объединенного списка
            $limit = max($perPage, 20); // Минимум 20 элементов для сортировки
            $discussions = $discussionsQuery->limit($limit)->get();
            $reviews = $reviewsQuery->limit($limit)->get();
        }

        // Если нужна пагинация для объединенного списка
        $pagination = null;
        if ($filter === 'all') {
            // Объединяем и сортируем все данные
            $allContent = collect();
            
            // Добавляем обсуждения
            foreach ($discussions as $discussion) {
                $allContent->push([
                    'type' => 'discussion',
                    'id' => $discussion->id,
                    'created_at' => $discussion->created_at,
                    'updated_at' => $discussion->updated_at,
                    'likes_count' => $discussion->likes_count,
                    'replies_count' => $discussion->replies_count,
                ]);
            }
            
            // Добавляем рецензии
            foreach ($reviews as $review) {
                $allContent->push([
                    'type' => 'review',
                    'id' => $review->id,
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at,
                    'likes_count' => $review->likes_count,
                    'replies_count' => $review->replies_count,
                ]);
            }

            // Сортируем объединенный список
            switch ($sortBy) {
                case 'newest':
                    $allContent = $allContent->sortByDesc('created_at');
                    break;
                case 'oldest':
                    $allContent = $allContent->sortBy('created_at');
                    break;
                case 'popular':
                case 'trending':
                    $allContent = $allContent->sortByDesc(function ($item) {
                        return $item['likes_count'] + $item['replies_count'];
                    });
                    break;
            }

            // Пагинация для объединенного списка
            $currentPage = $request->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $paginatedContent = $allContent->slice($offset, $perPage)->values();
            
            // Создаем пагинацию
            $totalItems = $allContent->count();
            $totalPages = ceil($totalItems / $perPage);
            
            $pagination = new \Illuminate\Pagination\LengthAwarePaginator(
                $paginatedContent,
                $totalItems,
                $perPage,
                $currentPage,
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
            
            // Загружаем только нужные данные для текущей страницы
            $discussionIds = $paginatedContent->where('type', 'discussion')->pluck('id');
            $reviewIds = $paginatedContent->where('type', 'review')->pluck('id');
            
            $discussions = $discussionIds->isNotEmpty() 
                ? $discussionsQuery->whereIn('id', $discussionIds)->get()->keyBy('id')
                : collect();
            $reviews = $reviewIds->isNotEmpty() 
                ? $reviewsQuery->whereIn('id', $reviewIds)->get()->keyBy('id')
                : collect();
        }

        $discussionsData = $this->prepareDiscussionPayload($discussions);
        $reviewsData = $this->prepareReviewPayload($reviews);

        // Если запрос ожидает JSON (API запрос)
        if ($request->expectsJson() || $request->wantsJson()) {
            $currentPage = (int) $request->get('page', 1);
            $perPage = (int) $request->input('per_page', 15);
            $perPage = max(1, min(50, $perPage));

            // Для API возвращаем пагинированные данные
            if ($filter === 'all') {
                // Пересоздаем queries для получения всех данных
                $allDiscussionsQuery = Discussion::with(['user'])
                    ->withCount(['replies', 'likes'])
                    ->where('status', 'active')
                    ->whereNotNull('user_id')
                    ->where('is_draft', false);
                
                $allReviewsQuery = \App\Models\Review::with(['user', 'book'])
                    ->withCount(['replies', 'likes'])
                    ->where('status', 'active')
                    ->whereNull('parent_id')
                    ->whereNotNull('user_id')
                    ->where('is_draft', false);
                
                // Применяем поиск
                if ($request->has('search') && $request->search) {
                    $search = $request->search;
                    $allDiscussionsQuery->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhere('content', 'like', "%{$search}%");
                    });
                    $allReviewsQuery->where(function ($q) use ($search) {
                        $q->where('content', 'like', "%{$search}%")
                          ->orWhereHas('book', function ($bookQuery) use ($search) {
                              $bookQuery->where('title', 'like', "%{$search}%");
                          });
                    });
                }
                
                // Применяем сортировку
                switch ($sortBy) {
                    case 'newest':
                        $allDiscussionsQuery->orderBy('created_at', 'desc');
                        $allReviewsQuery->orderBy('created_at', 'desc');
                        break;
                    case 'oldest':
                        $allDiscussionsQuery->orderBy('created_at', 'asc');
                        $allReviewsQuery->orderBy('created_at', 'asc');
                        break;
                    case 'popular':
                        $allDiscussionsQuery->orderBy('likes_count', 'desc')->orderBy('replies_count', 'desc');
                        $allReviewsQuery->orderBy('likes_count', 'desc')->orderBy('replies_count', 'desc');
                        break;
                    case 'trending':
                        $weekAgo = now()->subDays(7);
                        $allDiscussionsQuery->where('updated_at', '>=', $weekAgo)
                            ->orderBy('likes_count', 'desc')->orderBy('replies_count', 'desc');
                        $allReviewsQuery->where('updated_at', '>=', $weekAgo)
                            ->orderBy('likes_count', 'desc')->orderBy('replies_count', 'desc');
                        break;
                }
                
                // Получаем все данные
                $allDiscussions = $allDiscussionsQuery->get();
                $allReviews = $allReviewsQuery->get();
                
                $allContent = collect();
                
                foreach ($allDiscussions as $discussion) {
                    $allContent->push([
                        'type' => 'discussion',
                        'id' => $discussion->id,
                        'created_at' => $discussion->created_at,
                        'updated_at' => $discussion->updated_at,
                        'likes_count' => $discussion->likes_count ?? 0,
                        'replies_count' => $discussion->replies_count ?? 0,
                    ]);
                }
                
                foreach ($allReviews as $review) {
                    $allContent->push([
                        'type' => 'review',
                        'id' => $review->id,
                        'created_at' => $review->created_at,
                        'updated_at' => $review->updated_at,
                        'likes_count' => $review->likes_count ?? 0,
                        'replies_count' => $review->replies_count ?? 0,
                    ]);
                }

                // Сортируем
                switch ($sortBy) {
                    case 'newest':
                        $allContent = $allContent->sortByDesc('created_at');
                        break;
                    case 'oldest':
                        $allContent = $allContent->sortBy('created_at');
                        break;
                    case 'popular':
                    case 'trending':
                        $allContent = $allContent->sortByDesc(function ($item) {
                            return ($item['likes_count'] ?? 0) + ($item['replies_count'] ?? 0);
                        });
                        break;
                }

                $offset = ($currentPage - 1) * $perPage;
                $paginatedContent = $allContent->slice($offset, $perPage)->values();
                
                $discussionIds = $paginatedContent->where('type', 'discussion')->pluck('id');
                $reviewIds = $paginatedContent->where('type', 'review')->pluck('id');
                
                // Загружаем только нужные элементы из уже полученных коллекций
                $discussions = $discussionIds->isNotEmpty() 
                    ? $allDiscussions->whereIn('id', $discussionIds)->keyBy('id')
                    : collect();
                $reviews = $reviewIds->isNotEmpty() 
                    ? $allReviews->whereIn('id', $reviewIds)->keyBy('id')
                    : collect();
                
                $discussionsData = $this->prepareDiscussionPayload($discussions);
                $reviewsData = $this->prepareReviewPayload($reviews);
                
                $totalItems = $allContent->count();
                $hasMore = $totalItems > ($currentPage * $perPage);
            } else {
                // Для фильтрованных запросов используем пагинацию
                if ($filter === 'discussions') {
                    $paginated = $discussionsQuery->paginate($perPage, ['*'], 'page', $currentPage);
                    $discussions = $paginated->items();
                    $reviews = collect();
                    $hasMore = $paginated->hasMorePages();
                } else {
                    $paginated = $reviewsQuery->paginate($perPage, ['*'], 'page', $currentPage);
                    $reviews = $paginated->items();
                    $discussions = collect();
                    $hasMore = $paginated->hasMorePages();
                }
                
                $discussionsData = $this->prepareDiscussionPayload($discussions);
                $reviewsData = $this->prepareReviewPayload($reviews);
            }

            return response()->json([
                'discussions' => $discussionsData,
                'reviews' => $reviewsData,
                'current_page' => $currentPage,
                'per_page' => $perPage,
                'has_more' => $hasMore ?? false,
            ]);
        }

        return view('discussions.index', compact(
            'discussions',
            'reviews',
            'filter',
            'sortBy',
            'pagination',
            'discussionsData',
            'reviewsData'
        ));
    }

    /**
     * Show the form for creating a new discussion
     */
    public function create()
    {
        return view('discussions.create');
    }

    /**
     * Store a newly created discussion
     */
    public function store(Request $request)
    {
        $isDraft = $request->boolean('is_draft', false);
        
        $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'is_draft' => 'nullable|boolean',
        ], [
            'title.max' => 'Назва обговорення повинна містити максимум 200 символів.',
        ]);

        // Validate content length (text only, not HTML) - same as ReviewController
        $content = $request->input('content');
        
        if (empty($content)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Контент обговорення не може бути порожнім.'
                ], 422);
            }
            return redirect()->back()->withErrors(['content' => 'Контент обговорення не може бути порожнім.'])->withInput();
        }
        
        // Удаляем HTML-теги, но сохраняем переносы строк как пробелы
        $contentText = strip_tags($content);
        
        // Удаляем HTML entities
        $contentText = html_entity_decode($contentText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Удаляем неразрывные пробелы и другие специальные символы
        $contentText = str_replace(["\xc2\xa0", "\u{00A0}", "\u{2009}", "\u{200A}", "\u{202F}", "\u{205F}"], ' ', $contentText);
        
        // Нормализуем пробелы: заменяем множественные пробелы, переносы строк, табы на один пробел
        $contentText = preg_replace('/[\s\n\r\t]+/u', ' ', $contentText);
        
        // Убираем пробелы в начале и конце
        $contentText = trim($contentText);
        
        // Подсчитываем длину (используем mb_strlen для правильной работы с UTF-8)
        $contentLength = mb_strlen($contentText, 'UTF-8');
        
        $minChars = 300;
        $maxChars = 3500;
        
        if ($contentLength < $minChars || $contentLength > $maxChars) {
            $errorMessage = $contentLength < $minChars 
                ? 'Текст обговорення повинен містити мінімум 300 символів.' 
                : 'Текст обговорення повинен містити максимум 3500 символів.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'debug' => [
                        'content_length' => $contentLength,
                        'min_chars' => $minChars,
                        'max_chars' => $maxChars,
                    ]
                ], 422);
            }
            
            return redirect()->back()->withErrors(['content' => $errorMessage])->withInput();
        }

        $discussion = Discussion::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
            'status' => 'active',
            'is_draft' => $isDraft,
        ]);

        // Process hashtags
        $this->processHashtags($discussion, $request->content);

        // Process mentions
        if (!$isDraft) {
            $this->processMentions($discussion, $request->content);
        }

        if ($isDraft) {
            return redirect()->route('profile.show', ['tab' => 'drafts'])
                ->with('success', 'Чернетку збережено!');
        }

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Обговорення успішно створено!');
    }

    /**
     * Process hashtags from content
     */
    private function processHashtags(Discussion $discussion, string $content)
    {
        // Extract hashtags from content using regex - supports Cyrillic
        // Pattern: # followed by letters (Latin/Cyrillic), numbers, and underscores
        preg_match_all('/#([a-zA-Zа-яА-ЯёЁіІїЇєЄ0-9_]+)/u', strip_tags($content), $matches);
        
        if (empty($matches[1])) {
            return;
        }

        $hashtags = array_unique($matches[1]);
        
        foreach ($hashtags as $hashtagName) {
            // Normalize hashtag (lowercase, remove special chars)
            $hashtagName = mb_strtolower(trim($hashtagName), 'UTF-8');
            if (empty($hashtagName)) {
                continue;
            }

            // Find or create tag
            $tag = \App\Models\Tag::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($hashtagName)],
                ['name' => $hashtagName]
            );

            // Attach tag to discussion
            if (!$discussion->tags()->where('tags.id', $tag->id)->exists()) {
                $discussion->tags()->attach($tag->id);
                $tag->incrementUsage();
            }
        }
    }

    /**
     * Process mentions from content
     */
    private function processMentions(Discussion $discussion, string $content)
    {
        // Extract mentions from content using regex - supports Cyrillic
        // Pattern: @ followed by letters (Latin/Cyrillic), numbers, and underscores
        preg_match_all('/@([a-zA-Zа-яА-ЯёЁіІїЇєЄ0-9_]+)/u', strip_tags($content), $matches);
        
        if (empty($matches[1])) {
            return;
        }

        $usernames = array_unique($matches[1]);
        
        foreach ($usernames as $username) {
            $user = \App\Models\User::where('username', $username)->first();
            
            if ($user && $user->id !== $discussion->user_id) {
                // Create mention record
                \App\Models\DiscussionMention::firstOrCreate(
                    [
                        'discussion_id' => $discussion->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'notified' => false,
                    ]
                );

                // Create notification (if notification system exists)
                // You can add notification creation here
            }
        }
    }

    /**
     * Display the specified discussion
     */
    public function show(Discussion $discussion)
    {
        $discussion->incrementViews();

        // Load discussion with user and their stats
        $discussion->load(['user' => function($query) {
            $query->withCount([
                'discussions as active_discussions_count' => function($query) {
                    $query->where('status', 'active');
                },
                'discussionReplies as active_replies_count' => function($query) {
                    $query->where('status', 'active');
                },
                'reviews as active_reviews_count' => function($query) {
                    $query->where('status', 'active');
                }
            ]);
        }]);

        // Загружаем все ответы с глубокой вложенностью (до 5 уровней)
        $replies = $discussion->replies()
            ->with([
                'user',
                'replies' => function ($query) {
                    $query->with([
                        'user',
                        'replies' => function ($query) {
                            $query->with([
                                'user',
                                'replies' => function ($query) {
                                    $query->with([
                                        'user',
                                        'replies.user'
                                    ])->withCount('likes')->orderBy('created_at', 'desc');
                                }
                            ])->withCount('likes')->orderBy('created_at', 'desc');
                        }
                    ])->withCount('likes')->orderBy('created_at', 'desc');
                }
            ])
            ->withCount('likes')
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Ensure replies are sorted by newest first
        $replies = $replies->sortByDesc('created_at');
        
        return view('discussions.show', compact('discussion', 'replies'));
    }

    protected function prepareDiscussionPayload($items): array
    {
        $collection = $this->normalizeItems($items);

        if ($collection->isEmpty()) {
            return [];
        }

        $topComments = $this->getTopDiscussionComments($collection->pluck('id'));

        return $collection->map(function ($discussion) use ($topComments) {
            $discussionArray = $this->modelToArray($discussion);
            $topComment = $topComments->get($discussionArray['id'] ?? null);
            $discussionArray['top_comment'] = $this->formatTopComment($topComment);

            return $discussionArray;
        })->values()->toArray();
    }

    protected function prepareReviewPayload($items): array
    {
        $collection = $this->normalizeItems($items);

        if ($collection->isEmpty()) {
            return [];
        }

        $topComments = $this->getTopReviewComments($collection->pluck('id'));

        return $collection->map(function ($review) use ($topComments) {
            $reviewArray = $this->modelToArray($review);
            $topComment = $topComments->get($reviewArray['id'] ?? null);
            $reviewArray['top_comment'] = $this->formatTopComment($topComment);

            return $reviewArray;
        })->values()->toArray();
    }

    protected function getTopDiscussionComments(Collection $discussionIds): Collection
    {
        if ($discussionIds->isEmpty()) {
            return collect();
        }

        $replies = DiscussionReply::with(['user'])
            ->withCount(['likes as likes_total' => function ($query) {
                $query->where('vote', 1);
            }])
            ->whereIn('discussion_id', $discussionIds)
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->orderByDesc('likes_total')
            ->orderByDesc('created_at')
            ->get();

        return $replies->groupBy('discussion_id')->map->first();
    }

    protected function getTopReviewComments(Collection $reviewIds): Collection
    {
        if ($reviewIds->isEmpty()) {
            return collect();
        }

        $replies = \App\Models\Review::with(['user'])
            ->withCount(['likes as likes_total' => function ($query) {
                $query->where('vote', 1);
            }])
            ->whereIn('parent_id', $reviewIds)
            ->where('status', 'active')
            ->where('is_draft', false)
            ->orderByDesc('likes_total')
            ->orderByDesc('created_at')
            ->get();

        return $replies->groupBy('parent_id')->map->first();
    }

    protected function normalizeItems($items): Collection
    {
        if ($items instanceof LengthAwarePaginator || $items instanceof Paginator) {
            return collect($items->items());
        }

        if ($items instanceof Collection) {
            return $items;
        }

        return collect($items ?? []);
    }

    protected function modelToArray($item): array
    {
        if (is_array($item)) {
            return $item;
        }

        if (is_object($item) && method_exists($item, 'toArray')) {
            return $item->toArray();
        }

        return (array) $item;
    }

    protected function formatTopComment($comment): ?array
    {
        if (!$comment) {
            return null;
        }

        $user = $comment->user;

        return [
            'id' => $comment->id,
            'content' => Str::limit(strip_tags($comment->content), 160),
            'likes_count' => $comment->likes_total ?? $comment->likes_count ?? 0,
            'created_at' => optional($comment->created_at)->toIso8601String(),
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'avatar_display' => $user->avatar_display ?? null,
            ] : null,
        ];
    }

    /**
     * Display a specific reply in a discussion
     * Used for notifications to navigate directly to a reply
     */
    public function showReply(Discussion $discussion, DiscussionReply $reply)
    {
        // Проверяем, что ответ принадлежит этому обсуждению
        if ($reply->discussion_id !== $discussion->id) {
            abort(404, 'Відповідь не знайдено в цьому обговоренні');
        }

        // Увеличиваем счетчик просмотров
        $discussion->incrementViews();

        // Загружаем обсуждение с пользователем и статистикой
        $discussion->load(['user' => function($query) {
            $query->withCount([
                'discussions as active_discussions_count' => function($query) {
                    $query->where('status', 'active');
                },
                'discussionReplies as active_replies_count' => function($query) {
                    $query->where('status', 'active');
                },
                'reviews as active_reviews_count' => function($query) {
                    $query->where('status', 'active');
                }
            ]);
        }]);

        // Загружаем все ответы на обсуждение (как в обычном show)
        $replies = $discussion->replies()
            ->with([
                'user',
                'replies' => function ($query) {
                    $query->with([
                        'user',
                        'replies' => function ($query) {
                            $query->with([
                                'user',
                                'replies' => function ($query) {
                                    $query->with([
                                        'user',
                                        'replies.user'
                                    ])->withCount('likes')->orderBy('created_at', 'desc');
                                }
                            ])->withCount('likes')->orderBy('created_at', 'desc');
                        }
                    ])->withCount('likes')->orderBy('created_at', 'desc');
                }
            ])
            ->withCount('likes')
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Сортируем ответы по новизне
        $replies = $replies->sortByDesc('created_at');

        // Загружаем конкретный ответ с его родителем (если есть) и пользователем
        $reply->load([
            'user',
            'parent',
            'replies' => function ($query) {
                $query->with([
                    'user',
                    'replies' => function ($query) {
                        $query->with([
                            'user',
                            'replies.user'
                        ])->withCount('likes')->orderBy('created_at', 'desc');
                    }
                ])->withCount('likes')->orderBy('created_at', 'desc');
            }
        ]);

        // Отмечаем этот ответ как целевой для подсветки на странице
        $highlightedReplyId = $reply->id;

        return view('discussions.reply', compact('discussion', 'replies', 'reply', 'highlightedReplyId'));
    }

    /**
     * Show the form for editing the discussion
     */
    public function edit(Discussion $discussion)
    {
        if (!$discussion->canBeEditedBy(Auth::user())) {
            abort(403, 'У вас нет прав для редактирования этого обсуждения');
        }

        return view('discussions.edit', compact('discussion'));
    }

    /**
     * Update the specified discussion
     */
    public function update(Request $request, Discussion $discussion)
    {
        if (!$discussion->canBeEditedBy(Auth::user())) {
            abort(403, 'У вас нет прав для редактирования этого обсуждения');
        }

        $isDraft = $request->boolean('is_draft', false);
        
        $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'is_draft' => 'nullable|boolean',
        ], [
            'title.max' => 'Назва обговорення повинна містити максимум 200 символів.',
        ]);

        // Validate content length (text only, not HTML) - same as store method
        $content = $request->input('content');
        
        if (empty($content)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Контент обговорення не може бути порожнім.'
                ], 422);
            }
            return redirect()->back()->withErrors(['content' => 'Контент обговорення не може бути порожнім.'])->withInput();
        }
        
        // Удаляем HTML-теги, но сохраняем переносы строк как пробелы
        $contentText = strip_tags($content);
        
        // Удаляем HTML entities
        $contentText = html_entity_decode($contentText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Удаляем неразрывные пробелы и другие специальные символы
        $contentText = str_replace(["\xc2\xa0", "\u{00A0}", "\u{2009}", "\u{200A}", "\u{202F}", "\u{205F}"], ' ', $contentText);
        
        // Нормализуем пробелы: заменяем множественные пробелы, переносы строк, табы на один пробел
        $contentText = preg_replace('/[\s\n\r\t]+/u', ' ', $contentText);
        
        // Убираем пробелы в начале и конце
        $contentText = trim($contentText);
        
        // Подсчитываем длину (используем mb_strlen для правильной работы с UTF-8)
        $contentLength = mb_strlen($contentText, 'UTF-8');
        
        $minChars = 300;
        $maxChars = 3500;
        
        if ($contentLength < $minChars || $contentLength > $maxChars) {
            $errorMessage = $contentLength < $minChars 
                ? 'Текст обговорення повинен містити мінімум 300 символів.' 
                : 'Текст обговорення повинен містити максимум 3500 символів.';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'debug' => [
                        'content_length' => $contentLength,
                        'min_chars' => $minChars,
                        'max_chars' => $maxChars,
                    ]
                ], 422);
            }
            
            return redirect()->back()->withErrors(['content' => $errorMessage])->withInput();
        }

        $wasDraft = $discussion->is_draft;
        $discussion->update([
            'title' => $request->title,
            'content' => $request->content,
            'is_draft' => $isDraft,
        ]);

        if ($isDraft) {
            return redirect()->route('profile.show', ['tab' => 'drafts'])
                ->with('success', 'Чернетку оновлено!');
        }

        if ($wasDraft && !$isDraft) {
            return redirect()->route('discussions.show', $discussion)
                ->with('success', 'Обговорення опубліковано!');
        }

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Обговорення успішно оновлено!');
    }

    /**
     * Close the discussion
     */
    public function close(Discussion $discussion)
    {
        if (!$discussion->canBeClosedBy(Auth::user())) {
            abort(403, 'У вас нет прав для закрытия этого обсуждения');
        }

        $discussion->update(['is_closed' => true]);

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Обсуждение закрыто!');
    }

    /**
     * Reopen the discussion
     */
    public function reopen(Discussion $discussion)
    {
        if (!$discussion->canBeClosedBy(Auth::user())) {
            abort(403, 'У вас нет прав для открытия этого обсуждения');
        }

        $discussion->update(['is_closed' => false]);

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Обсуждение открыто!');
    }

    /**
     * Remove the specified discussion
     */
    public function destroy(Discussion $discussion)
    {
        if (!$discussion->canBeEditedBy(Auth::user())) {
            abort(403, 'У вас нет прав для удаления этого обсуждения');
        }

        $discussion->delete();

        return redirect()->route('discussions.index')
            ->with('success', 'Обсуждение удалено!');
    }

    /**
     * Store a new reply to discussion
     */
    public function storeReply(Request $request, Discussion $discussion)
    {
        if (!$discussion->canBeRepliedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Обсуждение закрыто для новых ответов'
            ], 403);
        }

        $request->validate([
            'content' => 'required|string|min:1|max:300',
            'parent_id' => 'nullable|exists:discussion_replies,id',
        ], [
            'content.min' => 'Коментар повинен містити мінімум 1 символ.',
            'content.max' => 'Коментар повинен містити максимум 300 символів.',
        ]);

        $reply = DiscussionReply::create([
            'content' => $request->content,
            'discussion_id' => $discussion->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'status' => 'active',
        ]);

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ответ добавлен!',
                'reply' => $reply->load('user')
            ]);
        }

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Ответ добавлен!');
    }

    /**
     * Update a reply
     */
    public function updateReply(Request $request, Discussion $discussion, DiscussionReply $reply)
    {
        if ($reply->user_id !== Auth::id() && !Auth::user()->isModerator()) {
            abort(403, 'У вас нет прав для редактирования этого ответа');
        }

        $request->validate([
            'content' => 'required|string|min:1',
        ]);

        $reply->update($request->only(['content']));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ответ обновлен!',
                'reply' => $reply->load('user')
            ]);
        }

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Ответ обновлен!');
    }

    /**
     * Delete a reply
     */
    public function destroyReply(Discussion $discussion, DiscussionReply $reply)
    {
        if ($reply->user_id !== Auth::id() && !Auth::user()->isModerator()) {
            abort(403, 'У вас нет прав для удаления этого ответа');
        }

        $reply->delete();

        if (request()->expectsJson() || request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ответ удален!'
            ]);
        }

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Ответ удален!');
    }
}
