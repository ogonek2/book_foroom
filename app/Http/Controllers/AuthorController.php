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
        
        // Загружаем книги с категориями
        $books = $author->books()->with('categories')->paginate(12);
        
        // Получаем библиотеки текущего пользователя
        $userLibraries = [];
        if (auth()->check()) {
            $userLibraries = auth()->user()->libraries()->with('books')->get();
        }
        
        // Получаем все книги автора для статистики
        $allBooks = $author->books()->with([
            'categories',
            'reviews.user',
            'reviews.book',
            'quotes.user',
            'quotes.book',
            'facts.user',
            'facts.book'
        ])->withCount('reviews')->get();
        
        // Статистика из книг автора
        $stats = [
            'total_books' => $allBooks->count(),
            'total_reviews' => $allBooks->sum(function($book) {
                // Считаем только главные рецензии (не ответы)
                return $book->reviews->where('parent_id', null)->count();
            }),
            'total_quotes' => $allBooks->sum(function($book) {
                return $book->quotes->count();
            }),
            'total_facts' => $allBooks->sum(function($book) {
                return $book->facts->count();
            }),
            'average_rating' => $allBooks->avg('display_rating') ?? 0,
        ];
        
        // Получаем цитаты из всех книг автора (последние 4)
        $quotes = collect();
        foreach ($allBooks as $book) {
            $quotes = $quotes->merge($book->quotes->take(2)); // Берем по 2 цитаты с каждой книги
        }
        $quotes = $quotes->sortByDesc('created_at')->take(4);
        
        // Получаем факты из всех книг автора (последние 4)
        $facts = collect();
        foreach ($allBooks as $book) {
            $facts = $facts->merge($book->facts->take(2)); // Берем по 2 факта с каждой книги
        }
        $facts = $facts->sortByDesc('created_at')->take(4);
        
        // Получаем рецензии из всех книг автора (последние 6)
        // Только главные рецензии (не ответы), у которых есть оценка
        $reviews = collect();
        foreach ($allBooks as $book) {
            // Загружаем только главные рецензии (parent_id = null) с оценкой
            $bookReviews = $book->reviews()
                ->whereNull('parent_id') // Только главные рецензии, не ответы
                ->whereNotNull('rating') // Только те, у которых есть оценка
                ->with(['user', 'book'])
                ->withCount(['likes', 'replies'])
                ->take(2)
                ->get();
            $reviews = $reviews->merge($bookReviews);
        }
        $reviews = $reviews->sortByDesc('created_at')->take(6);
        
        return view('authors.show', compact('author', 'books', 'stats', 'quotes', 'facts', 'reviews', 'userLibraries'));
    }
}
