<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Показать профиль пользователя
     */
    public function show(User $user)
    {
        $user->load([
            'quotes' => function($query) {
                $query->where('is_public', true)->with('book')->latest();
            },
            'publications' => function($query) {
                $query->where('status', 'published')->latest('published_at');
            },
            'reviews' => function($query) {
                $query->whereNull('parent_id')->with('book')->latest();
            },
            'savedBooks' => function($query) {
                $query->with('author')->latest('user_libraries.added_at');
            }
        ]);

        // Статистика пользователя
        $stats = [
            'quotes_count' => $user->quotes()->where('is_public', true)->count(),
            'publications_count' => $user->publications()->where('status', 'published')->count(),
            'reviews_count' => $user->reviews()->whereNull('parent_id')->where('is_draft', false)->count(),
            'library_count' => $user->savedBooks()->count(),
        ];

        return view('users.show', compact('user', 'stats'));
    }

    /**
     * Показать список пользователей
     */
    public function index(Request $request)
    {
        $query = User::withCount([
            'quotes as public_quotes_count' => function($query) {
                $query->where('is_public', true);
            },
            'publications as published_publications_count' => function($query) {
                $query->where('status', 'published');
            },
            'reviews as main_reviews_count' => function($query) {
                $query->whereNull('parent_id');
            },
            'discussions as discussions_count' => function($query) {
                $query->where('status', 'active');
            },
            'discussionReplies as replies_count',
            'reviews as review_replies_count' => function($query) {
                $query->whereNotNull('parent_id')->where('is_draft', false);
            },
            'readingStatuses as ratings_count' => function($query) {
                $query->whereNotNull('rating');
            },
            'readingStatuses as read_books_count' => function($query) {
                $query->where('status', 'read');
            }
        ]);

        $ratingRawExpression = "LEAST(("
            . "(SELECT COUNT(*) FROM reviews r1 WHERE r1.user_id = users.id AND r1.parent_id IS NULL AND r1.is_draft = 0) * 10 + "
            . "(SELECT COUNT(*) FROM quotes q1 WHERE q1.user_id = users.id AND q1.is_public = 1 AND q1.is_draft = 0) * 5 + "
            . "(SELECT COUNT(*) FROM publications p1 WHERE p1.user_id = users.id AND p1.status = 'published') * 15 + "
            . "(SELECT COUNT(*) FROM book_reading_statuses b1 WHERE b1.user_id = users.id AND b1.rating IS NOT NULL) * 2 + "
            . "(SELECT COUNT(*) FROM book_reading_statuses b2 WHERE b2.user_id = users.id AND b2.status = 'read') * 3 + "
            . "(SELECT COUNT(*) FROM discussions d1 WHERE d1.user_id = users.id AND d1.status = 'active' AND d1.is_draft = 0) * 8 + "
            . "(SELECT COUNT(*) FROM discussion_replies dr1 WHERE dr1.user_id = users.id) * 3 + "
            . "(SELECT COUNT(*) FROM reviews r2 WHERE r2.user_id = users.id AND r2.parent_id IS NOT NULL AND r2.is_draft = 0) * 2"
            . "), 100)";

        // Поиск по юзернейму или имени
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->has('rating_filter') && $request->rating_filter) {
            $ratingFilter = $request->rating_filter;
            switch ($ratingFilter) {
                case '5_stars':
                    $query->whereRaw("CEIL(($ratingRawExpression) / 10) >= 5");
                    break;
                case '7_stars':
                    $query->whereRaw("CEIL(($ratingRawExpression) / 10) >= 7");
                    break;
                case '9_stars':
                    $query->whereRaw("CEIL(($ratingRawExpression) / 10) >= 9");
                    break;
            }
        }

        // Фильтр по активности
        if ($request->has('activity_filter') && $request->activity_filter) {
            $activityFilter = $request->activity_filter;
            switch ($activityFilter) {
                case 'most_reviews':
                    $query->orderBy('main_reviews_count', 'desc');
                    break;
                case 'most_quotes':
                    $query->orderBy('public_quotes_count', 'desc');
                    break;
                case 'most_discussions':
                    $query->orderBy('discussions_count', 'desc');
                    break;
                case 'most_books_read':
                    $query->orderBy('read_books_count', 'desc');
                    break;
            }
        }

        $sort = $request->get('sort', 'rating');
        switch ($sort) {
            case 'name':
                $query->orderBy('name');
                break;
            case 'username':
                $query->orderBy('username');
                break;
            case 'reviews':
                $query->orderByDesc('main_reviews_count');
                break;
            case 'quotes':
                $query->orderByDesc('public_quotes_count');
                break;
            case 'books':
                $query->orderByDesc('read_books_count');
                break;
            default:
                $query->orderByRaw("$ratingRawExpression DESC");
        }

        $perPage = (int) $request->get('per_page', 18);
        $perPage = max(9, min($perPage, 36));
        $users = $query->paginate($perPage)->appends($request->query());

        $users->getCollection()->transform(function ($user) {
            $scoreRaw = min(
                ((int) $user->main_reviews_count * 10) +
                ((int) $user->public_quotes_count * 5) +
                ((int) $user->published_publications_count * 15) +
                ((int) $user->ratings_count * 2) +
                ((int) $user->read_books_count * 3) +
                ((int) $user->discussions_count * 8) +
                ((int) $user->replies_count * 3) +
                ((int) $user->review_replies_count * 2),
                100
            );
            $user->rating_score = round($scoreRaw / 10, 1);
            $user->stars_count = max(1, min(10, (int) ceil($scoreRaw / 10)));
            return $user;
        });

        // Статистика для сайдбара
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::whereHas('reviews')->orWhereHas('quotes')->orWhereHas('discussions')->count(),
            'top_reviewers' => User::withCount(['reviews as main_reviews_count' => function($query) {
                $query->whereNull('parent_id');
            }])->orderBy('main_reviews_count', 'desc')->limit(5)->get(),
        ];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'users' => $users->getCollection()->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'avatar' => $user->avatar_display,
                        'rating_score' => $user->rating_score,
                        'stars_count' => $user->stars_count,
                        'main_reviews_count' => (int) $user->main_reviews_count,
                        'public_quotes_count' => (int) $user->public_quotes_count,
                        'read_books_count' => (int) $user->read_books_count,
                        'discussions_count' => (int) $user->discussions_count,
                        'profile_url' => url('/account/u/' . $user->username . '/overview'),
                    ];
                })->values(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ],
                'stats' => [
                    'total_users' => (int) $stats['total_users'],
                    'active_users' => (int) $stats['active_users'],
                    'found_users' => (int) $users->total(),
                ],
            ]);
        }

        return view('users.index', compact('users', 'stats'));
    }

    /**
     * Получить распределение рейтингов
     */
    private function getRatingDistribution()
    {
        $distribution = [];
        for ($i = 1; $i <= 10; $i++) {
            $count = 0;
            foreach (User::all() as $user) {
                if ($user->getStarsCount() === $i) {
                    $count++;
                }
            }
            $distribution[$i] = $count;
        }
        return $distribution;
    }

    /**
     * Получить последнюю активность
     */
    private function getRecentActivity()
    {
        $recentReviews = \App\Models\Review::with('user', 'book')
            ->whereNull('parent_id')
            ->latest()
            ->limit(3)
            ->get();

        $recentQuotes = \App\Models\Quote::with('user', 'book')
            ->where('is_public', true)
            ->latest()
            ->limit(3)
            ->get();

        $recentDiscussions = \App\Models\Discussion::with('user')
            ->whereIn('status', ['active', 'blocked']) // Включаем заблокированные
            ->latest()
            ->limit(3)
            ->get();

        return [
            'reviews' => $recentReviews,
            'quotes' => $recentQuotes,
            'discussions' => $recentDiscussions,
        ];
    }

    /**
     * Показать публичный профиль пользователя (новая верстка)
     */
    public function publicProfile($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        // Проверяем, является ли просматривающий владельцем профиля
        $isOwner = auth()->check() && auth()->id() === $user->id;
        
        // Проверяем публичность профиля
        if (!$isOwner && !$user->public_profile) {
            return view('users.public.private-profile', compact('user'));
        }
        
        // Загружаем данные для статистики с учетом настроек приватности
        $stats = [
            'discussions_count' => $user->discussions()->where('status', 'active')->count(),
            'reviews_count' => $user->reviews()->whereNull('parent_id')->where('is_draft', false)->count(),
            'quotes_count' => $user->quotes()->where('is_public', true)->count(),
        ];
        
        // Добавляем статистику читания только если разрешено или это владелец
        if ($isOwner || $user->show_reading_stats) {
            $stats['read_count'] = $user->readingStatuses()->where('status', 'read')->count();
            $stats['reading_count'] = $user->readingStatuses()->where('status', 'reading')->count();
            $stats['want_to_read_count'] = $user->readingStatuses()->where('status', 'want_to_read')->count();
            $stats['average_rating'] = $user->readingStatuses()->whereNotNull('rating')->avg('rating');
        } else {
            $stats['read_count'] = null;
            $stats['reading_count'] = null;
            $stats['want_to_read_count'] = null;
            $stats['average_rating'] = null;
        }

        // Статистика рейтингов (1-10) только если разрешено показывать рейтинги или это владелец
        $ratingStats = [];
        if ($isOwner || $user->show_ratings) {
            for ($i = 1; $i <= 10; $i++) {
                $ratingStats[$i] = $user->readingStatuses()
                    ->whereNotNull('rating')
                    ->where('rating', $i)
                    ->count();
            }
            $totalRatedBooks = array_sum($ratingStats);
            $stats['total_rated_books'] = $totalRatedBooks;
        } else {
            for ($i = 1; $i <= 10; $i++) {
                $ratingStats[$i] = 0;
            }
            $stats['total_rated_books'] = 0;
        }

        // Недавно прочитанные книги (только если разрешено или это владелец)
        $recentReadBooks = collect();
        if ($isOwner || $user->show_reading_stats) {
            $recentReadBooks = $user->readingStatuses()
                ->where('status', 'read')
                ->orderBy('finished_at', 'desc')
                ->limit(4)
                ->get();
            
            // Використовуємо кешовані дані книг
            $recentReadBooks->transform(function ($status) {
                $cachedBookData = Book::getCachedBookData($status->book_id);
                if ($cachedBookData) {
                    $status->setRelation('book', (object) $cachedBookData);
                } else {
                    $status->load('book');
                    if ($status->book) {
                        $status->book->cacheBookData();
                    }
                }
                return $status;
            });
        }

        // Недавние рецензии
        $recentReviews = $user->reviews()
            ->with(['book'])
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Пользовательские библиотеки для авторизованного пользователя
        $userLibraries = [];
        if (auth()->check()) {
            $userLibraries = auth()->user()->libraries()->get()->map(function($library) {
                return [
                    'id' => $library->id,
                    'name' => $library->name,
                ];
            })->toArray();
        }

        // Загружаем награды пользователя
        $userAwards = $user->awards()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('users.public.profile', compact('user', 'stats', 'ratingStats', 'recentReadBooks', 'recentReviews', 'userLibraries', 'isOwner', 'userAwards'));
    }

    /**
     * Показать публичную библиотеку пользователя
     */
    public function publicLibrary($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        // Проверяем, является ли просматривающий владельцем профиля
        $isOwner = auth()->check() && auth()->id() === $user->id;
        
        // Проверяем публичность профиля
        if (!$isOwner && !$user->public_profile) {
            return view('users.public.private-profile', compact('user'));
        }
        
        // Проверяем настройки приватности для статистики читания
        if (!$isOwner && !$user->show_reading_stats) {
            return view('users.public.private-stats', compact('user'));
        }
        
        // Загружаем данные для статистики
        $stats = [
            'discussions_count' => $user->discussions()->where('status', 'active')->count(),
            'reviews_count' => $user->reviews()->whereNull('parent_id')->where('is_draft', false)->count(),
            'quotes_count' => $user->quotes()->where('is_public', true)->count(),
            'read_count' => $user->readingStatuses()->where('status', 'read')->count(),
            'reading_count' => $user->readingStatuses()->where('status', 'reading')->count(),
            'want_to_read_count' => $user->readingStatuses()->where('status', 'want_to_read')->count(),
            'average_rating' => $user->readingStatuses()->whereNotNull('rating')->avg('rating'),
        ];
        
        $recentReadBooks = $user->readingStatuses()
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();
        
        // Використовуємо кешовані дані книг
        $recentReadBooks->transform(function ($status) {
            $cachedBookData = Book::getCachedBookData($status->book_id);
            if ($cachedBookData) {
                $status->setRelation('book', (object) $cachedBookData);
            } else {
                $status->load('book');
                if ($status->book) {
                    $status->book->cacheBookData();
                }
            }
            return $status;
        });
        
        // Награды пользователя
        $userAwards = $user->awards()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        return view('users.public.library', compact('user', 'stats', 'recentReadBooks', 'userAwards'));
    }

    /**
     * Показать публичные рецензии пользователя
     */
    public function publicReviews($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        // Проверяем, является ли просматривающий владельцем профиля
        $isOwner = auth()->check() && auth()->id() === $user->id;
        
        // Проверяем публичность профиля
        if (!$isOwner && !$user->public_profile) {
            return view('users.public.private-profile', compact('user'));
        }
        
        // Загружаем данные для статистики с учетом настроек приватности
        $stats = [
            'discussions_count' => $user->discussions()->where('status', 'active')->count(),
            'reviews_count' => $user->reviews()->whereNull('parent_id')->where('is_draft', false)->count(),
            'quotes_count' => $user->quotes()->where('is_public', true)->count(),
        ];
        
        // Добавляем статистику читания только если разрешено или это владелец
        if ($isOwner || $user->show_reading_stats) {
            $stats['read_count'] = $user->readingStatuses()->where('status', 'read')->count();
            $stats['reading_count'] = $user->readingStatuses()->where('status', 'reading')->count();
            $stats['want_to_read_count'] = $user->readingStatuses()->where('status', 'want_to_read')->count();
            $stats['average_rating'] = $user->readingStatuses()->whereNotNull('rating')->avg('rating');
        } else {
            $stats['read_count'] = null;
            $stats['reading_count'] = null;
            $stats['want_to_read_count'] = null;
            $stats['average_rating'] = null;
        }
        
        $reviews = $user->reviews()
            ->with(['book'])
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $recentReadBooks = collect();
        if ($isOwner || $user->show_reading_stats) {
            $recentReadBooks = $user->readingStatuses()
                ->with('book')
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get();
        }
        
        // Награды пользователя
        $userAwards = $user->awards()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        return view('users.public.reviews', compact('user', 'stats', 'reviews', 'recentReadBooks', 'userAwards'));
    }

    /**
     * Показать публичные обсуждения пользователя
     */
    public function publicDiscussions($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        // Проверяем, является ли просматривающий владельцем профиля
        $isOwner = auth()->check() && auth()->id() === $user->id;
        
        // Проверяем публичность профиля
        if (!$isOwner && !$user->public_profile) {
            return view('users.public.private-profile', compact('user'));
        }
        
        // Загружаем данные для статистики с учетом настроек приватности
        $stats = [
            'discussions_count' => $user->discussions()->where('status', 'active')->count(),
            'reviews_count' => $user->reviews()->whereNull('parent_id')->where('is_draft', false)->count(),
            'quotes_count' => $user->quotes()->where('is_public', true)->count(),
        ];
        
        // Добавляем статистику читания только если разрешено или это владелец
        if ($isOwner || $user->show_reading_stats) {
            $stats['read_count'] = $user->readingStatuses()->where('status', 'read')->count();
            $stats['reading_count'] = $user->readingStatuses()->where('status', 'reading')->count();
            $stats['want_to_read_count'] = $user->readingStatuses()->where('status', 'want_to_read')->count();
            $stats['average_rating'] = $user->readingStatuses()->whereNotNull('rating')->avg('rating');
        } else {
            $stats['read_count'] = null;
            $stats['reading_count'] = null;
            $stats['want_to_read_count'] = null;
            $stats['average_rating'] = null;
        }
        
        $discussions = $user->discussions()
            ->withCount(['replies', 'likes'])
            ->whereIn('status', ['active', 'blocked']) // Включаем заблокированные
            ->where('is_closed', false)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $recentReadBooks = collect();
        if ($isOwner || $user->show_reading_stats) {
            $recentReadBooks = $user->readingStatuses()
                ->with('book')
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get();
        }
        
        // Награды пользователя
        $userAwards = $user->awards()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        return view('users.public.discussions', compact('user', 'stats', 'discussions', 'recentReadBooks', 'userAwards'));
    }

    /**
     * Показать публичные цитаты пользователя
     */
    public function publicQuotes($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        // Проверяем, является ли просматривающий владельцем профиля
        $isOwner = auth()->check() && auth()->id() === $user->id;
        
        // Проверяем публичность профиля
        if (!$isOwner && !$user->public_profile) {
            return view('users.public.private-profile', compact('user'));
        }
        
        // Загружаем данные для статистики с учетом настроек приватности
        $stats = [
            'discussions_count' => $user->discussions()->where('status', 'active')->count(),
            'reviews_count' => $user->reviews()->whereNull('parent_id')->where('is_draft', false)->count(),
            'quotes_count' => $user->quotes()->where('is_public', true)->count(),
        ];
        
        // Добавляем статистику читания только если разрешено или это владелец
        if ($isOwner || $user->show_reading_stats) {
            $stats['read_count'] = $user->readingStatuses()->where('status', 'read')->count();
            $stats['reading_count'] = $user->readingStatuses()->where('status', 'reading')->count();
            $stats['want_to_read_count'] = $user->readingStatuses()->where('status', 'want_to_read')->count();
            $stats['average_rating'] = $user->readingStatuses()->whereNotNull('rating')->avg('rating');
        } else {
            $stats['read_count'] = null;
            $stats['reading_count'] = null;
            $stats['want_to_read_count'] = null;
            $stats['average_rating'] = null;
        }
        
        $quotes = $user->quotes()
            ->where('is_public', true)
            ->with('book')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        $recentReadBooks = collect();
        if ($isOwner || $user->show_reading_stats) {
            $recentReadBooks = $user->readingStatuses()
                ->with('book')
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get();
        }
        
        // Награды пользователя
        $userAwards = $user->awards()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        return view('users.public.quotes', compact('user', 'stats', 'quotes', 'recentReadBooks', 'userAwards'));
    }

    /**
     * Показать публичные коллекции пользователя
     */
    public function publicCollections($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        // Проверяем, является ли просматривающий владельцем профиля
        $isOwner = auth()->check() && auth()->id() === $user->id;
        
        // Проверяем публичность профиля
        if (!$isOwner && !$user->public_profile) {
            return view('users.public.private-profile', compact('user'));
        }
        
        // Загружаем данные для статистики с учетом настроек приватности
        $stats = [
            'discussions_count' => $user->discussions()->where('status', 'active')->count(),
            'reviews_count' => $user->reviews()->whereNull('parent_id')->where('is_draft', false)->count(),
            'quotes_count' => $user->quotes()->where('is_public', true)->count(),
        ];
        
        // Добавляем статистику читания только если разрешено или это владелец
        if ($isOwner || $user->show_reading_stats) {
            $stats['read_count'] = $user->readingStatuses()->where('status', 'read')->count();
            $stats['reading_count'] = $user->readingStatuses()->where('status', 'reading')->count();
            $stats['want_to_read_count'] = $user->readingStatuses()->where('status', 'want_to_read')->count();
            $stats['average_rating'] = $user->readingStatuses()->whereNotNull('rating')->avg('rating');
        } else {
            $stats['read_count'] = null;
            $stats['reading_count'] = null;
            $stats['want_to_read_count'] = null;
            $stats['average_rating'] = null;
        }
        
        try {
            // Готовим коллекции и книги для публичного профиля
            $libraries = $user->libraries()
                ->where('is_private', false)
                ->withCount('books')
                ->with([
                    'user',
                    'books' => function ($q) {
                        $q->select('books.id','books.slug','books.title','books.cover_image','books.author')
                          ->limit(3);
                    },
                    'likes'
                ])
                ->orderBy('created_at','desc')
                ->get();

            $selectedLibrary = $libraries->first();

            $books = collect();
            if ($selectedLibrary) {
                $books = $selectedLibrary->books()
                    ->select('books.*')
                    ->with(['author','categories'])
                    ->paginate(12);
            }
        } catch (\Exception $e) {
            $libraries = collect();
            $selectedLibrary = null;
            $books = collect();
        }
        
        $recentReadBooks = collect();
        if ($isOwner || $user->show_reading_stats) {
            $recentReadBooks = $user->readingStatuses()
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get();
            
            // Використовуємо кешовані дані книг
            $recentReadBooks->transform(function ($status) {
                $cachedBookData = Book::getCachedBookData($status->book_id);
                if ($cachedBookData) {
                    $status->setRelation('book', (object) $cachedBookData);
                } else {
                    $status->load('book');
                    if ($status->book) {
                        $status->book->cacheBookData();
                    }
                }
                return $status;
            });
        }
        
        // Награды пользователя
        $userAwards = $user->awards()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        return view('users.public.collections', compact('user', 'stats', 'libraries', 'books', 'selectedLibrary', 'recentReadBooks', 'userAwards'));
    }

    /**
     * Показать публичные награды пользователя
     */
    public function publicAwards($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        // Проверяем, является ли просматривающий владельцем профиля
        $isOwner = auth()->check() && auth()->id() === $user->id;
        
        // Проверяем публичность профиля
        if (!$isOwner && !$user->public_profile) {
            return view('users.public.private-profile', compact('user'));
        }
        
        // Загружаем данные для статистики с учетом настроек приватности
        $stats = [
            'discussions_count' => $user->discussions()->where('status', 'active')->count(),
            'reviews_count' => $user->reviews()->whereNull('parent_id')->where('is_draft', false)->count(),
            'quotes_count' => $user->quotes()->where('is_public', true)->count(),
        ];
        
        // Добавляем статистику читания только если разрешено или это владелец
        if ($isOwner || $user->show_reading_stats) {
            $stats['read_count'] = $user->readingStatuses()->where('status', 'read')->count();
            $stats['reading_count'] = $user->readingStatuses()->where('status', 'reading')->count();
            $stats['want_to_read_count'] = $user->readingStatuses()->where('status', 'want_to_read')->count();
            $stats['average_rating'] = $user->readingStatuses()->whereNotNull('rating')->avg('rating');
        } else {
            $stats['read_count'] = null;
            $stats['reading_count'] = null;
            $stats['want_to_read_count'] = null;
            $stats['average_rating'] = null;
        }

        // Недавно прочитанные книги (только если разрешено или это владелец)
        $recentReadBooks = collect();
        if ($isOwner || $user->show_reading_stats) {
            $recentReadBooks = $user->readingStatuses()
                ->where('status', 'read')
                ->with('book')
                ->orderBy('finished_at', 'desc')
                ->limit(4)
                ->get();
        }

        // Загружаем все награды пользователя
        $userAwards = $user->awards()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('users.public.awards', compact('user', 'stats', 'recentReadBooks', 'userAwards', 'isOwner'));
    }
}
