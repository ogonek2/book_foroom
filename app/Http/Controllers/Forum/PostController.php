<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PostController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Topic $topic)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:posts,id',
        ]);

        $post = $topic->posts()->create([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
        ]);

        // Update topic's last activity and replies count
        $topic->updateLastActivity();
        $topic->increment('replies_count');

        return redirect()->route('forum.topics.show', $topic)
            ->with('success', 'Сообщение успешно добавлено!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return Inertia::render('Forum/Posts/Edit', [
            'post' => $post,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'content' => 'required|string',
        ]);

        $post->update($request->only(['content']));

        return redirect()->route('forum.topics.show', $post->topic)
            ->with('success', 'Сообщение успешно обновлено!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $topic = $post->topic;
        $post->delete();

        // Update topic's replies count
        $topic->decrement('replies_count');

        return redirect()->route('forum.topics.show', $topic)
            ->with('success', 'Сообщение успешно удалено!');
    }

    /**
     * Mark post as solution
     */
    public function markAsSolution(Post $post)
    {
        $this->authorize('markAsSolution', $post);

        $post->markAsSolution();

        return redirect()->route('forum.topics.show', $post->topic)
            ->with('success', 'Сообщение отмечено как решение!');
    }
}
