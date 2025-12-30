@extends('layouts.app')

@section('title', $reply->user->name . ' - Відповідь')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('main')
    <div class="min-h-screen duration-300">
        <div class="max-w-7xl mx-auto flex gap-8">
            <div class="flex flex-col lg:flex-row gap-8 w-full">
                <!-- Main Content -->
                <div class="flex-1 min-w-0 order-2 lg:order-1">
                    <!-- Breadcrumb -->
                    <div class="mb-4">
                        <nav class="text-sm text-slate-500 dark:text-slate-400">
                            <ol class="flex flex-wrap gap-2">
                                <li><a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Головна</a></li>
                                <li>/</li>
                                <li><a href="{{ route('discussions.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Обговорення</a></li>
                                <li>/</li>
                                <li><a href="{{ route('discussions.show', $discussion->slug) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ Str::limit($discussion->title, 50) }}</a></li>
                                <li>/</li>
                                <li class="font-semibold text-slate-900 dark:text-white">Коментар</li>
                            </ol>
                        </nav>
                    </div>

                    <!-- Reply Card (Main Comment) -->
                    <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-6 mb-6">
                        <!-- Parent Reply Link -->
                        @if($reply->parent)
                        <div class="mb-4 pb-4 border-b border-light-border dark:border-dark-border">
                            <a href="{{ route('discussions.replies.show', [$discussion->slug, $reply->parent->id]) }}" 
                               class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 flex items-center space-x-2">
                                <i class="fas fa-arrow-up"></i>
                                <span>Відповідь на коментар {{ $reply->parent->user->name }}</span>
                            </a>
                        </div>
                        @else
                        <div class="mb-4 pb-4 border-b border-light-border dark:border-dark-border">
                            <a href="{{ route('discussions.show', $discussion->slug) }}" 
                               class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 flex items-center space-x-2">
                                <i class="fas fa-arrow-up"></i>
                                <span>Повернутись до обговорення: {{ $discussion->title }}</span>
                            </a>
                        </div>
                        @endif

                        <!-- Reply Header -->
                        <div class="flex items-center space-x-4 mb-4">
                            <a href="{{ route('users.public.profile', $reply->user->username) }}">
                                <img src="{{ $reply->user->avatar_display }}" alt="{{ $reply->user->name }}"
                                    class="w-12 h-12 rounded-full">
                            </a>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('users.public.profile', $reply->user->username) }}" 
                                       class="text-light-text-primary dark:text-dark-text-primary font-medium hover:text-brand-500 dark:hover:text-brand-400 transition-colors">
                                        {{ $reply->user->name }}
                                    </a>
                                    <span class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm">
                                        {{ '@' . $reply->user->username }}
                                    </span>
                                </div>
                                <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm">
                                    {{ $reply->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>

                        <!-- Reply Content -->
                        <div class="prose dark:prose-invert max-w-none mb-4">
                            <div class="text-light-text-primary dark:text-dark-text-primary leading-relaxed rich-content text-sm break-words" 
                                 style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                                {!! $reply->content !!}
                            </div>
                        </div>

                        <!-- Reply Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-light-border dark:border-dark-border">
                            <div class="flex items-center space-x-4">
                                @auth
                                    <button onclick="toggleReplyLike({{ $reply->id }})"
                                            class="flex items-center space-x-2 px-4 py-2 rounded-xl transition-colors"
                                            id="likeButton{{ $reply->id }}">
                                        <i class="fas fa-heart {{ $reply->isLikedBy(Auth::id()) ? 'text-red-500 dark:text-red-400' : 'text-light-text-tertiary dark:text-dark-text-tertiary' }}"></i>
                                        <span class="text-light-text-primary dark:text-dark-text-primary"
                                            id="likeCount{{ $reply->id }}">{{ $reply->likes_count ?? 0 }}</span>
                                    </button>
                                @endauth
                                <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm">
                                    <span class="font-medium text-light-text-primary dark:text-dark-text-primary">{{ $reply->replies->count() ?? 0 }}</span> відповідей
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Replies to this comment -->
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-light-text-primary dark:text-dark-text-primary mb-4">
                            Відповіді ({{ $reply->replies->count() ?? 0 }})
                        </h2>

                        <!-- Vue.js Replies Component -->
                        <div id="discussion-replies-app">
                            @php
                                $formatReply = function($reply) use (&$formatReply) {
                                    return [
                                        'id' => $reply->id,
                                        'content' => $reply->content,
                                        'created_at' => $reply->created_at->toISOString(),
                                        'user_id' => $reply->user_id,
                                        'parent_id' => $reply->parent_id,
                                        'user' => $reply->user ? [
                                            'id' => $reply->user->id,
                                            'name' => $reply->user->name,
                                            'username' => $reply->user->username,
                                            'avatar_display' => $reply->user->avatar_display ?? null,
                                        ] : null,
                                        'is_liked_by_current_user' => auth()->check() ? $reply->isLikedBy(auth()->id()) : false,
                                        'likes_count' => $reply->likes_count ?? 0,
                                        'replies_count' => $reply->replies_count ?? 0,
                                        'replies' => $reply->replies ? $reply->replies->map($formatReply)->toArray() : [],
                                    ];
                                };
                                $repliesData = $reply->replies->map($formatReply)->toArray();
                            @endphp

                            <discussion-replies-list 
                                :replies="{{ json_encode($repliesData) }}"
                                :discussion-slug="'{{ $discussion->slug }}'"
                                :current-user-id="{{ auth()->check() ? auth()->id() : 'null' }}"
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
                                <img src="{{ $reply->user->avatar_display }}" alt="{{ $reply->user->name }}"
                                    class="w-20 h-20 rounded-full mx-auto mb-3 border-2 border-light-border dark:border-dark-border">
                                <h3 class="text-lg font-semibold text-light-text-primary dark:text-dark-text-primary mb-1">
                                    {{ $reply->user->name }}
                                </h3>
                                <a href="{{ route('users.public.profile', $reply->user->username) }}"
                                    class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm hover:text-brand-500 dark:hover:text-brand-400 transition-colors">
                                    {{ "@" . $reply->user->username }}
                                </a>
                            </div>

                            <!-- Author Stats -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between py-2 border-b border-light-border dark:border-dark-border">
                                    <span class="text-light-text-secondary dark:text-dark-text-secondary text-sm">Обговорення</span>
                                    <span class="text-light-text-primary dark:text-dark-text-primary font-medium">
                                        {{ $reply->user->active_discussions_count ?? 0 }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-light-border dark:border-dark-border">
                                    <span class="text-light-text-secondary dark:text-dark-text-secondary text-sm">Відповіді</span>
                                    <span class="text-light-text-primary dark:text-dark-text-primary font-medium">
                                        {{ $reply->user->active_replies_count ?? 0 }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b border-light-border dark:border-dark-border">
                                    <span class="text-light-text-secondary dark:text-dark-text-secondary text-sm">Рецензії</span>
                                    <span class="text-light-text-primary dark:text-dark-text-primary font-medium">
                                        {{ $reply->user->active_reviews_count ?? 0 }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-light-text-secondary dark:text-dark-text-secondary text-sm">Зареєстрований з</span>
                                    <span class="text-light-text-primary dark:text-dark-text-primary font-medium">
                                        {{ $reply->user->created_at->format('M Y') }}
                                    </span>
                                </div>
                            </div>

                            <!-- View Profile Button -->
                            <div class="mt-4">
                                <a href="{{ route('users.public.profile', $reply->user->username) }}"
                                    class="w-full bg-gradient-to-r from-brand-500 to-accent-500 text-white px-4 py-2 rounded-lg font-medium hover:from-brand-600 hover:to-accent-600 transition-all duration-300 text-center block">
                                    Переглянути профіль
                                </a>
                            </div>
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

                // Like functionality for reply
                function toggleReplyLike(replyId) {
                    fetch(`/discussions/{{ $discussion->slug }}/replies/${replyId}/like`, {
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
                                const button = document.getElementById(`likeButton${replyId}`);
                                const count = document.getElementById(`likeCount${replyId}`);
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
            </script>
        @endpush
    @endsection
