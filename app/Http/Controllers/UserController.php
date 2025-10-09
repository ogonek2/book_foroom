<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            'reviews_count' => $user->reviews()->whereNull('parent_id')->count(),
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
            'readingStatuses as ratings_count' => function($query) {
                $query->whereNotNull('rating');
            },
            'readingStatuses as read_books_count' => function($query) {
                $query->where('status', 'read');
            }
        ]);

        // Поиск по юзернейму или имени
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Фильтр по рейтингу
        if ($request->has('rating_filter') && $request->rating_filter) {
            $ratingFilter = $request->rating_filter;
            // Применяем фильтр после расчета рейтинга
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

        $users = $query->get()
        ->map(function($user) {
            // Добавляем рассчитанный рейтинг
            $user->calculated_rating = $user->calculateRating();
            $user->stars_count = $user->getStarsCount();
            $user->rating_score = $user->getRatingScore();
            return $user;
        });

        // Применяем фильтр по рейтингу после расчета
        if ($request->has('rating_filter') && $request->rating_filter) {
            $ratingFilter = $request->rating_filter;
            switch ($ratingFilter) {
                case '5_stars':
                    $users = $users->filter(function($user) {
                        return $user->stars_count >= 5;
                    });
                    break;
                case '7_stars':
                    $users = $users->filter(function($user) {
                        return $user->stars_count >= 7;
                    });
                    break;
                case '9_stars':
                    $users = $users->filter(function($user) {
                        return $user->stars_count >= 9;
                    });
                    break;
            }
        }

        // Сортировка
        $sort = $request->get('sort', 'rating');
        switch ($sort) {
            case 'name':
                $users = $users->sortBy('name')->values();
                break;
            case 'username':
                $users = $users->sortBy('username')->values();
                break;
            case 'reviews':
                $users = $users->sortByDesc('main_reviews_count')->values();
                break;
            case 'quotes':
                $users = $users->sortByDesc('public_quotes_count')->values();
                break;
            default:
                $users = $users->sortByDesc('calculated_rating')->values();
        }

        // Создаем пагинацию вручную
        $perPage = 32;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = $users->slice($offset, $perPage);
        
        $users = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $users->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        // Статистика для сайдбара
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::whereHas('reviews')->orWhereHas('quotes')->orWhereHas('discussions')->count(),
            'top_reviewers' => User::withCount(['reviews as main_reviews_count' => function($query) {
                $query->whereNull('parent_id');
            }])->orderBy('main_reviews_count', 'desc')->limit(5)->get(),
            'rating_distribution' => $this->getRatingDistribution(),
            'recent_activity' => $this->getRecentActivity(),
        ];

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
            ->where('status', 'active')
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
            'reviews_count' => $user->reviews()->whereNull('parent_id')->count(),
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
                ->with('book')
                ->orderBy('finished_at', 'desc')
                ->limit(4)
                ->get();
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

        return view('users.public.profile', compact('user', 'stats', 'ratingStats', 'recentReadBooks', 'recentReviews', 'userLibraries', 'isOwner'));
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
            'reviews_count' => $user->reviews()->whereNull('parent_id')->count(),
            'quotes_count' => $user->quotes()->where('is_public', true)->count(),
            'read_count' => $user->readingStatuses()->where('status', 'read')->count(),
            'reading_count' => $user->readingStatuses()->where('status', 'reading')->count(),
            'want_to_read_count' => $user->readingStatuses()->where('status', 'want_to_read')->count(),
            'average_rating' => $user->readingStatuses()->whereNotNull('rating')->avg('rating'),
        ];
        
        $recentReadBooks = $user->readingStatuses()
            ->with('book')
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();
        
        return view('users.public.library', compact('user', 'stats', 'recentReadBooks'));
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
            'reviews_count' => $user->reviews()->whereNull('parent_id')->count(),
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
        
        return view('users.public.reviews', compact('user', 'stats', 'reviews', 'recentReadBooks'));
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
            'reviews_count' => $user->reviews()->whereNull('parent_id')->count(),
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
            ->where('status', 'active')
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
        
        return view('users.public.discussions', compact('user', 'stats', 'discussions', 'recentReadBooks'));
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
            'reviews_count' => $user->reviews()->whereNull('parent_id')->count(),
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
        
        return view('users.public.quotes', compact('user', 'stats', 'quotes', 'recentReadBooks'));
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
            'reviews_count' => $user->reviews()->whereNull('parent_id')->count(),
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
            // Показываем только публичные коллекции для других пользователей
            $libraries = $user->libraries()
                ->where('is_private', false)
                ->withCount('books')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $selectedLibrary = $libraries->first();
            
            if ($selectedLibrary) {
                $books = $selectedLibrary->books()->with(['author', 'category'])->paginate(12);
            } else {
                $books = collect();
            }
        } catch (\Exception $e) {
            $libraries = collect();
            $books = collect();
            $selectedLibrary = null;
        }
        
        $recentReadBooks = collect();
        if ($isOwner || $user->show_reading_stats) {
            $recentReadBooks = $user->readingStatuses()
                ->with('book')
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get();
        }
        
        return view('users.public.collections', compact('user', 'stats', 'libraries', 'books', 'selectedLibrary', 'recentReadBooks'));
    }
}
