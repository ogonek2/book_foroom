<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function featured(Request $request)
    {
        $limit = (int) $request->get('limit', 10);
        $limit = max(1, min($limit, 20));

        $quotes = Quote::query()
            ->where('is_public', true)
            ->with(['user', 'book'])
            ->withCount('likes')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (Quote $quote) {
                return [
                    'id' => $quote->id,
                    'content' => $quote->content,
                    'page_number' => $quote->page_number,
                    'created_at' => $quote->created_at?->toIso8601String(),
                    'likes_count' => $quote->likes_count ?? 0,
                    'user' => $quote->user ? [
                        'id' => $quote->user->id,
                        'name' => $quote->user->name,
                        'username' => $quote->user->username,
                        'avatar' => $quote->user->avatar_display,
                    ] : null,
                    'book' => $quote->book ? [
                        'title' => $quote->book->title,
                        'slug' => $quote->book->slug,
                    ] : null,
                ];
            });

        return response()->json([
            'data' => $quotes,
        ]);
    }
}
