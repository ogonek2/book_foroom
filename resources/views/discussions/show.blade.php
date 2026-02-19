@extends('layouts.app')

@section('title', $discussion->title)

@section('main')
    <div class="min-h-screen duration-300">
        <div class="max-w-7xl mx-auto flex gap-8">
            <div class="flex flex-col lg:flex-row gap-8 w-full">
                <!-- Main Content -->
                <div class="flex-1 min-w-0 order-2 lg:order-1">

                    <!-- Discussion Header -->
                    <div>
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex-1">
                                <!-- Status Badges -->
                                <div class="flex items-center space-x-3 mb-4">
                                    @if ($discussion->is_pinned)
                                        <span
                                            class="bg-yellow-500 text-yellow-900 dark:bg-yellow-600 dark:text-yellow-100 px-3 py-1 rounded-full text-sm font-bold">
                                            <i class="fas fa-thumbtack mr-1"></i>
                                            Закріплено
                                        </span>
                                    @endif
                                    @if ($discussion->is_closed)
                                        <span
                                            class="bg-red-500 text-red-900 dark:bg-red-600 dark:text-red-100 px-3 py-1 rounded-full text-sm font-bold">
                                            <i class="fas fa-lock mr-1"></i>
                                            Зачинено
                                        </span>
                                    @endif
                                </div>

                                <!-- Title -->
                                <h1 class="text-xl font-bold text-light-text-primary dark:text-dark-text-primary mb-4">
                                    {{ $discussion->title }}</h1>

                                <!-- Author Info -->
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('users.public.profile', $discussion->user->username) }}" class="flex items-center space-x-4 group">
                                        <img src="{{ $discussion->user->avatar_display }}" alt="{{ $discussion->user->name }}"
                                            class="w-12 h-12 rounded-full">
                                        <div>
                                            <div class="text-light-text-primary dark:text-dark-text-primary font-medium">
                                                {{ $discussion->user->name }}
                                            </div>
                                            <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm">
                                                {{ $discussion->created_at->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                    <!-- Like Button -->
                                    @auth
                                        <button onclick="toggleLike('{{ $discussion->slug }}')"
                                            class="flex items-center space-x-2 px-4 py-2 rounded-xl transition-colors"
                                            id="likeButton{{ $discussion->id }}">
                                            <i
                                                class="fas fa-heart {{ $discussion->isLikedBy(Auth::id()) ? 'text-red-500 dark:text-red-400' : 'text-light-text-tertiary dark:text-dark-text-tertiary' }}"></i>
                                            <span class="text-light-text-primary dark:text-dark-text-primary"
                                                id="likeCount{{ $discussion->id }}">{{ $discussion->likes_count }}</span>
                                        </button>
                                    @endauth
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="prose dark:prose-invert max-w-none">
                            @if($discussion->status === 'blocked')
                                <div class="border-2 border-red-300 dark:border-red-700 rounded-lg bg-red-50/50 dark:bg-red-900/20 p-4">
                                    <div class="relative overflow-hidden rounded-md mb-3">
                                        <div class="text-light-text-primary dark:text-dark-text-primary leading-relaxed rich-content text-sm break-words blur-sm filter" 
                                             style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                                            {!! $discussion->content !!}
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 pt-2 border-t border-red-200 dark:border-red-800">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <i class="fas fa-exclamation-triangle text-red-500 dark:text-red-400 text-lg"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-red-700 dark:text-red-300 font-medium mb-1">
                                                Контент заблоковано адміністрацією сайту
                                            </p>
                                            @if($discussion->moderation_reason)
                                                <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                                    Причина: {{ $discussion->moderation_reason }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div
                                    class="text-light-text-primary dark:text-dark-text-primary leading-relaxed rich-content text-sm break-words" 
                                    style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                                    {!! $discussion->content !!}
                                </div>
                            @endif
                            <!-- Actions -->
                            @auth
                                <div class="flex items-center space-x-3 py-2 text-sm">
                                    @if ($discussion->canBeEditedBy(Auth::user()))
                                        <a href="{{ route('discussions.edit', $discussion) }}"
                                            class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 transition-colors">
                                            Редагувати
                                        </a>
                                    @endif

                                    @if ($discussion->canBeClosedBy(Auth::user()))
                                        @if ($discussion->is_closed)
                                            <form action="{{ route('discussions.reopen', $discussion) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="text-green-500 dark:text-green-400 hover:text-green-600 dark:hover:text-green-300 transition-colors">
                                                    <i class="fas fa-unlock mr-1"></i>
                                                    Відкрити
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('discussions.close', $discussion) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="text-red-500 dark:text-red-400 hover:text-red-600 dark:hover:text-red-300 transition-colors">
                                                    <i class="fas fa-lock mr-1"></i>
                                                    Закрити
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            @endauth
                        </div>

                        <!-- Stats -->
                        <div
                            class="flex items-center justify-between py-4 border-t border-light-border dark:border-dark-border">
                            <div
                                class="flex items-center space-x-6 text-light-text-tertiary dark:text-dark-text-tertiary text-sm">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-eye"></i>
                                    <span>{{ $discussion->views_count }} Переглядів</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-comments"></i>
                                    <span>{{ $discussion->replies_count }} Відповідей</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-heart"></i>
                                    <span>{{ $discussion->likes_count }} Лайків</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <button onclick="shareDiscussion()" 
                                        type="button"
                                        class="flex items-center space-x-2 text-light-text-tertiary dark:text-dark-text-tertiary hover:text-brand-500 dark:hover:text-brand-400 transition-colors cursor-pointer">
                                    <i class="fas fa-share"></i>
                                    <span class="text-sm">Поділитися</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Vue.js Replies Component -->
                    <div id="discussion-replies-app">
                        <discussion-replies-list 
                            :replies="{{ json_encode($replies->values()->toArray()) }}"
                            :discussion-slug="'{{ $discussion->slug }}'"
                            :current-user-id="{{ auth()->check() ? auth()->id() : null }}"
                            :is-discussion-closed="{{ $discussion->is_closed ? 'true' : 'false' }}"
                            :is-moderator="{{ auth()->check() && auth()->user()->isModerator() ? 'true' : 'false' }}">
                        </discussion-replies-list>
                    </div>
                </div>
            </div>
            <!-- Sidebar -->
            <div class="w-full lg:w-80 flex-shrink-0 order-1 lg:order-2 hidden lg:block">
                <div class="sticky" style="top: 5rem;">
                    <!-- Author Profile Card -->
                    <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-6 mb-6">
                        <!-- Author Avatar and Info -->
                        <div class="text-center mb-4">
                            <img src="{{ $discussion->user->avatar_display }}" alt="{{ $discussion->user->name }}"
                                class="w-20 h-20 rounded-full mx-auto mb-3 border-2 border-light-border dark:border-dark-border">
                            <h3 class="text-lg font-semibold text-light-text-primary dark:text-dark-text-primary mb-1">
                                {{ $discussion->user->name }}
                            </h3>
                            <a href="{{ route('users.public.profile', $discussion->user->username) }}"
                                class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm hover:text-brand-500 dark:hover:text-brand-400 transition-colors">
                                {{ "@" . $discussion->user->username }}
                            </a>
                        </div>

                        <!-- Author Stats -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between py-2 border-b border-light-border dark:border-dark-border">
                                <span class="text-light-text-secondary dark:text-dark-text-secondary text-sm">Обговорення</span>
                                <span class="text-light-text-primary dark:text-dark-text-primary font-medium" id="authorActiveDiscussionsCount">
                                    {{ $discussion->user->active_discussions_count }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-light-border dark:border-dark-border">
                                <span class="text-light-text-secondary dark:text-dark-text-secondary text-sm">Відповіді</span>
                                <span class="text-light-text-primary dark:text-dark-text-primary font-medium">
                                    {{ $discussion->user->active_replies_count }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-light-border dark:border-dark-border">
                                <span class="text-light-text-secondary dark:text-dark-text-secondary text-sm">Рецензії</span>
                                <span class="text-light-text-primary dark:text-dark-text-primary font-medium">
                                    {{ $discussion->user->active_reviews_count }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <span class="text-light-text-secondary dark:text-dark-text-secondary text-sm">Зареєстрований з</span>
                                <span class="text-light-text-primary dark:text-dark-text-primary font-medium">
                                    {{ $discussion->user->created_at->format('M Y') }}
                                </span>
                            </div>
                        </div>

                        <!-- View Profile Button -->
                        <div class="mt-4">
                            <a href="{{ route('users.public.profile', $discussion->user->username) }}"
                                class="w-full bg-gradient-to-r from-brand-500 to-accent-500 text-white px-4 py-2 rounded-lg font-medium hover:from-brand-600 hover:to-accent-600 transition-all duration-300 text-center block">
                                Переглянути профіль
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('styles')
            <style>
                .rich-content {
                    word-wrap: break-word;
                }

                .rich-content h1,
                .rich-content h2,
                .rich-content h3,
                .rich-content h4,
                .rich-content h5,
                .rich-content h6 {
                    font-weight: 600;
                    margin-top: 1.5em;
                    margin-bottom: 0.5em;
                    line-height: 1.25;
                }

                .rich-content h1 {
                    font-size: 1.875rem;
                }

                .rich-content h2 {
                    font-size: 1.5rem;
                }

                .rich-content h3 {
                    font-size: 1.25rem;
                }

                .rich-content h4 {
                    font-size: 1.125rem;
                }

                .rich-content h5 {
                    font-size: 1rem;
                }

                .rich-content h6 {
                    font-size: 0.875rem;
                }

                .rich-content p {
                    margin-bottom: 1em;
                    line-height: 1.6;
                }

                .rich-content ul,
                .rich-content ol {
                    margin-bottom: 1em;
                    padding-left: 1.5em;
                }

                .rich-content li {
                    margin-bottom: 0.25em;
                }

                .rich-content blockquote {
                    border-left: 4px solid #e5e7eb;
                    padding-left: 1em;
                    margin: 1em 0;
                    font-style: italic;
                    color: #6b7280;
                }

                .dark .rich-content blockquote {
                    border-left-color: #4b5563;
                    color: #9ca3af;
                }

                .rich-content a {
                    color: #3b82f6;
                    text-decoration: underline;
                }

                .rich-content a:hover {
                    color: #1d4ed8;
                }

                .dark .rich-content a {
                    color: #60a5fa;
                }

                .dark .rich-content a:hover {
                    color: #93c5fd;
                }

                .rich-content img {
                    max-width: 100%;
                    height: auto;
                    border-radius: 0.5rem;
                    margin: 1em 0;
                }

                .rich-content code {
                    background-color: #f3f4f6;
                    padding: 0.125rem 0.25rem;
                    border-radius: 0.25rem;
                    font-family: 'Courier New', monospace;
                    font-size: 0.875em;
                }

                .dark .rich-content code {
                    background-color: #374151;
                }

                .rich-content pre {
                    background-color: #f3f4f6;
                    padding: 1rem;
                    border-radius: 0.5rem;
                    overflow-x: auto;
                    margin: 1em 0;
                }

                .dark .rich-content pre {
                    background-color: #374151;
                }

                .rich-content pre code {
                    background: none;
                    padding: 0;
                }

                .rich-content table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 1em 0;
                }

                .rich-content th,
                .rich-content td {
                    border: 1px solid #e5e7eb;
                    padding: 0.5rem;
                    text-align: left;
                }

                .dark .rich-content th,
                .dark .rich-content td {
                    border-color: #4b5563;
                }

                .rich-content th {
                    background-color: #f9fafb;
                    font-weight: 600;
                }

                .dark .rich-content th {
                    background-color: #374151;
                }
            </style>
        @endpush

        @push('scripts')
            <script>
                // Vue приложение для страницы обсуждения
                document.addEventListener('DOMContentLoaded', function() {
                    const discussionApp = new Vue({
                        el: '#discussion-replies-app',
                        data: {
                            // Данные передаются через props
                        },
                        methods: {
                            showNotification(message, type = 'success') {
                                // Простое уведомление
                                const alertType = type === 'success' ? 'success' : type === 'error' ? 'error' : 'info';
                                alert(message, type.toUpperCase(), alertType);
                            }
                        }
                    });
                });

                // Like functionality for main discussion
                function toggleLike(discussionSlug) {
                    fetch(`/discussions/${discussionSlug}/like`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const button = document.getElementById(`likeButton{{ $discussion->id }}`);
                                const count = document.getElementById(`likeCount{{ $discussion->id }}`);
                                const icon = button.querySelector('i');

                                if (data.liked) {
                                    icon.classList.remove('text-light-text-tertiary', 'dark:text-dark-text-tertiary');
                                    icon.classList.add('text-red-500', 'dark:text-red-400');
                                } else {
                                    icon.classList.remove('text-red-500', 'dark:text-red-400');
                                    icon.classList.add('text-light-text-tertiary', 'dark:text-dark-text-tertiary');
                                }

                                count.textContent = data.likes_count;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }

                // Toggle discussion status function
                window.toggleDiscussionStatus = async function(discussionSlug, isClosed, event) {
                    const checkbox = event ? event.target : document.querySelector(`input[onchange*="${discussionSlug}"]`);
                    try {
                        const url = isClosed 
                            ? `/discussions/${discussionSlug}/close`
                            : `/discussions/${discussionSlug}/reopen`;
                        
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();
                            // Update the label text
                            const label = checkbox ? checkbox.closest('label').querySelector('span') : null;
                            if (label) {
                                label.textContent = isClosed ? 'Закрите' : 'Відкрите';
                            }
                            // Update the badge if it exists
                            const badge = document.querySelector('.bg-red-500');
                            if (badge) {
                                if (isClosed) {
                                    badge.style.display = 'inline-block';
                                } else {
                                    badge.style.display = 'none';
                                }
                            }
                            
                            // Update author's active discussions count if on discussion show page
                            const authorCountElement = document.getElementById('authorActiveDiscussionsCount');
                            if (authorCountElement) {
                                let currentCount = parseInt(authorCountElement.textContent) || 0;
                                if (isClosed) {
                                    // Closing discussion - decrease count
                                    currentCount = Math.max(0, currentCount - 1);
                                } else {
                                    // Opening discussion - increase count
                                    currentCount = currentCount + 1;
                                }
                                authorCountElement.textContent = currentCount;
                            }
                        } else {
                            // Revert checkbox on error
                            if (checkbox) checkbox.checked = !isClosed;
                            alert('Помилка при зміні статусу обговорення', 'Помилка', 'error');
                        }
                    } catch (error) {
                        console.error('Error toggling discussion status:', error);
                        // Revert checkbox on error
                        if (checkbox) checkbox.checked = !isClosed;
                        alert('Помилка при зміні статусу обговорення', 'Помилка', 'error');
                    }
                }

                // Share discussion function
                window.shareDiscussion = async function() {
                    try {
                        // Используем shareContent из app.js если доступен
                        if (window.shareContent) {
                            await window.shareContent({
                                title: '{{ addslashes($discussion->title) }}',
                                text: '{{ addslashes(strip_tags($discussion->content)) }}'.substring(0, 200),
                                url: window.location.href
                            });
                            return;
                        }

                        // Fallback к нативному шарингу или буферу обмена
                        if (navigator.share) {
                            await navigator.share({
                                title: '{{ addslashes($discussion->title) }}',
                                text: '{{ addslashes(strip_tags($discussion->content)) }}'.substring(0, 200),
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
                        if (error.name !== 'AbortError') {
                            console.error('Error sharing discussion:', error);
                        }
                    }
                };
            </script>
        @endpush
        
        @push('styles')
            <style>
                /* Mention link styles */
                .mention-link {
                    color: rgb(139, 92, 246);
                    font-weight: 500;
                    text-decoration: none;
                    transition: color 0.2s ease;
                }
                
                .mention-link:hover {
                    color: rgb(124, 58, 237);
                    text-decoration: underline;
                }
                
                .dark .mention-link {
                    color: rgb(167, 139, 250);
                }
                
                .dark .mention-link:hover {
                    color: rgb(196, 181, 253);
                }
            </style>
        @endpush
    @endsection
