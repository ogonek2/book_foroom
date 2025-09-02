<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->withCount('topics')
            ->orderBy('sort_order')
            ->get();

        return view('forum.categories.index', compact('categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $topics = $category->topics()
            ->with(['user', 'posts' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->withCount('posts')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('last_activity_at', 'desc')
            ->paginate(20);

        return view('forum.categories.show', compact('category', 'topics'));
    }
}
