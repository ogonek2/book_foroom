<?php

namespace App\Http\Controllers;

use App\Helpers\CDNUploader;
use App\Models\Book;
use App\Models\BookReadingStatus;
use App\Models\Library;
use App\Models\ReadingPlan;
use App\Models\ReadingPlanItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
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
        $totalReadingStatuses = $user->readingStatuses()->count();
        $ratedCount = $user->readingStatuses()->whereNotNull('rating')->count();
        $averageRating = $user->readingStatuses()->whereNotNull('rating')->avg('rating');

        $plannerItemsQuery = ReadingPlanItem::query()
            ->whereHas('plan', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        $plannerTotal = (clone $plannerItemsQuery)->count();
        $plannerDone = (clone $plannerItemsQuery)->where('is_done', true)->count();

        $recentReadBooks = $user->readingStatuses()
            ->whereIn('status', ['read', 'reading', 'want_to_read'])
            ->with(['book', 'sessions'])
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get()
            ->map(function ($status) {
                return [
                    'id' => $status->id,
                    'status' => $status->status,
                    'times_read' => (int) ($status->times_read ?? 1),
                    'reading_language' => $status->reading_language,
                    'rating' => $status->rating,
                    'updated_at_human' => optional($status->updated_at)->diffForHumans(),
                    'sessions' => $status->sessions->map(function ($session) {
                        return [
                            'id' => $session->id,
                            'read_at' => optional($session->read_at)->toDateString(),
                            'language' => $session->language,
                        ];
                    })->values(),
                    'book' => $status->book ? [
                        'id' => $status->book->id,
                        'slug' => $status->book->slug,
                        'title' => $status->book->title,
                        'cover' => $status->book->cover_image_display ?? $status->book->cover_image ?? null,
                    ] : null,
                ];
            })
            ->values();

        $readingPlans = collect();
        if ($isOwner) {
            $readingPlans = $user->readingPlans()
                ->with(['items.book'])
                ->limit(10)
                ->get()
                ->map(function ($plan) {
                    $total = $plan->items->count();
                    $done = $plan->items->where('is_done', true)->count();
                    return [
                        'id' => $plan->id,
                        'title' => $plan->title,
                        'goal' => $plan->goal,
                        'target_date' => optional($plan->target_date)->toDateString(),
                        'total_items' => $total,
                        'done_items' => $done,
                        'progress' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
                        'items' => $plan->items->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'is_done' => (bool) $item->is_done,
                                'book' => $item->book ? [
                                    'id' => $item->book->id,
                                    'slug' => $item->book->slug,
                                    'title' => $item->book->title,
                                    'cover' => $item->book->cover_image_display ?? $item->book->cover_image ?? null,
                                ] : null,
                            ];
                        })->values(),
                    ];
                })->values();
        }

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
                        'cover' => $review->book->cover_image_display ?? $review->book->cover_image ?? null,
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
                    'slug' => $discussion->slug,
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
                        'cover' => $quote->book->cover_image_display ?? $quote->book->cover_image ?? null,
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
                $covers = $library->books()
                    ->limit(3)
                    ->get()
                    ->map(function ($book) {
                        return $book->cover_image_display ?? $book->cover_image ?? null;
                    })
                    ->filter()
                    ->values();

                return [
                    'id' => $library->id,
                    'slug' => $library->slug,
                    'name' => $library->name,
                    'description' => $library->description,
                    'books_count' => $library->books_count,
                    'is_private' => (bool) $library->is_private,
                    'preview_covers' => $covers,
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
                        'book_cover' => optional($quote->book)->cover_image_display ?? optional($quote->book)->cover_image,
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
                        'book_cover' => optional($review->book)->cover_image_display ?? optional($review->book)->cover_image,
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
                    'total_books_count' => $totalReadingStatuses,
                    'rated_count' => $ratedCount,
                    'average_rating' => $averageRating ? round((float) $averageRating, 1) : null,
                    'reviews_count' => $user->reviews()->whereNull('parent_id')->where('is_draft', false)->count(),
                    'discussions_count' => $user->discussions()->whereIn('status', ['active', 'blocked'])->count(),
                    'quotes_count' => $user->quotes()->where('is_public', true)->count(),
                    'collections_count' => $user->libraries()->when(!$isOwner, function ($q) {
                        $q->where('is_private', false);
                    })->count(),
                    'favorite_quotes_count' => $isOwner ? $user->favoriteQuotes()->count() : 0,
                    'favorite_reviews_count' => $isOwner ? $user->favoriteReviews()->count() : 0,
                    'planner_total_items' => $isOwner ? $plannerTotal : 0,
                    'planner_done_items' => $isOwner ? $plannerDone : 0,
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
                'reading_plans' => $readingPlans,
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

    public function collectionBooks(int $collectionId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $library = Library::where('id', $collectionId)->where('user_id', $user->id)->firstOrFail();
        $books = $library->books()->limit(100)->get()->map(function ($book) {
            return [
                'id' => $book->id,
                'slug' => $book->slug,
                'title' => $book->title,
                'cover' => $book->cover_image_display ?? $book->cover_image ?? null,
            ];
        })->values();

        return response()->json(['books' => $books]);
    }

    public function addCollectionBook(Request $request, int $collectionId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $library = Library::where('id', $collectionId)->where('user_id', $user->id)->firstOrFail();
        $validated = $request->validate([
            'book_slug' => ['required', 'string', 'exists:books,slug'],
        ]);

        $book = Book::where('slug', $validated['book_slug'])->firstOrFail();
        if (!$library->books()->where('book_id', $book->id)->exists()) {
            $library->books()->attach($book->id);
        }

        return response()->json(['message' => 'Книгу додано до колекції.']);
    }

    public function removeCollectionBook(int $collectionId, int $bookId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $library = Library::where('id', $collectionId)->where('user_id', $user->id)->firstOrFail();
        $library->books()->detach($bookId);

        return response()->json(['message' => 'Книгу видалено з колекції.']);
    }

    public function destroyCollection(int $collectionId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $library = Library::where('id', $collectionId)->where('user_id', $user->id)->firstOrFail();
        $library->delete();

        return response()->json(['message' => 'Колекцію видалено.']);
    }

    public function searchBooks(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if ($q === '') {
            return response()->json(['books' => []]);
        }

        $books = Book::query()
            ->where('title', 'like', '%' . $q . '%')
            ->orderBy('title')
            ->limit(12)
            ->get()
            ->map(function ($book) {
                return [
                    'id' => $book->id,
                    'slug' => $book->slug,
                    'title' => $book->title,
                    'cover' => $book->cover_image_display ?? $book->cover_image ?? null,
                ];
            })->values();

        return response()->json(['books' => $books]);
    }

    public function libraries()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $libraries = $user->libraries()
            ->orderBy('name')
            ->get(['id', 'name', 'is_private']);

        return response()->json([
            'libraries' => $libraries->map(function ($library) {
                return [
                    'id' => $library->id,
                    'name' => $library->name,
                    'is_private' => (bool) $library->is_private,
                ];
            })->values(),
        ]);
    }

    public function readingStatusUpdate(Request $request, int $statusId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $status = BookReadingStatus::where('id', $statusId)->where('user_id', $user->id)->firstOrFail();
        $validated = $request->validate([
            'status' => ['required', Rule::in(['read', 'reading', 'want_to_read', 'abandoned'])],
            'times_read' => ['nullable', 'integer', 'min:1', 'max:999'],
            'reading_language' => ['nullable', 'string', 'max:10'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:10'],
            'sessions' => ['nullable', 'array'],
            'sessions.*.read_at' => ['required', 'date'],
            'sessions.*.language' => ['nullable', 'string', 'max:10'],
            'library_ids' => ['nullable', 'array'],
            'library_ids.*' => ['integer'],
        ]);

        $status->update([
            'status' => $validated['status'],
            'times_read' => $validated['times_read'] ?? $status->times_read ?? 1,
            'reading_language' => $validated['reading_language'] ?? null,
            'rating' => $validated['rating'] ?? $status->rating,
        ]);

        if (array_key_exists('sessions', $validated)) {
            $status->sessions()->delete();
            foreach (array_slice($validated['sessions'], 0, 200) as $session) {
                $status->sessions()->create([
                    'read_at' => $session['read_at'],
                    'language' => $session['language'] ?? null,
                ]);
            }

            $latest = $status->sessions()->orderByDesc('read_at')->first();
            if ($latest && $status->status === 'read') {
                $status->finished_at = Carbon::parse($latest->read_at);
                if (!$status->started_at) {
                    $status->started_at = Carbon::parse($latest->read_at);
                }
                $status->save();
            }
        }

        if (array_key_exists('library_ids', $validated)) {
            $allowedIds = $user->libraries()->whereIn('id', $validated['library_ids'])->pluck('id')->all();
            $bookId = $status->book_id;

            $userLibraries = $user->libraries()->get();
            foreach ($userLibraries as $library) {
                $exists = $library->books()->where('book_id', $bookId)->exists();
                $shouldBeAttached = in_array($library->id, $allowedIds, true);
                if ($shouldBeAttached && !$exists) {
                    $library->books()->attach($bookId);
                }
                if (!$shouldBeAttached && $exists) {
                    $library->books()->detach($bookId);
                }
            }
        }

        return response()->json([
            'message' => 'Статус книги оновлено.',
            'status' => $status->fresh(['sessions']),
        ]);
    }

    public function readingPlans()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $plans = $user->readingPlans()->with('items.book')->get();
        return response()->json([
            'plans' => $plans->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'title' => $plan->title,
                    'goal' => $plan->goal,
                    'target_date' => optional($plan->target_date)->toDateString(),
                    'items' => $plan->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'is_done' => (bool) $item->is_done,
                            'book' => $item->book ? [
                                'id' => $item->book->id,
                                'slug' => $item->book->slug,
                                'title' => $item->book->title,
                                'cover' => $item->book->cover_image_display ?? $item->book->cover_image ?? null,
                            ] : null,
                        ];
                    })->values(),
                ];
            })->values(),
        ]);
    }

    public function createReadingPlan(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'goal' => ['nullable', 'string', 'max:1000'],
            'target_date' => ['nullable', 'date'],
            'book_ids' => ['nullable', 'array'],
            'book_ids.*' => ['integer', 'exists:books,id'],
        ]);

        $plan = ReadingPlan::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'goal' => $validated['goal'] ?? null,
            'target_date' => $validated['target_date'] ?? null,
        ]);

        foreach (($validated['book_ids'] ?? []) as $bookId) {
            $plan->items()->firstOrCreate(['book_id' => $bookId]);
        }

        return response()->json(['message' => 'План читання створено.', 'plan_id' => $plan->id]);
    }

    public function createReadingPlanItem(Request $request, int $planId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $plan = ReadingPlan::where('id', $planId)->where('user_id', $user->id)->firstOrFail();
        $validated = $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
        ]);

        $item = $plan->items()->firstOrCreate(['book_id' => $validated['book_id']]);
        return response()->json(['message' => 'Книгу додано в план.', 'item_id' => $item->id]);
    }

    public function updateReadingPlanItem(Request $request, int $planId, int $itemId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $plan = ReadingPlan::where('id', $planId)->where('user_id', $user->id)->firstOrFail();
        $item = $plan->items()->where('id', $itemId)->firstOrFail();
        $validated = $request->validate([
            'is_done' => ['required', 'boolean'],
        ]);

        $item->update([
            'is_done' => (bool) $validated['is_done'],
            'completed_at' => $validated['is_done'] ? now() : null,
        ]);

        return response()->json(['message' => 'Пункт плану оновлено.']);
    }
}

