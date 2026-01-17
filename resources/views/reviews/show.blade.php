@extends('layouts.app')

@section('title', 'Рецензія на ' . $book->title . ' - Книжковий форум')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
<style>
    .hashtag-highlight {
        color: #6366f1; /* indigo-500 */
        font-weight: 500;
    }
    .dark .hashtag-highlight {
        color: #818cf8; /* indigo-400 */
    }
</style>
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
                        <div id="review-rating-opinion" class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
                            <div class="flex items-center gap-2">
                                <span class="text-yellow-400 text-2xl"><i class="fas fa-star"></i></span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $review->rating }}/10</span>
                            </div>
                            <!-- Opinion Reaction -->
                            @if($review->opinion_type)
                                <opinion-type-icon :opinion-type="'{{ $review->opinion_type }}'" size="md"></opinion-type-icon>
                            @endif
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
                <div id="review-content" class="lg:px-6 py-6 leading-7 text-slate-900 dark:text-slate-100 prose prose-slate dark:prose-invert max-w-none break-words" style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
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
                        
                        @auth
                        <button onclick="toggleFavorite({{ $review->id }})" 
                                id="favorite-btn-{{ $review->id }}"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 text-sm font-medium {{ auth()->check() && $review->isFavoritedBy(auth()->id()) ? 'text-yellow-500 bg-yellow-50 dark:bg-yellow-900/20' : 'text-slate-600 dark:text-slate-400' }} hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-yellow-500 dark:hover:text-yellow-400"
                                title="Зберегти в обране">
                            <svg class="w-4 h-4" fill="{{ auth()->check() && $review->isFavoritedBy(auth()->id()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </button>
                        @endauth
                        
                        <button onclick="shareReview()" 
                                class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-slate-200"
                                title="Поділитися рецензією">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                            </svg>
                            <span class="hidden sm:inline">Поділитися</span>
                        </button>
                        
                        @auth
                        <button onclick="openReportModalForReview({{ $review->id }})" 
                                class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all duration-200 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-red-500 dark:hover:text-red-400"
                                title="Поскаржитись">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                            </svg>
                            <span class="hidden sm:inline">Поскаржитись</span>
                        </button>
                        @endauth
                        
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
                        :current-user-id="{{ auth()->check() ? auth()->id() : 'null' }}"
                        :is-moderator="{{ auth()->check() && auth()->user()->isModerator() ? 'true' : 'false' }}">
                    </reviews-replies-list>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Функция для подсветки хештегов в контенте
function highlightHashtags() {
    const reviewContent = document.getElementById('review-content');
    if (!reviewContent) return;
    
    // Регулярное выражение для поиска хештегов (поддерживает кириллицу и латиницу)
    const hashtagRegex = /#([a-zA-Zа-яА-ЯёЁіІїЇєЄ0-9_]+)/g;
    
    // Функция для обработки текстовых узлов
    function processTextNode(node) {
        const text = node.textContent;
        if (!hashtagRegex.test(text)) return;
        
        // Сбрасываем regex
        hashtagRegex.lastIndex = 0;
        
        const parent = node.parentNode;
        const parts = [];
        let lastIndex = 0;
        let match;
        
        while ((match = hashtagRegex.exec(text)) !== null) {
            // Добавляем текст до хештега
            if (match.index > lastIndex) {
                parts.push(document.createTextNode(text.substring(lastIndex, match.index)));
            }
            
            // Создаем span для хештега
            const hashtagSpan = document.createElement('span');
            hashtagSpan.className = 'hashtag-highlight';
            hashtagSpan.textContent = match[0]; // Включаем #
            parts.push(hashtagSpan);
            
            lastIndex = hashtagRegex.lastIndex;
        }
        
        // Добавляем оставшийся текст
        if (lastIndex < text.length) {
            parts.push(document.createTextNode(text.substring(lastIndex)));
        }
        
        // Заменяем текстовый узел на новые узлы
        if (parts.length > 0) {
            parts.forEach(part => parent.insertBefore(part, node));
            parent.removeChild(node);
        }
    }
    
    // Рекурсивно обрабатываем все текстовые узлы
    function walkNode(node) {
        if (node.nodeType === Node.TEXT_NODE) {
            // Проверяем, что родитель не является уже обработанным хештегом
            if (node.parentNode && !node.parentNode.classList.contains('hashtag-highlight')) {
                processTextNode(node);
            }
        } else if (node.nodeType === Node.ELEMENT_NODE) {
            // Пропускаем script и style теги
            if (node.tagName !== 'SCRIPT' && node.tagName !== 'STYLE') {
                // Создаем копию дочерних узлов для безопасной итерации
                const children = Array.from(node.childNodes);
                children.forEach(child => walkNode(child));
            }
        }
    }
    
    walkNode(reviewContent);
}

