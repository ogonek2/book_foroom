<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\DiscussionReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            ->whereNotNull('user_id'); // Исключаем обсуждения без пользователя

        // Загружаем рецензии
        $reviewsQuery = \App\Models\Review::with(['user', 'book'])
            ->withCount(['replies', 'likes'])
            ->where('status', 'active')
            ->whereNull('parent_id') // Только основные рецензии, не ответы
            ->whereNotNull('user_id'); // Исключаем рецензии без пользователя

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
            // Получаем все данные
            $discussions = $discussionsQuery->get();
            $reviews = $reviewsQuery->get();
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

        return view('discussions.index', compact('discussions', 'reviews', 'filter', 'sortBy', 'pagination'));
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
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $discussion = Discussion::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
            'status' => 'active',
        ]);

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Обсуждение успешно создано!');
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

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $discussion->update($request->only(['title', 'content']));

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Обсуждение успешно обновлено!');
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
            'content' => 'required|string|min:1',
            'parent_id' => 'nullable|exists:discussion_replies,id',
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
