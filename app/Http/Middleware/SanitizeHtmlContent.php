<?php

namespace App\Http\Middleware;

use App\Helpers\HtmlSanitizer;
use Closure;
use Illuminate\Http\Request;

class SanitizeHtmlContent
{
    /**
     * Handle an incoming request.
     * Санитизирует HTML контент в полях 'content' перед обработкой
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Санитизируем поле 'content' если оно присутствует
        if ($request->has('content')) {
            $sanitizedContent = HtmlSanitizer::sanitize($request->input('content'));
            $request->merge(['content' => $sanitizedContent]);
        }

        return $next($request);
    }
}
