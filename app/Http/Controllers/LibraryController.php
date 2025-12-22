<?php

namespace App\Http\Controllers;

use App\Models\Library;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class LibraryController extends Controller
{
    /**
     * Display a listing of libraries
     */
    public function index(Request $request)
    {
        $query = $this->buildLibrariesQuery($request);

        $libraries = $query->paginate($request->integer('per_page', 12));

        if ($request->wantsJson()) {
            return response()->json($this->transformPaginatedLibraries($libraries));
        }

        $payload = $this->transformPaginatedLibraries($libraries);

        $filters = [
            'search' => (string) $request->get('search', ''),
            'category' => (string) $request->get('category', ''),
            'sort' => (string) $request->get('sort', 'popular'),
            'page' => $libraries->currentPage(),
        ];

        return view('libraries.index', [
            'initialData' => $payload,
            'initialFilters' => $filters,
            'isAuthenticated' => Auth::check(),
            'createUrl' => route('libraries.create'),
        ]);
    }

    /**
     * Show the form for creating a new library
     */
    public function create()
    {
        return view('libraries.create');
    }

    /**
     * Store a newly created library
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_private' => 'boolean',
        ]);

        $library = Library::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_private' => $request->boolean('is_private', false),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('libraries.show', $library)
            ->with('success', 'Добірку створено успішно!');
    }

    /**
     * Display the specified library
     */
    public function show(Library $library)
    {
        // Check if user can view this library
        if (!$library->canBeViewedBy(Auth::user())) {
            abort(403, 'Ця добірка є приватною');
        }

        // Load books with pagination
        $books = $library->books()->paginate(25);

        // Check if current user has saved this library
        $isSaved = false;
        if (Auth::check()) {
            $isSaved = DB::table('saved_libraries')
                ->where('user_id', Auth::id())
                ->where('library_id', $library->id)
                ->exists();
        }

        return view('libraries.show', compact('library', 'books', 'isSaved'));
    }

    /**
     * Show the form for editing the specified library
     */
    public function edit(Library $library)
    {
        if (!$library->canBeEditedBy(Auth::user())) {
            abort(403);
        }

        return view('libraries.edit', compact('library'));
    }

    /**
     * Update the specified library
     */
    public function update(Request $request, Library $library)
    {
        if (!$library->canBeEditedBy(Auth::user())) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_private' => 'boolean',
        ]);

        $library->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_private' => $request->boolean('is_private', false),
        ]);

        return redirect()->route('libraries.show', $library)
            ->with('success', 'Добірку оновлено успішно!');
    }

    /**
     * Remove the specified library
     */
    public function destroy(Library $library)
    {
        if (!$library->canBeEditedBy(Auth::user())) {
            return response()->json(['success' => false, 'message' => 'У вас нет прав на редактирование этой библиотеки'], 403);
        }

        $library->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Добірку видалено успішно!'
            ]);
        }

        return redirect()->route('libraries.index')
            ->with('success', 'Добірку видалено успішно!');
    }

    /**
     * Add a book to the library
     */
    public function addBook(Request $request, Library $library)
    {
        if (!$library->canBeEditedBy(Auth::user())) {
            return response()->json(['success' => false, 'message' => 'У вас нет прав на редактирование этой библиотеки'], 403);
        }

        $request->validate([
            'book_slug' => 'required|exists:books,slug',
        ]);

        $book = Book::where('slug', $request->book_slug)->firstOrFail();

        if ($library->books()->where('book_id', $book->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Эта книга уже добавлена в библиотеку']);
        }

        $library->books()->attach($book->id);

        return response()->json([
            'success' => true,
            'message' => 'Книга "' . $book->title . '" добавлена в библиотеку',
            'library_name' => $library->name,
            'library_id' => $library->id
        ]);
    }

    /**
     * Remove a book from the library
     */
    public function removeBook(Request $request, Library $library, Book $book)
    {
        if (!$library->canBeEditedBy(Auth::user())) {
            return response()->json(['success' => false, 'message' => 'У вас нет прав на редактирование этой библиотеки'], 403);
        }

        $library->books()->detach($book->id);

        return response()->json([
            'success' => true,
            'message' => 'Книга "' . $book->title . '" удалена из библиотеки'
        ]);
    }

    /**
     * Save/unsave library for user
     */
    public function toggleSave(Request $request, Library $library)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Необходима авторизация'], 401);
        }

        $isSaved = DB::table('saved_libraries')
            ->where('user_id', Auth::id())
            ->where('library_id', $library->id)
            ->exists();

        if ($isSaved) {
            DB::table('saved_libraries')
                ->where('user_id', Auth::id())
                ->where('library_id', $library->id)
                ->delete();
            
            $message = 'Добірку видалено зі збережених';
        } else {
            DB::table('saved_libraries')->insert([
                'user_id' => Auth::id(),
                'library_id' => $library->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $message = 'Добірку збережено';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_saved' => !$isSaved
        ]);
    }

    /**
     * Toggle like for library
     */
    public function toggleLike(Request $request, Library $library)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Необходима авторизация'], 401);
        }

        $isLiked = DB::table('user_liked_libraries')
            ->where('user_id', Auth::id())
            ->where('library_id', $library->id)
            ->exists();

        if ($isLiked) {
            DB::table('user_liked_libraries')
                ->where('user_id', Auth::id())
                ->where('library_id', $library->id)
                ->delete();
            
            $message = 'Лайк видалено';
        } else {
            DB::table('user_liked_libraries')->insert([
                'user_id' => Auth::id(),
                'library_id' => $library->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $message = 'Добірка сподобалась';
        }

        // Get updated likes count
        $likesCount = DB::table('user_liked_libraries')
            ->where('library_id', $library->id)
            ->count();

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_liked' => !$isLiked,
            'likes_count' => $likesCount
        ]);
    }

    /**
     * Remove library from user's saved libraries
     */
    public function unsave(Library $library)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Потрібна авторизація'], 401);
        }

        $user = Auth::user();
        
        // Check if library is saved by user
        $isSaved = $user->savedLibraries()->where('library_id', $library->id)->exists();
        
        if (!$isSaved) {
            return response()->json(['success' => false, 'message' => 'Добірка не збережена'], 400);
        }

        // Remove from saved libraries
        $user->savedLibraries()->detach($library->id);

        return response()->json([
            'success' => true,
            'message' => 'Добірку прибрано з збережених'
        ]);
    }

    /**
     * Build the base query for listing libraries with applied filters.
     */
    protected function buildLibrariesQuery(Request $request): Builder
    {
        $query = Library::query()
            ->with([
                'user:id,name,username,avatar',
                'books' => function ($q) {
                    $q->limit(3);
                },
            ])
            ->withCount(['books', 'likes'])
            ->whereHas('books');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function (Builder $builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!Auth::check() || !$request->has('show_private')) {
            $query->where('is_private', false);
        } else {
            $query->where(function (Builder $builder) {
                $builder->where('is_private', false)
                    ->orWhere(function (Builder $sub) {
                        $sub->where('is_private', true)
                            ->where('user_id', Auth::id());
                    });
            });
        }

        $sort = $request->get('sort', 'popular');
        switch ($sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'books_count':
                $query->orderBy('books_count', 'desc');
                break;
            default:
                $query->orderBy('likes_count', 'desc')
                      ->orderBy('books_count', 'desc');
        }

        return $query;
    }

    /**
     * Transform paginated libraries to array structure for API/initial payload.
     */
    protected function transformPaginatedLibraries(LengthAwarePaginator $libraries): array
    {
        $collection = $libraries->getCollection();
        $libraryIds = $collection->pluck('id');

        $user = Auth::user();
        $likedIds = [];
        $savedIds = [];

        if ($user && $libraryIds->isNotEmpty()) {
            $likedIds = DB::table('user_liked_libraries')
                ->where('user_id', $user->id)
                ->whereIn('library_id', $libraryIds)
                ->pluck('library_id')
                ->all();

            $savedIds = DB::table('saved_libraries')
                ->where('user_id', $user->id)
                ->whereIn('library_id', $libraryIds)
                ->pluck('library_id')
                ->all();
        }

        $data = $collection->map(function (Library $library) use ($likedIds, $savedIds) {
            return [
                'id' => $library->id,
                'name' => $library->name,
                'description' => $library->description,
                'created_at' => optional($library->created_at)->toIso8601String(),
                'books_count' => (int) ($library->books_count ?? $library->books->count()),
                'likes_count' => (int) ($library->likes_count ?? 0),
                'user' => [
                    'id' => $library->user->id,
                    'name' => $library->user->name,
                    'username' => $library->user->username,
                    'avatar' => $library->user->avatar,
                ],
                'books' => $library->books->map(function (Book $book) {
                    return [
                        'id' => $book->id,
                        'title' => $book->title,
                        'slug' => $book->slug,
                        'cover_image' => $book->cover_image,
                    ];
                })->values(),
                'is_liked' => in_array($library->id, $likedIds, true),
                'is_saved' => in_array($library->id, $savedIds, true),
            ];
        })->values();

        return [
            'data' => $data,
            'meta' => [
                'current_page' => $libraries->currentPage(),
                'last_page' => $libraries->lastPage(),
                'per_page' => $libraries->perPage(),
                'total' => $libraries->total(),
            ],
            'links' => [
                'prev' => $libraries->previousPageUrl(),
                'next' => $libraries->nextPageUrl(),
            ],
        ];
    }
}