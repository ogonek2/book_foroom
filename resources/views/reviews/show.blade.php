@extends('layouts.app')

@section('title', 'Рецензія на ' . $book->title . ' - Книжковий форум')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
@endpush

@section('main')
<div class="max-w-7xl mx-auto space-y-8">
    <!-- Breadcrumb Navigation -->
    <nav class="text-sm text-slate-500 dark:text-slate-400">
        <ol class="flex flex-wrap gap-2">
            <li><a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Головна</a></li>
            <li>/</li>
            <li><a href="{{ route('books.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Книги</a></li>
            <li>/</li>
            <li><a href="{{ route('books.show', $book) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ $book->title }}</a></li>
            <li>/</li>
            <li><a href="{{ route('books.reviews.index', $book) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Всі рецензії</a></li>
            <li>/</li>
            <li class="font-semibold text-slate-900 dark:text-white">{{ $review->review_type === 'review' ? 'Рецензія' : 'Відгук' }}</li>
        </ol>
    </nav>

    <div class="w-full flex items-start">
             <!-- Sticky Book Sidebar -->
        <aside class="w-full max-w-[220px] hidden lg:block lg:sticky top-4 space-y-6">
            <div>
                <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=450&fit=crop&crop=center' }}" 
                     alt="{{ $book->title }}" 
                     class="w-full object-cover rounded-lg mb-4" style="aspect-ratio: 2 / 3;">
                <div>
                    <p class="text-xs uppercase text-slate-500 dark:text-slate-400 mb-1">Книга</p>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ $book->title }}</h2>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        @php
                            $authorModel = $book->relationLoaded('author') ? $book->author : null;
                        @endphp
                        @if ($authorModel && $authorModel->slug)
                            <a href="{{ route('authors.show', $authorModel->slug) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                {{ $authorModel->full_name ?? $authorModel->short_name ?? $book->author }}
                            </a>
                        @else
                            {{ is_string($book->author) ? $book->author : ($book->author ?? 'Невідомий автор') }}
                        @endif
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-3">
                <a href="{{ route('books.show', $book) }}" 
                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <i class="fas fa-book-open mr-2"></i> До книги
                </a>
                <a href="{{ route('books.reviews.index', $book) }}" 
                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    <i class="fas fa-comments mr-2"></i> Всі рецензії
                </a>
            </div>
        </aside>
        <!-- Main Content -->
        <div class="w-full flex-1 space-y-4">
            <!-- Main Review Card -->
            <div class="overflow-hidden">
                <!-- Review Header -->
                <div class="lg:p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @include('partials.user-mini-header', [
                                'user' => $review->isGuest() ? null : $review->user,
                                'timestamp' => $review->created_at->diffForHumans(),
                                'showGuest' => $review->isGuest()
                            ])
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                            <span class="text-yellow-400 text-2xl"><i class="fas fa-star"></i></span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ $review->rating }}/10</span>
                        </div>
                    </div>
                </div>
                
                <!-- Review Meta Info -->
                @if ($review->review_type || $review->book_type || $review->language)
                <div class="border-b border-white/60 dark:border-white/10 lg:px-4 pt-2 pb-4">
                    <div class="flex items-center flex-wrap gap-2">
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
                </div>
                @endif
                
                <!-- Review Content -->
                <div class="lg:px-6 py-6 leading-7 text-slate-900 dark:text-slate-100 prose prose-slate dark:prose-invert max-w-none">
                    {!! $review->content !!}
                </div>
                
                <!-- Review Actions -->
                <div class="px-6 py-4 border-t border-white/60 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button onclick="toggleLike({{ $review->id }})" 
                                class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 text-sm font-medium {{ auth()->check() && $review->isLikedBy(auth()->id()) ? 'text-red-500' : 'text-slate-600 dark:text-slate-400' }} hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-slate-200">
                            <svg class="w-4 h-4" fill="{{ auth()->check() && $review->isLikedBy(auth()->id()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span id="likes-count-{{ $review->id }}">{{ $review->likes_count ?? 0 }}</span>
                        </button>
                        
                        <!-- Review Controls (only for own reviews) -->
                        @if(auth()->check() && $review->user_id == auth()->id())
                            <div class="flex items-center gap-2">
                                <a href="{{ route('books.reviews.edit', [$book, $review]) }}" class="flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-blue-500" title="Редагувати рецензію">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                
                                <button onclick="deleteComment({{ $review->id }})" class="flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-red-500" title="Видалити рецензію">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        <span class="replies-count font-medium text-slate-900 dark:text-white">{{ $review->replies_count ?? 0 }}</span> {{ $review->replies_count == 1 ? 'відповідь' : ($review->replies_count < 5 ? 'відповіді' : 'відповідей') }}
                    </div>
                </div>
            </div>

            <!-- Replies Section -->
            <div class="lg:p-6">
                @php
                // Рекурсивная функция для формирования данных ответов
                $formatReply = function($reply) use (&$formatReply) {
                    return [
                        'id' => $reply->id,
                        'content' => $reply->content,
                        'created_at' => $reply->created_at->toISOString(),
                        'user_id' => $reply->user_id,
                        'parent_id' => $reply->parent_id,
                        'is_guest' => $reply->isGuest(),
                        'user' => $reply->user ? [
                            'id' => $reply->user->id,
                            'name' => $reply->user->name,
                            'username' => $reply->user->username,
                            'avatar_display' => $reply->user->avatar_display ?? null,
                        ] : null,
                        'is_liked_by_current_user' => auth()->check() ? $reply->isLikedBy(auth()->id()) : false,
                        'likes_count' => $reply->likes_count ?? 0,
                        'replies_count' => $reply->replies_count ?? 0,
                        'replies' => $reply->replies->map($formatReply)->toArray(),
                    ];
                };
                
                $repliesData = $review->replies->map($formatReply)->toArray();
                @endphp
                
                <div id="review-replies-app">
                    <reviews-replies-list 
                        :replies="{{ json_encode($repliesData) }}"
                        book-slug="{{ $book->slug }}"
                        :review-id="{{ $review->id }}"
                        :current-user-id="{{ auth()->id() }}"
                        :is-moderator="{{ auth()->check() && auth()->user()->isModerator() ? 'true' : 'false' }}">
                    </reviews-replies-list>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Vue приложение для ответов на рецензии