// Vue приложение для ответов на рецензии
document.addEventListener('DOMContentLoaded', function() {
    // Подсвечиваем хештеги в контенте рецензии
    highlightHashtags();
    // Инициализируем Vue для области с рейтингом и типом думки
    if (document.getElementById('review-rating-opinion')) {
        new Vue({
            el: '#review-rating-opinion'
        });
    }
    
    // Функция для открытия модального окна скарги для рецензии
    @auth
    @php
        $contentPreview = \Illuminate\Support\Str::limit(strip_tags($review->content ?? ''), 100);
        $contentUrl = route('books.reviews.show', [$book, $review]);
    @endphp
    window.openReportModalForReview = function(reviewId) {
        // Создаем контейнер для модального окна, если его еще нет
        let modalContainer = document.getElementById('report-modal-global-container');
        if (modalContainer && modalContainer.__vue__) {
            // Если уже есть экземпляр, просто обновляем данные
            modalContainer.__vue__.show = true;
            modalContainer.__vue__.reportableId = reviewId;
            return;
        }
        
        if (modalContainer) {
            modalContainer.remove();
        }
        
        modalContainer = document.createElement('div');
        modalContainer.id = 'report-modal-global-container';
        document.body.appendChild(modalContainer);
        
        // Проверяем, есть ли Vue компонент
        if (typeof Vue !== 'undefined' && Vue.component('report-modal')) {
            // Создаем новый Vue экземпляр для модального окна
            new Vue({
                el: modalContainer,
                template: '<report-modal :show="show" reportable-type="App\\Models\\Review" :reportable-id="reportableId" :content-preview="contentPreview" :content-url="contentUrl" @close="closeModal"></report-modal>',
                data: {
                    show: true,
                    reportableId: reviewId,
                    contentPreview: {!! json_encode($contentPreview) !!},
                    contentUrl: {!! json_encode($contentUrl) !!}
                },
                methods: {
                    closeModal() {
                        this.show = false;
                        setTimeout(() => {
                            if (this.$el && this.$el.parentNode) {
                                this.$destroy();
                                this.$el.parentNode.removeChild(this.$el);
                            }
                        }, 300);
                    }
                }
            });
        } else {
            alert('Компонент скарги недоступен. Будь ласка, оновіть сторінку.');
        }
    };
    @endauth
    
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
        button.disabled = false;
    });
    @endauth
}

// Функция для добавления/удаления из избранного
window.toggleFavorite = function(reviewId) {
    @auth
    const button = document.getElementById(`favorite-btn-${reviewId}`);
    
    if (!button) return;
    
    const originalContent = button.innerHTML;
    button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    button.disabled = true;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch(`/books/{{ $book->slug }}/reviews/${reviewId}/favorite`, {
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
            const svg = button.querySelector('svg');
            if (data.is_favorited) {
                button.classList.remove('text-slate-600', 'dark:text-slate-400');
                button.classList.add('text-yellow-500', 'bg-yellow-50', 'dark:bg-yellow-900/20');
                if (svg) svg.setAttribute('fill', 'currentColor');
            } else {
                button.classList.remove('text-yellow-500', 'bg-yellow-50', 'dark:bg-yellow-900/20');
                button.classList.add('text-slate-600', 'dark:text-slate-400');
                if (svg) svg.setAttribute('fill', 'none');
            }
        }
        button.innerHTML = originalContent;
        button.disabled = false;
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = originalContent;
        button.disabled = false;
    });
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

// Функция для шаринга рецензии
window.shareReview = async function() {
    try {
        // Используем shareContent из app.js если доступен
        if (window.shareContent) {
            await window.shareContent({
                title: '{{ $review->review_type === "review" ? "Рецензія" : "Відгук" }} на {{ addslashes($book->title) }}',
                text: '{{ $review->review_type === "review" ? "Рецензія" : "Відгук" }} від {{ $review->isGuest() ? "Гість" : addslashes($review->user->name) }}',
                url: window.location.href
            });
            return;
        }

        // Fallback к нативному шарингу или буферу обмена
        if (navigator.share) {
            await navigator.share({
                title: '{{ $review->review_type === "review" ? "Рецензія" : "Відгук" }} на {{ addslashes($book->title) }}',
                text: '{{ $review->review_type === "review" ? "Рецензія" : "Відгук" }} від {{ $review->isGuest() ? "Гість" : addslashes($review->user->name) }}',
                url: window.location.href
            });
        } else {
            // Копируем ссылку в буфер обмена
            await navigator.clipboard.writeText(window.location.href);
            // Показываем уведомление
            if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
                window.alertModalInstance.$refs.modal.alert('Посилання скопійовано в буфер обміну!', 'Успіх', 'success');
            } else {
                alert('Посилання скопійовано в буфер обміну!');
            }
        }
    } catch (error) {
        console.error('Error sharing review:', error);
        // Если пользователь отменил шаринг, не показываем ошибку
        if (error.name !== 'AbortError') {
            // Показываем ссылку в alert как последний fallback
            if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
                window.alertModalInstance.$refs.modal.alert(`Посилання: ${window.location.href}`, 'Посилання', 'info');
            } else {
                alert(`Посилання: ${window.location.href}`);
            }
        }
    }
}
</script>
@endpush
@endsection
