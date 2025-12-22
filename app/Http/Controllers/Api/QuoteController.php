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
            ->limit($limit * 2) // Берем больше, чтобы после фильтрации осталось достаточно
            ->get()
            ->map(function (Quote $quote) {
                // Обрезаем контент до 80-120 символов
                $content = strip_tags($quote->content);
                $contentLength = mb_strlen($content);
                
                if ($contentLength < 80) {
                    // Если меньше 80 символов, пропускаем
                    return null;
                }
                
                if ($contentLength > 120) {
                    // Обрезаем до 120 символов и добавляем троеточие
                    $content = mb_substr($content, 0, 120);
                    // Убираем последнее слово, если оно обрезано
                    $lastSpace = mb_strrpos($content, ' ');
                    if ($lastSpace !== false && $lastSpace > 100) {
                        $content = mb_substr($content, 0, $lastSpace);
                    }
                    $content .= '...';
                }
                
                return [
                    'id' => $quote->id,
                    'content' => $content,
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
            })
            ->filter(function ($quote) {
                // Убираем null значения (цитаты короче 80 символов)
                return $quote !== null;
            })
            ->values() // Переиндексируем массив
            ->take($limit); // Берем только нужное количество

        return response()->json([
            'data' => $quotes,
        ]);
    }
}
