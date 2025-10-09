<?php

namespace App\Http\Controllers;

use App\Helpers\CDNUploader;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;

class ProfileController extends Controller
{
    public function show($username = null)
    {
        $user = $username ? User::where('username', $username)->firstOrFail() : Auth::user();
        
        // Если это публичный профиль другого пользователя
        if ($username && $user->id !== Auth::id()) {
            return redirect()->route('users.public.profile', $user->username);
        }
        
        // Для приватного профиля текущего пользователя
        $tab = request('tab', 'overview');
        
        // Получаем статистику
        $stats = $this->getUserStats($user);
        $ratingStats = $this->getRatingStats($user);
        
        // Получаем последнюю активность
        $recentActivity = $this->getRecentActivity($user);
        
        // Определяем какой шаблон использовать в зависимости от вкладки
        $viewMap = [
            'overview' => 'profile.private.overview',
            'library' => 'profile.private.library',
            'reviews' => 'profile.private.reviews',
            'discussions' => 'profile.private.discussions',
            'quotes' => 'profile.private.quotes',
            'collections' => 'profile.pages.collections', // Используем существующий шаблон для добірок
        ];
        
        $view = $viewMap[$tab] ?? 'profile.private.overview';
        
        // Для вкладки collections нужно передать библиотеки
        $data = compact('user', 'stats', 'ratingStats', 'recentActivity');
        
        if ($tab === 'collections') {
            $libraries = $user->libraries()->withCount('books')->with(['books' => function($q) {
                $q->limit(3); // Для предварительного просмотра берем только первые 3 книги
            }, 'likes'])->orderBy('created_at', 'desc')->get();
            $data['libraries'] = $libraries;
        }
        
        return view($view, $data);
    }

    /**
     * Get user statistics
     */
    private function getUserStats($user)
    {
        return [
            'total_rated_books' => $user->ratings()->count(),
            'total_reviews' => $user->reviews()->count(),
            'total_discussions' => $user->discussions()->count(),
            'total_libraries' => $user->libraries()->count(),
            'average_rating' => $user->ratings()->avg('rating'),
        ];
    }

    /**
     * Get rating statistics for chart
     */
    private function getRatingStats($user)
    {
        $ratingStats = [];
        for ($i = 1; $i <= 10; $i++) {
            $ratingStats[$i] = $user->ratings()->where('rating', $i)->count();
        }
        return $ratingStats;
    }

    /**
     * Get recent user activity
     */
    private function getRecentActivity($user)
    {
        $activities = collect();
        
        // Recent ratings
        $recentRatings = $user->ratings()
            ->with('book')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($rating) {
                return (object) [
                    'id' => $rating->id,
                    'type' => 'rating',
                    'description' => "Оцінив книгу \"{$rating->book->title}\" на {$rating->rating}/10",
                    'created_at' => $rating->created_at,
                ];
            });
        
        // Recent reviews
        $recentReviews = $user->reviews()
            ->with('book')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($review) {
                return (object) [
                    'id' => $review->id,
                    'type' => 'review',
                    'description' => "Написав рецензію на \"{$review->book->title}\"",
                    'created_at' => $review->created_at,
                ];
            });
        
        // Recent discussions
        $recentDiscussions = $user->discussions()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($discussion) {
                return (object) [
                    'id' => $discussion->id,
                    'type' => 'discussion',
                    'description' => "Створив обговорення \"{$discussion->title}\"",
                    'created_at' => $discussion->created_at,
                ];
            });
        
        // Combine and sort by date
        $activities = $activities
            ->merge($recentRatings)
            ->merge($recentReviews)
            ->merge($recentDiscussions)
            ->sortByDesc('created_at')
            ->take(10);
        
        return $activities;
    }

    public function library($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        return view('profile.pages.library', compact('user'));
    }

    public function reviews($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        return view('profile.pages.reviews', compact('user'));
    }

    public function discussions($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        $discussions = $user->discussions()
            ->withCount(['replies', 'likes'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('profile.pages.discussions', compact('user', 'discussions'));
    }

    public function quotes($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        return view('profile.pages.quotes', compact('user'));
    }

    public function collections($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        // Проверяем, что у пользователя есть библиотеки
        try {
            $libraries = $user->libraries()->withCount('books')->with(['books' => function($q) {
                $q->limit(3); // Для предварительного просмотра берем только первые 3 книги
            }, 'likes'])->orderBy('created_at', 'desc')->get();
            $selectedLibrary = $libraries->first(); // Выбираем первую библиотеку по умолчанию
            
            if ($selectedLibrary) {
                $books = $selectedLibrary->books()->with(['author', 'category'])->paginate(12);
            } else {
                $books = collect();
            }
        } catch (\Exception $e) {
            // Если таблица libraries не существует, возвращаем пустые коллекции
            $libraries = collect();
            $books = collect();
            $selectedLibrary = null;
        }
        
        return view('profile.pages.collections', compact('user', 'libraries', 'books', 'selectedLibrary'));
    }

    public function getLibraryBooks($username, $libraryId)
    {
        $user = User::where('username', $username)->firstOrFail();
        $library = $user->libraries()->findOrFail($libraryId);
        
        // Проверяем права доступа
        if (!$library->canBeViewedBy(auth()->user())) {
            return response()->json(['error' => 'У вас нет доступа к этой библиотеке'], 403);
        }
        
        $books = $library->books()->with(['author', 'category'])->paginate(12);
        
        return response()->json([
            'library' => [
                'id' => $library->id,
                'name' => $library->name,
                'description' => $library->description,
                'is_private' => $library->is_private,
                'books_count' => $library->books_count
            ],
            'books' => $books->items(),
            'pagination' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total()
            ]
        ]);
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'avatar' => [
                'nullable',
                File::image()
                    ->max(2048) // 2MB
                    ->types(['jpeg', 'png', 'gif', 'webp'])
            ],
        ]);

        $data = $request->only(['name', 'username', 'email', 'bio']);

        // Обработка загрузки аватарки
        if ($request->hasFile('avatar')) {
            try {
                // Удаляем старую аватарку, если она есть
                if ($user->avatar) {
                    CDNUploader::deleteFromBunnyCDN($user->avatar);
                }

                // Загружаем новую аватарку (сначала CDN, потом локально)
                $avatarUrl = CDNUploader::uploadFile($request->file('avatar'), 'avatars');
                
                if (!$avatarUrl) {
                    return redirect()->back()
                        ->withErrors(['avatar' => 'Ошибка загрузки аватарки'])
                        ->withInput();
                }
                
                $data['avatar'] = $avatarUrl;
                
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['avatar' => 'Ошибка загрузки аватарки: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        $user->update($data);

        return redirect()->route('profile.show')
            ->with('success', 'Профиль успешно обновлен!');
    }

    public function destroyAvatar()
    {
        $user = Auth::user();

        if ($user->avatar) {
            try {
                // Удаляем аватар с CDN
                CDNUploader::deleteFromBunnyCDN($user->avatar);
            } catch (\Exception $e) {
                // Логируем ошибку, но не прерываем процесс
                \Log::warning('Failed to delete avatar from CDN: ' . $e->getMessage());
            }
            
            $user->update(['avatar' => null]);
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Аватарка удалена!');
    }
}
