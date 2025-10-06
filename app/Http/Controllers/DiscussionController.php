<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\DiscussionReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DiscussionController extends Controller
{
    /**
     * Display a listing of discussions
     */
    public function index(Request $request)
    {
        $query = Discussion::with(['user', 'replies.user'])
            ->withCount(['replies', 'likes'])
            ->where('status', 'active')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('last_activity_at', 'desc');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            switch ($request->status) {
                case 'open':
                    $query->where('is_closed', false);
                    break;
                case 'closed':
                    $query->where('is_closed', true);
                    break;
                case 'pinned':
                    $query->where('is_pinned', true);
                    break;
            }
        }

        $discussions = $query->paginate(15);

        return view('discussions.index', compact('discussions'));
    }

    /**
     * Show the form for creating a new discussion
     */
    public function create()
    {
        return view('discussions.create');
    }

    /**
     * Store a newly created discussion
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $discussion = Discussion::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
            'status' => 'active',
        ]);

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Обсуждение успешно создано!');
    }

    /**
     * Display the specified discussion
     */
    public function show(Discussion $discussion)
    {
        $discussion->incrementViews();

        // Load discussion with user and their stats
        $discussion->load(['user' => function($query) {
            $query->withCount([
                'discussions as active_discussions_count' => function($query) {
                    $query->where('status', 'active');
                },
                'discussionReplies as active_replies_count' => function($query) {
                    $query->where('status', 'active');
                },
                'reviews as active_reviews_count' => function($query) {
                    $query->where('status', 'active');
                }
            ]);
        }]);

        $replies = $discussion->replies()
            ->with(['user', 'replies.user'])
            ->withCount('likes')
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Ensure replies are sorted by newest first
        $replies = $replies->sortByDesc('created_at');
        
        return view('discussions.show', compact('discussion', 'replies'));
    }

    /**
     * Show the form for editing the discussion
     */
    public function edit(Discussion $discussion)
    {
        if (!$discussion->canBeEditedBy(Auth::user())) {
            abort(403, 'У вас нет прав для редактирования этого обсуждения');
        }

        return view('discussions.edit', compact('discussion'));
    }

    /**
     * Update the specified discussion
     */
    public function update(Request $request, Discussion $discussion)
    {
        if (!$discussion->canBeEditedBy(Auth::user())) {
            abort(403, 'У вас нет прав для редактирования этого обсуждения');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $discussion->update($request->only(['title', 'content']));

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Обсуждение успешно обновлено!');
    }

    /**
     * Close the discussion
     */
    public function close(Discussion $discussion)
    {
        if (!$discussion->canBeClosedBy(Auth::user())) {
            abort(403, 'У вас нет прав для закрытия этого обсуждения');
        }

        $discussion->update(['is_closed' => true]);

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Обсуждение закрыто!');
    }

    /**
     * Reopen the discussion
     */
    public function reopen(Discussion $discussion)
    {
        if (!$discussion->canBeClosedBy(Auth::user())) {
            abort(403, 'У вас нет прав для открытия этого обсуждения');
        }

        $discussion->update(['is_closed' => false]);

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Обсуждение открыто!');
    }

    /**
     * Remove the specified discussion
     */
    public function destroy(Discussion $discussion)
    {
        if (!$discussion->canBeEditedBy(Auth::user())) {
            abort(403, 'У вас нет прав для удаления этого обсуждения');
        }

        $discussion->delete();

        return redirect()->route('discussions.index')
            ->with('success', 'Обсуждение удалено!');
    }

    /**
     * Store a new reply to discussion
     */
    public function storeReply(Request $request, Discussion $discussion)
    {
        if (!$discussion->canBeRepliedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Обсуждение закрыто для новых ответов'
            ], 403);
        }

        $request->validate([
            'content' => 'required|string|min:1',
            'parent_id' => 'nullable|exists:discussion_replies,id',
        ]);

        $reply = DiscussionReply::create([
            'content' => $request->content,
            'discussion_id' => $discussion->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'status' => 'active',
        ]);

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ответ добавлен!',
                'reply' => $reply->load('user')
            ]);
        }

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Ответ добавлен!');
    }

    /**
     * Update a reply
     */
    public function updateReply(Request $request, Discussion $discussion, DiscussionReply $reply)
    {
        if ($reply->user_id !== Auth::id() && !Auth::user()->isModerator()) {
            abort(403, 'У вас нет прав для редактирования этого ответа');
        }

        $request->validate([
            'content' => 'required|string|min:1',
        ]);

        $reply->update($request->only(['content']));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ответ обновлен!',
                'reply' => $reply->load('user')
            ]);
        }

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Ответ обновлен!');
    }

    /**
     * Delete a reply
     */
    public function destroyReply(Discussion $discussion, DiscussionReply $reply)
    {
        if ($reply->user_id !== Auth::id() && !Auth::user()->isModerator()) {
            abort(403, 'У вас нет прав для удаления этого ответа');
        }

        $reply->delete();

        if (request()->expectsJson() || request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ответ удален!'
            ]);
        }

        return redirect()->route('discussions.show', $discussion)
            ->with('success', 'Ответ удален!');
    }
}
