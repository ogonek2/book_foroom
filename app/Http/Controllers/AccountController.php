<?php

namespace App\Http\Controllers;

use App\Helpers\CDNUploader;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class AccountController extends Controller
{
    public function show(Request $request, ?string $username = null)
    {
        // When the SPA catch-all route passes something like "u/username/overview"
        // we must not treat it as a username.
        if ($username && str_contains($username, '/')) {
            $username = null;
        }

        return view('account.app', [
            'username' => $username,
        ]);
    }

    public function profile(string $username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $viewer = Auth::user();
        $isOwner = $viewer && $viewer->id === $user->id;

        if (!$isOwner && !$user->public_profile) {
            return response()->json([
                'message' => 'Профіль приватний.',
            ], 403);
        }

        $readCount = $user->readingStatuses()->where('status', 'read')->count();
        $readingCount = $user->readingStatuses()->where('status', 'reading')->count();
        $plannedCount = $user->readingStatuses()->where('status', 'want_to_read')->count();
        $droppedCount = $user->readingStatuses()->where('status', 'abandoned')->count();

        $recentReadBooks = $user->readingStatuses()
            ->whereIn('status', ['read', 'reading', 'want_to_read'])
            ->with('book')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get()
            ->map(function ($status) {
                return [
                    'id' => $status->id,
                    'status' => $status->status,
                    'updated_at_human' => optional($status->updated_at)->diffForHumans(),
                    'book' => $status->book ? [
                        'id' => $status->book->id,
                        'slug' => $status->book->slug,
                        'title' => $status->book->title,
                        'cover' => $status->book->cover_image_display ?? $status->book->cover_image ?? null,
                    ] : null,
                ];
            })
            ->values();

        $recentReviews = $user->reviews()
            ->whereNull('parent_id')
            ->where('is_draft', false)
            ->with('book')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'book' => $review->book ? [
                        'title' => $review->book->title,
                        'slug' => $review->book->slug,
                    ] : null,
                    'rating' => $review->rating ?? null,
                    'content' => mb_substr((string) $review->content, 0, 160),
                    'created_at_human' => optional($review->created_at)->diffForHumans(),
                ];
            })
            ->values();

        $recentDiscussions = $user->discussions()
            ->whereIn('status', ['active', 'blocked'])
            ->withCount(['replies', 'likes'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(function ($discussion) {
                return [
                    'id' => $discussion->id,
                    'title' => $discussion->title,
                    'status' => $discussion->status,
                    'replies_count' => $discussion->replies_count,
                    'likes_count' => $discussion->likes_count,
                    'created_at_human' => optional($discussion->created_at)->diffForHumans(),
                ];
            })
            ->values();

        $recentQuotes = $user->quotes()
            ->where('is_public', true)
            ->with('book')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(function ($quote) {
                return [
                    'id' => $quote->id,
                    'content' => mb_substr((string) $quote->content, 0, 180),
                    'book' => $quote->book ? [
                        'title' => $quote->book->title,
                        'slug' => $quote->book->slug,
                    ] : null,
                    'created_at_human' => optional($quote->created_at)->diffForHumans(),
                ];
            })
            ->values();

        $librariesQuery = $user->libraries()->withCount('books')->orderByDesc('created_at');
        if (!$isOwner) {
            $librariesQuery->where('is_private', false);
        }

        $collections = $librariesQuery
            ->limit(6)
            ->get()
            ->map(function ($library) {
                return [
                    'id' => $library->id,
                    'name' => $library->name,
                    'description' => $library->description,
                    'books_count' => $library->books_count,
                    'is_private' => (bool) $library->is_private,
                    'created_at_human' => optional($library->created_at)->diffForHumans(),
                ];
            })
            ->values();

        $favoriteQuotes = collect();
        $favoriteReviews = collect();
        $draftReviews = collect();
        $draftQuotes = collect();
        $draftDiscussions = collect();

        if ($isOwner) {
            $favoriteQuotes = $user->favoriteQuotes()
                ->with('book')
                ->orderByDesc('favorite_quotes.created_at')
                ->limit(20)
                ->get()
                ->map(function ($quote) {
                    return [
                        'id' => $quote->id,
                        'book_slug' => optional($quote->book)->slug,
                        'book_title' => optional($quote->book)->title,
                        'content' => mb_substr((string) $quote->content, 0, 220),
                        'created_at_human' => optional($quote->created_at)->diffForHumans(),
                    ];
                })->values();

            $favoriteReviews = $user->favoriteReviews()
                ->with('book')
                ->whereNull('parent_id')
                ->orderByDesc('favorite_reviews.created_at')
                ->limit(20)
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'book_slug' => optional($review->book)->slug,
                        'book_title' => optional($review->book)->title,
                        'content' => mb_substr(strip_tags((string) $review->content), 0, 220),
                        'rating' => $review->rating,
                        'created_at_human' => optional($review->created_at)->diffForHumans(),
                    ];
                })->values();

            $draftReviews = $user->reviews()
                ->with('book')
                ->whereNull('parent_id')
                ->where('is_draft', true)
                ->orderByDesc('updated_at')
                ->limit(20)
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'book_slug' => optional($review->book)->slug,
                        'book_title' => optional($review->book)->title,
                        'content' => mb_substr(strip_tags((string) $review->content), 0, 200),
                        'updated_at_human' => optional($review->updated_at)->diffForHumans(),
                    ];
                })->values();

            $draftQuotes = $user->quotes()
                ->with('book')
                ->where('is_draft', true)
                ->orderByDesc('updated_at')
                ->limit(20)
                ->get()
                ->map(function ($quote) {
                    return [
                        'id' => $quote->id,
                        'book_slug' => optional($quote->book)->slug,
                        'book_title' => optional($quote->book)->title,
                        'content' => mb_substr((string) $quote->content, 0, 200),
                        'updated_at_human' => optional($quote->updated_at)->diffForHumans(),
                    ];
                })->values();

            $draftDiscussions = $user->discussions()
                ->where('status', 'draft')
                ->orderByDesc('updated_at')
                ->limit(20)
                ->get()
                ->map(function ($discussion) {
                    return [
                        'id' => $discussion->id,
                        'slug' => $discussion->slug,
                        'title' => $discussion->title,
                        'content' => mb_substr(strip_tags((string) $discussion->content), 0, 200),
                        'updated_at_human' => optional($discussion->updated_at)->diffForHumans(),
                    ];
                })->values();
        }

        return response()->json([
            'profile' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $isOwner ? $user->email : null,
                'avatar' => $user->avatar_display,
                'bio' => $user->bio,
                'header' => [
                    'title' => $user->profile_header_title,
                    'subtitle' => $user->profile_header_subtitle,
                    'image' => $user->profile_header_image,
                ],
                'theme' => [
                    'accent' => $user->profile_accent_color ?: '#7c3aed',
                    'secondary' => $user->profile_secondary_color ?: '#2563eb',
                    'frame' => $user->profile_frame_style ?: 'default',
                    'card' => $user->profile_card_style ?: 'glass',
                ],
                'settings' => $isOwner ? [
                    'email_notifications' => (bool) $user->email_notifications,
                    'new_books_notifications' => (bool) $user->new_books_notifications,
                    'comments_notifications' => (bool) $user->comments_notifications,
                    'public_profile' => (bool) $user->public_profile,
                    'show_reading_stats' => (bool) $user->show_reading_stats,
                    'show_ratings' => (bool) $user->show_ratings,
                ] : null,
            ],
            'dashboard' => [
                'stats' => [
                    'read_count' => $readCount,
                    'reading_count' => $readingCount,
                    'planned_count' => $plannedCount,
                    'dropped_count' => $droppedCount,
                    'reviews_count' => $user->reviews()->whereNull('parent_id')->where('is_draft', false)->count(),
                    'discussions_count' => $user->discussions()->whereIn('status', ['active', 'blocked'])->count(),
                    'quotes_count' => $user->quotes()->where('is_public', true)->count(),
                    'collections_count' => $user->libraries()->when(!$isOwner, function ($q) {
                        $q->where('is_private', false);
                    })->count(),
                    'favorite_quotes_count' => $isOwner ? $user->favoriteQuotes()->count() : 0,
                    'favorite_reviews_count' => $isOwner ? $user->favoriteReviews()->count() : 0,
                ],
                'recent_read_books' => $recentReadBooks,
                'recent_reviews' => $recentReviews,
                'recent_discussions' => $recentDiscussions,
                'recent_quotes' => $recentQuotes,
                'collections' => $collections,
                'favorite_quotes' => $favoriteQuotes,
                'favorite_reviews' => $favoriteReviews,
                'draft_reviews' => $draftReviews,
                'draft_quotes' => $draftQuotes,
                'draft_discussions' => $draftDiscussions,
            ],
            'isOwner' => (bool) $isOwner,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Профіль оновлено.',
            'profile' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar_display,
                'bio' => $user->bio,
            ],
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'avatar' => [
                'required',
                File::image()->max(2048)->types(['jpeg', 'png', 'gif', 'webp']),
            ],
        ]);

        $file = $request->file('avatar');
        $avatarUrl = CDNUploader::uploadFile($file, 'avatars');

        if (!$avatarUrl) {
            return response()->json([
                'message' => 'Не вдалося завантажити аватар.',
            ], 422);
        }

        if ($user->avatar) {
            try {
                CDNUploader::deleteFromBunnyCDN($user->avatar);
            } catch (\Throwable $e) {
                // ignore avatar cleanup errors
            }
        }

        $user->update(['avatar' => $avatarUrl]);

        return response()->json([
            'message' => 'Аватар оновлено.',
            'avatar' => $user->avatar_display,
        ]);
    }

    public function updateDesign(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'header_title' => ['nullable', 'string', 'max:255'],
            'header_subtitle' => ['nullable', 'string', 'max:255'],
            'header_image' => ['nullable', 'string', 'max:2048'],
            'accent_color' => ['nullable', 'string', 'max:32'],
            'secondary_color' => ['nullable', 'string', 'max:32'],
            'frame_style' => ['nullable', 'string', 'max:32'],
            'card_style' => ['nullable', 'string', 'max:32'],
        ]);

        $user->update([
            'profile_header_title' => $validated['header_title'] ?? null,
            'profile_header_subtitle' => $validated['header_subtitle'] ?? null,
            'profile_header_image' => $validated['header_image'] ?? null,
            'profile_accent_color' => $validated['accent_color'] ?? null,
            'profile_secondary_color' => $validated['secondary_color'] ?? null,
            'profile_frame_style' => $validated['frame_style'] ?? null,
            'profile_card_style' => $validated['card_style'] ?? null,
        ]);

        return response()->json([
            'message' => 'Оформлення оновлено.',
            'profile' => [
                'header' => [
                    'title' => $user->profile_header_title,
                    'subtitle' => $user->profile_header_subtitle,
                    'image' => $user->profile_header_image,
                ],
                'theme' => [
                    'accent' => $user->profile_accent_color ?: '#7c3aed',
                    'secondary' => $user->profile_secondary_color ?: '#2563eb',
                    'frame' => $user->profile_frame_style ?: 'default',
                    'card' => $user->profile_card_style ?: 'glass',
                ],
            ],
        ]);
    }

    public function updateHeaderImage(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'header_image' => [
                'required',
                File::image()->max(4096)->types(['jpeg', 'png', 'jpg', 'gif', 'webp']),
            ],
        ]);

        $file = $request->file('header_image');
        $url = CDNUploader::uploadFile($file, 'profile_headers');

        if (!$url) {
            return response()->json([
                'message' => 'Не вдалося завантажити шапку профілю.',
            ], 422);
        }

        if ($user->profile_header_image) {
            try {
                CDNUploader::deleteFromBunnyCDN($user->profile_header_image);
            } catch (\Throwable $e) {
                // ignore cleanup
            }
        }

        $user->update(['profile_header_image' => $url]);

        return response()->json([
            'message' => 'Шапку профілю оновлено.',
            'profile' => [
                'header' => [
                    'image' => $user->profile_header_image,
                ],
            ],
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['message' => 'Невірний поточний пароль.'], 422);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return response()->json(['message' => 'Пароль оновлено.']);
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'email_notifications' => ['nullable', 'boolean'],
            'new_books_notifications' => ['nullable', 'boolean'],
            'comments_notifications' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'email_notifications' => (bool) ($validated['email_notifications'] ?? false),
            'new_books_notifications' => (bool) ($validated['new_books_notifications'] ?? false),
            'comments_notifications' => (bool) ($validated['comments_notifications'] ?? false),
        ]);

        return response()->json([
            'message' => 'Налаштування сповіщень оновлено.',
            'settings' => [
                'email_notifications' => (bool) $user->email_notifications,
                'new_books_notifications' => (bool) $user->new_books_notifications,
                'comments_notifications' => (bool) $user->comments_notifications,
            ],
        ]);
    }

    public function updatePrivacy(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'public_profile' => ['nullable', 'boolean'],
            'show_reading_stats' => ['nullable', 'boolean'],
            'show_ratings' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'public_profile' => (bool) ($validated['public_profile'] ?? false),
            'show_reading_stats' => (bool) ($validated['show_reading_stats'] ?? false),
            'show_ratings' => (bool) ($validated['show_ratings'] ?? false),
        ]);

        return response()->json([
            'message' => 'Налаштування приватності оновлено.',
            'settings' => [
                'public_profile' => (bool) $user->public_profile,
                'show_reading_stats' => (bool) $user->show_reading_stats,
                'show_ratings' => (bool) $user->show_ratings,
            ],
        ]);
    }

    public function exportData()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'message' => 'Експорт підготовлено.',
            'export' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'bio' => $user->bio,
                ],
                'stats' => [
                    'books_read' => $user->readingStatuses()->where('status', 'read')->count(),
                    'reviews' => $user->reviews()->whereNull('parent_id')->count(),
                    'quotes' => $user->quotes()->count(),
                    'discussions' => $user->discussions()->count(),
                    'collections' => $user->libraries()->count(),
                ],
            ],
        ]);
    }

    public function destroyAvatar()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->avatar) {
            try {
                CDNUploader::deleteFromBunnyCDN($user->avatar);
            } catch (\Throwable $e) {
                // ignore avatar cleanup errors
            }
        }

        $user->update(['avatar' => null]);

        return response()->json([
            'message' => 'Аватар видалено.',
            'avatar' => $user->avatar_display,
        ]);
    }
}

