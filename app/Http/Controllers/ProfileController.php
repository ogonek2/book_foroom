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
            'favorites' => 'profile.private.favorites',
            'drafts' => 'profile.private.drafts',
        ];
        
        $view = $viewMap[$tab] ?? 'profile.private.overview';
        
        // Загружаем награды пользователя (для всех вкладок)
        $userAwards = $user->awards()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        // Для вкладки collections нужно передать библиотеки
        $data = compact('user', 'stats', 'ratingStats', 'recentActivity', 'userAwards');
        
        if ($tab === 'collections') {
            $libraries = $user->libraries()->withCount('books')->with(['books' => function($q) {
                $q->limit(3); // Для предварительного просмотра берем только первые 3 книги
            }, 'likes'])->orderBy('created_at', 'desc')->get();
            
            $savedLibraries = $user->savedLibraries()->withCount('books')->with(['books' => function($q) {
                $q->limit(3); // Для предварительного просмотра берем только первые 3 книги
            }, 'likes', 'user'])->orderBy('saved_libraries.created_at', 'desc')->get();
            
            $data['libraries'] = $libraries;
            $data['savedLibraries'] = $savedLibraries;
        }
        
        if ($tab === 'favorites') {
            $favoriteQuotes = $user->favoriteQuotes()
                ->with(['book', 'user'])
                ->orderBy('favorite_quotes.created_at', 'desc')
                ->paginate(10);
            
            $favoriteReviews = $user->favoriteReviews()
                ->with(['book', 'user'])
                ->whereNull('parent_id') // Только основные рецензии, не ответы
                ->orderBy('favorite_reviews.created_at', 'desc')
                ->paginate(10);
            
            $data['favoriteQuotes'] = $favoriteQuotes;
            $data['favoriteReviews'] = $favoriteReviews;
        }
        
        if ($tab === 'drafts') {
            $draftReviews = \App\Models\Review::where('user_id', $user->id)
                ->where('is_draft', true)
                ->whereNull('parent_id')
                ->with('book')
                ->orderBy('updated_at', 'desc')
                ->get();
            
            $draftQuotes = \App\Models\Quote::where('user_id', $user->id)
                ->where('is_draft', true)
                ->with('book')
                ->orderBy('updated_at', 'desc')
                ->get();
            
            $draftDiscussions = \App\Models\Discussion::where('user_id', $user->id)
                ->where('is_draft', true)
                ->orderBy('updated_at', 'desc')
                ->get();
            
            $data['draftReviews'] = $draftReviews;
            $data['draftQuotes'] = $draftQuotes;
            $data['draftDiscussions'] = $draftDiscussions;
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
                        ->withErrors(['avatar' => 'Помилка завантаження аватарки'])
                        ->withInput();
                }
                
                $data['avatar'] = $avatarUrl;
                
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['avatar' => 'Помилка завантаження аватарки: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        $user->update($data);

        return redirect()->route('profile.show')
            ->with('success', 'Профіль успішно оновлений!');
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
            ->with('success', 'Аватарка видалена!');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check current password
        if (!password_verify($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Неверный текущий пароль']);
        }

        // Update password
        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Пароль успішно змінений!');
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        
        $user->update([
            'email_notifications' => $request->has('email_notifications'),
            'new_books_notifications' => $request->has('new_books_notifications'),
            'comments_notifications' => $request->has('comments_notifications'),
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Налаштування повідомлень оновлені!');
    }

    /**
     * Update privacy settings
     */
    public function updatePrivacy(Request $request)
    {
        $user = Auth::user();
        
        $user->update([
            'public_profile' => $request->has('public_profile'),
            'show_reading_stats' => $request->has('show_reading_stats'),
            'show_ratings' => $request->has('show_ratings'),
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Налаштування приватності оновлені!');
    }

    /**
     * Export user data
     */
    public function export()
    {
        $user = Auth::user();
        
        $data = [
            'user' => $user->toArray(),
            'reading_statuses' => $user->bookReadingStatuses()->with('book')->get()->toArray(),
            'reviews' => $user->reviews()->with('book')->get()->toArray(),
            'libraries' => $user->libraries()->with('books')->get()->toArray(),
            'discussions' => $user->discussions()->get()->toArray(),
            'quotes' => $user->quotes()->get()->toArray(),
            'exported_at' => now()->toISOString(),
        ];

        $filename = 'user_data_' . $user->username . '_' . now()->format('Y-m-d_H-i-s') . '.json';

        // Конвертируем в JSON с правильной кодировкой для кириллицы
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return response($json, 200)
            ->header('Content-Type', 'application/json; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Delete user account
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        
        // Soft delete user and related data
        $user->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Ваш аккаунт був успішно видалений.');
    }
}
