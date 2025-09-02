<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TopicController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topics = Topic::with(['category', 'user'])
            ->withCount('posts')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('last_activity_at', 'desc')
            ->paginate(20);

        return view('forum.topics.index', compact('topics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Category $category)
    {
        return view('forum.topics.create', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Category $category)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $topic = $category->topics()->create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('forum.topics.show', $topic)
            ->with('success', 'Тема успешно создана!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic)
    {
        $topic->incrementViews();

        $posts = $topic->posts()
            ->with(['user', 'replies.user', 'likes'])
            ->withCount('likes')
            ->orderBy('created_at')
            ->paginate(10);

        return view('forum.topics.show', [
            'topic' => $topic->load(['category', 'user']),
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Topic $topic)
    {
        $this->authorize('update', $topic);

        return view('forum.topics.edit', compact('topic'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Topic $topic)
    {
        $this->authorize('update', $topic);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $topic->update($request->only(['title', 'content']));

        return redirect()->route('forum.topics.show', $topic)
            ->with('success', 'Тема успешно обновлена!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic)
    {
        $this->authorize('delete', $topic);

        $category = $topic->category;
        $topic->delete();

        return redirect()->route('forum.categories.show', $category)
            ->with('success', 'Тема успешно удалена!');
    }
}
