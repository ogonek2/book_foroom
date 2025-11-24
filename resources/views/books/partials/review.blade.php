<div class="review-item rounded-2xl bg-white dark:bg-slate-800 shadow-lg border border-slate-200 dark:border-slate-700 p-6 mb-6 hover:shadow-xl transition-all duration-300 cursor-pointer" 
     onclick="window.location.href='{{ route('books.reviews.show', [$book, $review]) }}'" 
     data-review-id="{{ $review->id }}">
    <!-- Review Header -->
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
        <div class="flex items-center space-x-4">
            @include('partials.user-mini-header', [
                'user' => $review->isGuest() ? null : $review->user,
                'timestamp' => $review->created_at->diffForHumans(),
                'showGuest' => $review->isGuest()
            ])
            </div>
            
            <!-- Review Meta Info -->
            @if (!$review->isReply() && ($review->review_type || $review->book_type || $review->language))
            <div class="flex items-center flex-wrap gap-2 py-2">
                @if ($review->review_type)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium text-white border
                        {{ $review->review_type === 'review' ? 'bg-blue-500/60 dark:bg-blue-600/60 border-blue-400 dark:border-blue-500' : 'bg-purple-500/60 dark:bg-purple-600/60 border-purple-400 dark:border-purple-500' }}">
                        {{ $review->review_type === 'review' ? 'Рецензія' : 'Відгук' }}
                    </span>
                @endif
                @if ($review->book_type)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-indigo-500/60 dark:bg-indigo-600/60 border border-indigo-400 dark:border-indigo-500 text-white">
                        @if($review->book_type === 'paper')
                            Паперова
                        @elseif($review->book_type === 'electronic')
                            Електронна
                        @elseif($review->book_type === 'audio')
                            Аудіо
                        @endif
                    </span>
                @endif
                @if ($review->language)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-emerald-500/60 dark:bg-emerald-600/60 border border-emerald-400 dark:border-emerald-500 text-white">
                        @if($review->language === 'uk')
                            Українська
                        @elseif($review->language === 'en')
                            English
                        @elseif($review->language === 'de')
                            Deutsch
                        @else
                            {{ $review->language }}
                        @endif
                    </span>
                @endif
            </div>
            @endif
        </div>

        <!-- Rating Stars -->
        @if ($review->rating && !$review->isReply())
            <div class="flex items-center space-x-2">
                <span class="text-yellow-400 text-2xl"><i class="fas fa-star"></i></span>
                <span class="ml-3 text-lg font-bold text-slate-700 dark:text-slate-300">{{ $review->rating }}/10</span>
            </div>
        @endif
    </div>

    <!-- Review Text (Truncated) -->
    <div class="mb-4">
        <p class="text-lg text-slate-700 dark:text-slate-300 leading-relaxed font-medium line-clamp-3">
            {{ Str::limit(strip_tags($review->content), 300) }}
        </p>
        @if(mb_strlen(strip_tags($review->content)) > 300)
            <p class="text-indigo-600 dark:text-indigo-400 text-sm font-medium mt-2">
                Читати повністю →
            </p>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-6">
            <!-- Like Button -->
            <button onclick="event.stopPropagation(); toggleLike({{ $review->id }})" 
                    class="flex items-center space-x-2 px-4 py-2 rounded-xl font-semibold transition-all duration-200 {{ auth()->check() && $review->isLikedBy(auth()->id()) ? 'text-red-500 bg-red-50 dark:bg-red-900/20' : 'text-slate-600 dark:text-slate-400 hover:text-red-500 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="{{ auth()->check() && $review->isLikedBy(auth()->id()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span id="likes-count-{{ $review->id }}">{{ $review->likes_count ?? 0 }}</span>
            </button>

            <!-- Comments Button -->
            <button onclick="event.stopPropagation(); window.location.href='{{ route('books.reviews.show', [$book, $review]) }}'" 
                    class="flex items-center space-x-2 px-4 py-2 rounded-xl font-semibold text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span>{{ $review->replies_count ?? 0 }}</span>
            </button>
        </div>

        <div class="text-sm text-slate-500 dark:text-slate-400">
            {{ $review->replies_count ?? 0 }} {{ $review->replies_count == 1 ? 'відповідь' : ($review->replies_count < 5 ? 'відповіді' : 'відповідей') }}
        </div>
    </div>
</div>
