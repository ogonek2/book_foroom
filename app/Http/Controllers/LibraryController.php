<?php

namespace App\Http\Controllers;

use App\Models\Library;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LibraryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $libraries = $user->libraries()->withCount('books')->orderBy('created_at', 'desc')->get();
        
        return view('libraries.index', compact('libraries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('libraries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_private' => 'boolean',
            'book_id' => 'nullable|exists:books,id', // Добавляем возможность сразу добавить книгу
        ]);

        $library = Auth::user()->libraries()->create([
            'name' => $request->name,
            'description' => $request->description,
            'is_private' => $request->boolean('is_private'),
        ]);

        // Если передан book_id, добавляем книгу в новую библиотеку
        if ($request->book_id) {
            $book = Book::findOrFail($request->book_id);
            $library->books()->attach($book->id);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Библиотека успешно создана!' . ($request->book_id ? ' Книга добавлена.' : ''),
            'library' => $library
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Library $library)
    {
        // Проверяем права доступа
        if (!$library->canBeViewedBy(Auth::user())) {
            abort(403, 'У вас нет доступа к этой библиотеке');
        }

        $books = $library->books()->with(['author', 'category'])->paginate(12);
        
        return view('libraries.show', compact('library', 'books'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Library $library)
    {
        // Проверяем права на редактирование
        if (!$library->canBeEditedBy(Auth::user())) {
            abort(403, 'У вас нет прав на редактирование этой библиотеки');
        }

        return view('libraries.edit', compact('library'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Library $library)
    {
        // Проверяем права на редактирование
        if (!$library->canBeEditedBy(Auth::user())) {
            abort(403, 'У вас нет прав на редактирование этой библиотеки');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_private' => 'boolean',
        ]);

        $library->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_private' => $request->boolean('is_private'),
        ]);

        return redirect()->route('libraries.show', $library)
                        ->with('success', 'Библиотека успешно обновлена!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Library $library)
    {
        // Проверяем права на удаление
        if (!$library->canBeEditedBy(Auth::user())) {
            abort(403, 'У вас нет прав на удаление этой библиотеки');
        }

        $library->delete();

        return redirect()->route('libraries.index')
                        ->with('success', 'Библиотека успешно удалена!');
    }

    /**
     * Добавить книгу в библиотеку
     */
    public function addBook(Request $request, Library $library)
    {
        // Проверяем права на редактирование
        if (!$library->canBeEditedBy(Auth::user())) {
            return response()->json(['success' => false, 'message' => 'У вас нет прав на редактирование этой библиотеки'], 403);
        }

        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($request->book_id);

        // Проверяем, не добавлена ли уже книга
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
     * Удалить книгу из библиотеки
     */
    public function removeBook(Library $library, Book $book)
    {
        // Проверяем права на редактирование
        if (!$library->canBeEditedBy(Auth::user())) {
            abort(403, 'У вас нет прав на редактирование этой библиотеки');
        }

        $library->books()->detach($book->id);

        return back()->with('success', 'Книга "' . $book->title . '" удалена из библиотеки');
    }

    /**
     * Показать публичные библиотеки пользователя
     */
    public function publicLibraries($username)
    {
        $user = \App\Models\User::where('username', $username)->firstOrFail();
        $libraries = $user->publicLibraries()->withCount('books')->orderBy('created_at', 'desc')->get();
        
        return view('libraries.public', compact('user', 'libraries'));
    }
}
