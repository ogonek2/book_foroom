<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{

    /**
     * Toggle like for a post or topic
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'likeable_type' => 'required|string|in:App\Models\Post,App\Models\Topic',
            'likeable_id' => 'required|integer',
        ]);

        $likeable = $request->likeable_type::findOrFail($request->likeable_id);

        $like = Like::where('user_id', Auth::id())
            ->where('likeable_type', $request->likeable_type)
            ->where('likeable_id', $request->likeable_id)
            ->first();

        if ($like) {
            // Unlike
            $like->delete();
            $likeable->decrement('likes_count');
            $action = 'unliked';
        } else {
            // Like
            Like::create([
                'user_id' => Auth::id(),
                'likeable_type' => $request->likeable_type,
                'likeable_id' => $request->likeable_id,
            ]);
            $likeable->increment('likes_count');
            $action = 'liked';
        }

        return response()->json([
            'action' => $action,
            'likes_count' => $likeable->fresh()->likes_count,
        ]);
    }
}
