<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $query = Author::withCount('books');

        // Поиск
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Фильтр по букве
        if ($request->filled('letter')) {
            $query->byLetter($request->letter);
        }

        // Фильтр по национальности
        if ($request->filled('nationality')) {
            $query->byNationality($request->nationality);
        }

        // Сортировка
        $sortBy = $request->get('sort', 'last_name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortBy, ['last_name', 'first_name', 'birth_date', 'books_count'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $authors = $query->paginate(12);

        // Получаем уникальные национальности для фильтра
        $nationalities = Author::select('nationality')
            ->whereNotNull('nationality')
            ->distinct()
            ->orderBy('nationality')
            ->pluck('nationality');

        // Получаем буквы для алфавитного указателя
        $letters = Author::selectRaw('UPPER(SUBSTRING(last_name, 1, 1)) as letter')
            ->distinct()
            ->orderBy('letter')
            ->pluck('letter');

        return view('authors.index', compact('authors', 'nationalities', 'letters'));
    }

    public function show(Author $author)
    {
        $author->loadCount('books');
        $books = $author->books()->with('category')->paginate(12);
        
        return view('authors.show', compact('author', 'books'));
    }
}