document.addEventListener('DOMContentLoaded', function() {
    const reviewRepliesApp = new Vue({
        el: '#review-replies-app',
        data: {
            // Данные передаются через props
        },
        methods: {
            showNotification(message, type = 'success') {
                // Уведомление обрабатывается в компоненте
            }
        }
    });
});

// Функция для лайка главной рецензии (не обрабатывается Vue)
window.toggleLike = function(reviewId) {
    @auth
    const button = document.querySelector(`[onclick="toggleLike(${reviewId})"]`);
    const countElement = document.getElementById(`likes-count-${reviewId}`);
    
    if (!button || !countElement) return;
    
    const originalContent = button.innerHTML;
    button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    button.disabled = true;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch(`/books/{{ $book->slug }}/reviews/${reviewId}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            countElement.textContent = data.likes_count;
            button.innerHTML = originalContent;
            
            const countSpan = button.querySelector('span');
            if (countSpan) {
                countSpan.textContent = data.likes_count;
            }
            
            const svg = button.querySelector('svg');
            if (data.is_liked) {
                button.classList.remove('text-slate-600', 'dark:text-slate-400');
                button.classList.add('text-red-500');
                if (svg) svg.setAttribute('fill', 'currentColor');
            } else {
                button.classList.remove('text-red-500');
                button.classList.add('text-slate-600', 'dark:text-slate-400');
                if (svg) svg.setAttribute('fill', 'none');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = originalContent;
    })
    .finally(() => {
        button.disabled = false;
    });
    @else
    alert('Будь ласка, увійдіть в систему, щоб ставити лайки');
    @endauth
}

// Функции для редактирования/удаления главной рецензии (не обрабатывается Vue)
window.editComment = function(commentId) {
    // Эта функция больше не используется для ответов (обрабатывается Vue)
    // Оставляем только для совместимости, если используется где-то еще
}

window.deleteComment = function(commentId) {
    // Эта функция больше не используется для ответов (обрабатывается Vue)
    // Оставляем только для совместимости, если используется где-то еще
}
</script>
@endpush
@endsection
