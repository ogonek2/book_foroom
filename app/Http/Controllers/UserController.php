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
    public function index()
    {
        $users = User::withCount([
            'quotes as public_quotes_count' => function($query) {
                $query->where('is_public', true);
            },
            'publications as published_publications_count' => function($query) {
                $query->where('status', 'published');
            },
            'reviews as main_reviews_count' => function($query) {
                $query->whereNull('parent_id');
            }
        ])
        ->orderBy('rating', 'desc')
        ->paginate(12);

        return view('users.index', compact('users'));
    }
}
