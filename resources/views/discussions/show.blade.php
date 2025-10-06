@extends('layouts.app')

@section('title', $discussion->title)

@section('main')
    <div class="min-h-screen duration-300">
        <div class="max-w-7xl mx-auto flex gap-8">
            <div class="flex flex-col lg:flex-row gap-8">
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
                                            Закреплено
                                        </span>
                                    @endif
                                    @if ($discussion->is_closed)
                                        <span
                                            class="bg-red-500 text-red-900 dark:bg-red-600 dark:text-red-100 px-3 py-1 rounded-full text-sm font-bold">
                                            <i class="fas fa-lock mr-1"></i>
                                            Закрыто
                                        </span>
                                    @endif
                                </div>

                                <!-- Title -->
                                <h1 class="text-xl font-bold text-light-text-primary dark:text-dark-text-primary mb-4">
                                    {{ $discussion->title }}</h1>

                                <!-- Author Info -->
                                <div class="flex items-center space-x-4">
                                    <img src="{{ $discussion->user->avatar_display }}" alt="{{ $discussion->user->name }}"
                                        class="w-12 h-12 rounded-full">
                                    <div>
                                        <div class="text-light-text-primary dark:text-dark-text-primary font-medium">
                                            {{ $discussion->user->name }}
                                            <a href="{{ route('users.public.profile', $discussion->user->username) }}"
                                                class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm">{{ '@' . $discussion->user->username }}</a>
                                        </div>
                                        <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm">
                                            {{ $discussion->created_at->diffForHumans() }}</div>
                                    </div>
                                    <!-- Like Button -->
                                    @auth
                                        <button onclick="toggleLike({{ $discussion->id }})"
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
                            <div
                                class="text-light-text-primary dark:text-dark-text-primary leading-relaxed rich-content text-sm">
                                {!! $discussion->content !!}
                            </div>
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
                        </div>
                    </div>

                    <!-- Replies Section -->
                    <div>
                        <!-- Reply Form -->
                        @auth
                            @if (!$discussion->is_closed)
                                <div>
                                    <form id="replyForm" action="{{ route('discussions.replies.store', $discussion) }}"
                                        method="POST" class="space-y-4">
                                        @csrf
                                        <textarea id="replyContent" name="content" rows="1" placeholder="Що ви думаєте про це?"
                                            class="w-full px-4 py-4 bg-light-bg-secondary dark:bg-gray-700 border border-light-border dark:border-dark-border rounded-xl text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none transition-colors"
                                            required></textarea>
                                        <div class="flex justify-end">
                                            <button type="submit"
                                                class="bg-gradient-to-r from-brand-500 to-accent-500 text-white px-6 py-3 rounded-xl font-medium hover:from-brand-600 hover:to-accent-600 transition-all duration-300">
                                                Відправити
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="mt-8 pt-8 text-center">
                                    <div
                                        class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                                        <i class="fas fa-lock text-red-500 dark:text-red-400 text-2xl mb-2"></i>
                                        <p class="text-red-600 dark:text-red-400">Це обговорення закрито для нових відповідей
                                        </p>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="mt-8 pt-8 text-center">
                                <p class="text-light-text-secondary dark:text-dark-text-secondary mb-4">Увійдіть в систему, щоб
                                    відповісти на обговорення</p>
                                <a href="{{ route('login') }}"
                                    class="bg-gradient-to-r from-brand-500 to-accent-500 text-white px-6 py-3 rounded-xl font-medium hover:from-brand-600 hover:to-accent-600 transition-all duration-300">
                                    Войти
                                </a>
                            </div>
                        @endauth
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <span class="text-lg font-bold text-light-text-primary dark:text-dark-text-primary">
                                Відповіді ({{ $discussion->replies_count }})
                            </span>
                        </div>

                        @if ($discussion->replies_count > 0)
                            <div class="space-y-6">
                                @foreach ($replies as $reply)
                                    @include('discussions.partials.reply', [
                                        'reply' => $reply,
                                        'level' => 0,
                                    ])
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto mb-4 text-light-text-tertiary dark:text-dark-text-tertiary"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                    </path>
                                </svg>
                                <h3
                                    class="text-xl font-semibold text-light-text-secondary dark:text-dark-text-secondary mb-2">
                                    Поки що немає відповідей</h3>
                                <p class="text-light-text-tertiary dark:text-dark-text-tertiary">Ставте першим, хто
                                    відповість на це обговорення!</p>
                            </div>
                        @endif
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
                                <span class="text-light-text-primary dark:text-dark-text-primary font-medium">
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
                // Simple and reliable toggle function - define first
                window.toggleReplies = function(replyId) {
                    console.log(`toggleReplies called for reply: ${replyId}`);

                    const container = document.getElementById(`repliesContainer${replyId}`);
                    const icon = document.getElementById(`toggleIcon${replyId}`);
                    const text = document.getElementById(`toggleText${replyId}`);

                    console.log('Container:', container);
                    console.log('Icon:', icon);
                    console.log('Text:', text);

                    if (!container) {
                        console.error(`Container not found for reply: ${replyId}`);
                        return;
                    }

                    if (!icon) {
                        console.error(`Icon not found for reply: ${replyId}`);
                        console.log('Looking for icon with ID:', `toggleIcon${replyId}`);
                        // Try to find the icon element differently
                        const button = document.getElementById(`toggleBtn${replyId}`);
                        console.log('Button found:', button);
                        if (button) {
                            const iconFromButton = button.querySelector('i');
                            console.log('Icon from button:', iconFromButton);
                            if (iconFromButton) {
                                // Use the icon from button
                                if (container.style.display === 'none') {
                                    container.style.display = 'block';
                                    iconFromButton.className = 'fas fa-chevron-down toggle-icon';
                                    const textFromButton = button.querySelector('span span');
                                    if (textFromButton) {
                                        textFromButton.textContent = 'Згорнути';
                                    }
                                } else {
                                    container.style.display = 'none';
                                    iconFromButton.className = 'fas fa-chevron-right toggle-icon';
                                    const textFromButton = button.querySelector('span span');
                                    if (textFromButton) {
                                        textFromButton.textContent = 'Відповіді';
                                    }
                                }
                                return;
                            }
                        }
                        return;
                    }

                    if (!text) {
                        console.error(`Text not found for reply: ${replyId}`);
                        return;
                    }

                    // Simple display toggle
                    if (container.style.display === 'none') {
                        // Show replies
                        container.style.display = 'block';
                        icon.className = 'fas fa-chevron-down toggle-icon';
                        text.textContent = 'Згорнути';
                    } else {
                        // Hide replies
                        container.style.display = 'none';
                        icon.className = 'fas fa-chevron-right toggle-icon';
                        text.textContent = 'Відповіді';
                    }
                };

                // Global reply management
                function collapseAllReplies() {
                    const containers = document.querySelectorAll('[id^="repliesContainer"]');
                    containers.forEach(container => {
                        const replyId = container.id.replace('repliesContainer', '');
                        const button = document.getElementById(`toggleBtn${replyId}`);

                        if (button && container.style.display !== 'none') {
                            const icon = button.querySelector('i');
                            const textSpan = button.querySelector('span span');

                            if (icon && textSpan) {
                                container.style.display = 'none';
                                icon.className = 'fas fa-chevron-right toggle-icon';
                                textSpan.textContent = 'Відповіді';
                            }
                        }
                    });
                }

                function expandAllReplies() {
                    const containers = document.querySelectorAll('[id^="repliesContainer"]');
                    containers.forEach(container => {
                        const replyId = container.id.replace('repliesContainer', '');
                        const button = document.getElementById(`toggleBtn${replyId}`);

                        if (button && container.style.display === 'none') {
                            const icon = button.querySelector('i');
                            const textSpan = button.querySelector('span span');

                            if (icon && textSpan) {
                                container.style.display = 'block';
                                icon.className = 'fas fa-chevron-down toggle-icon';
                                textSpan.textContent = 'Згорнути';
                            }
                        }
                    });
                }

                // toggleReplies function is defined above

                // Initialize after DOM is loaded
                document.addEventListener('DOMContentLoaded', function() {
                    console.log('DOM loaded, toggleReplies function available:', typeof toggleReplies);

                    // Handle main reply form submission
                    const mainReplyForm = document.getElementById('replyForm');
                    if (mainReplyForm) {
                        // Remove any existing listeners to prevent duplicates
                        mainReplyForm.removeEventListener('submit', mainReplyForm._submitHandler);

                        mainReplyForm._submitHandler = function(e) {
                            e.preventDefault();

                            const formData = new FormData(this);
                            const textarea = this.querySelector('textarea');
                            const submitButton = this.querySelector('button[type="submit"]');

                            // Disable form during submission
                            submitButton.disabled = true;
                            submitButton.textContent = 'Відправляємо...';

                            // Check if CSRF token exists
                            const csrfToken = document.querySelector('meta[name="csrf-token"]');
                            if (!csrfToken) {
                                console.error('CSRF token not found');
                                showNotification('Помилка безпеки. Спробуйте перезавантажити сторінку.', 'error');
                                submitButton.disabled = false;
                                submitButton.textContent = 'Відправити';
                                return;
                            }

                            // Validate form data
                            if (!textarea.value.trim()) {
                                showNotification('Будь ласка, введіть текст відповіді', 'error');
                                textarea.focus();
                                submitButton.disabled = false;
                                submitButton.textContent = 'Відправити';
                                return;
                            }

                            fetch(this.action, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                    body: formData
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        // Clear form
                                        textarea.value = '';

                                        // Add new reply to main replies section
                                        addMainReplyToDOM(data.reply);

                                        // Update discussion replies count
                                        updateDiscussionRepliesCount();

                                        // Show success message
                                        showNotification('Відповідь успішно додано!', 'success');
                                    } else {
                                        showNotification(data.message || 'Помилка при відправці відповіді',
                                            'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    showNotification(
                                        'Помилка при відправці відповіді. Перевірте підключення до інтернету.',
                                        'error');
                                })
                                .finally(() => {
                                    // Re-enable form
                                    submitButton.disabled = false;
                                    submitButton.textContent = 'Відправити';
                                });
                        };

                        mainReplyForm.addEventListener('submit', mainReplyForm._submitHandler);
                    }

                    // Add event delegation for toggle buttons
                    document.addEventListener('click', function(e) {
                        if (e.target.closest('.toggle-button')) {
                            const button = e.target.closest('.toggle-button');
                            const replyId = button.getAttribute('data-reply-id');
                            if (replyId) {
                                console.log('Toggle button clicked via delegation for reply:', replyId);
                                e.preventDefault();
                                e.stopPropagation();

                                // Direct toggle without relying on ID searches
                                const container = document.getElementById(`repliesContainer${replyId}`);
                                const icon = button.querySelector('i');
                                const textSpan = button.querySelector('span span');

                                if (container && icon && textSpan) {
                                    if (container.style.display === 'none') {
                                        container.style.display = 'block';
                                        icon.className = 'fas fa-chevron-down toggle-icon';
                                        textSpan.textContent = 'Згорнути';
                                    } else {
                                        container.style.display = 'none';
                                        icon.className = 'fas fa-chevron-right toggle-icon';
                                        textSpan.textContent = 'Відповіді';
                                    }
                                } else {
                                    console.error('Elements not found for direct toggle');
                                    console.log('Container:', container);
                                    console.log('Icon:', icon);
                                    console.log('TextSpan:', textSpan);
                                }
                            }
                        }
                    });
                });

                // Add main reply to DOM
                function addMainReplyToDOM(replyData) {
                    const repliesSection = document.querySelector('#replies-section');
                    if (!repliesSection) {
                        console.error('Replies section not found');
                        return;
                    }

                    // Create new reply element
                    const replyElement = createMainReplyElement(replyData);

                    // Add to the beginning of replies section
                    const existingReplies = repliesSection.querySelector('.space-y-6');
                    if (existingReplies) {
                        existingReplies.insertBefore(replyElement, existingReplies.firstChild);
                    } else {
                        repliesSection.appendChild(replyElement);
                    }

                    // Smooth scroll to new reply
                    replyElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }

                // Create main reply element from data
                function createMainReplyElement(replyData) {
                    const currentUserId = {{ auth()->id() ?? 'null' }};
                    const isDiscussionClosed = {{ $discussion->is_closed ? 'true' : 'false' }};

                    const replyDiv = document.createElement('div');
                    replyDiv.className = 'discussion-reply';
                    replyDiv.setAttribute('data-reply-id', replyData.id);
                    replyDiv.setAttribute('data-depth', '0');

                    let actionsHtml = '';

                    if (currentUserId) {
                        actionsHtml = `
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <!-- Like Button -->
                    <button onclick="toggleReplyLike(${replyData.id})"
                        class="flex items-center py-1 rounded-lg transition-colors" id="replyLikeButton${replyData.id}">
                        <i class="fas fa-heart text-xs sm:text-sm text-light-text-tertiary dark:text-dark-text-tertiary"></i>
                        <span class="ml-1 text-light-text-tertiary dark:text-dark-text-tertiary text-xs sm:text-sm"
                            id="replyLikeCount${replyData.id}">0</span>
                    </button>
                </div>
                
                <div class="flex items-center space-x-2">
                    <!-- Reply Button -->
                    ${!isDiscussionClosed ? `
                                                <button onclick="showReplyForm(${replyData.id})"
                                                    class="flex items-center space-x-1 text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 transition-colors text-xs sm:text-sm">
                                                    <i class="fas fa-reply text-xs"></i>
                                                    <span>Відповісти</span>
                                                </button>
                                            ` : ''}
                    
                    <!-- Edit/Delete menu for owner -->
                    ${replyData.user_id === currentUserId ? `
                                                <div class="relative group">
                                                    <button
                                                        class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary transition-colors p-1">
                                                        <i class="fas fa-ellipsis-h text-xs"></i>
                                                    </button>
                                                    <div
                                                        class="absolute right-0 top-8 bg-white dark:bg-gray-800 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10 min-w-32">
                                                        <div class="py-2">
                                                            <button onclick="editReply(${replyData.id})"
                                                                class="block w-full text-left px-3 py-2 text-sm text-light-text-secondary dark:text-dark-text-secondary hover:bg-light-bg-secondary dark:hover:bg-gray-700 hover:text-light-text-primary dark:hover:text-dark-text-primary">
                                                                Редагувати
                                                            </button>
                                                            <button onclick="deleteReply(${replyData.id})"
                                                                class="block w-full text-left px-3 py-2 text-sm text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                                Вилучити
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            ` : ''}
                </div>
            </div>
        `;
                    }

                    replyDiv.innerHTML = `
        <div class="rounded-xl p-0 py-1 sm:py-2 px-1 sm:px-2">
            <!-- Mobile: Compact header -->
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <img src="${replyData.user.avatar_display}" alt="${replyData.user.name}" 
                         class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex-shrink-0">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center space-x-2">
                            <div class="text-light-text-primary dark:text-dark-text-primary font-medium text-sm sm:text-base truncate">
                                ${replyData.user.name}
                            </div>
                            <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs sm:text-sm">
                                щойно
                            </div>
                        </div>
                        <a href="/users/${replyData.user.username}"
                            class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs sm:text-sm truncate block">
                            @${replyData.user.username}
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Reply Content -->
            <div class="text-light-text-primary dark:text-dark-text-primary leading-relaxed mb-3 sm:mb-4 whitespace-pre-wrap text-sm sm:text-base">
                ${replyData.content}
            </div>
            
            ${actionsHtml}
        </div>
    `;

                    return replyDiv;
                }

                // Update discussion replies count
                function updateDiscussionRepliesCount() {
                    const repliesCountElement = document.querySelector('.flex.items-center.space-x-2 .fas.fa-comments')
                        .parentElement;
                    if (repliesCountElement) {
                        const countSpan = repliesCountElement.querySelector('span');
                        if (countSpan) {
                            const currentCount = parseInt(countSpan.textContent.match(/(\d+)/)?.[1] || '0');
                            countSpan.textContent = `${currentCount + 1} Відповідей`;
                        }
                    }
                }

                // Show notification
                function showNotification(message, type = 'info') {
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
                    notification.textContent = message;

                    document.body.appendChild(notification);

                    // Auto remove after 3 seconds
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        notification.style.transform = 'translateX(100%)';
                        setTimeout(() => {
                            document.body.removeChild(notification);
                        }, 300);
                    }, 3000);
                }

                // Like functionality
                function toggleLike(discussionId) {
                    fetch(`/discussions/${discussionId}/like`, {
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
                                const button = document.getElementById(`likeButton${discussionId}`);
                                const count = document.getElementById(`likeCount${discussionId}`);
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

                // Reply form submission
                document.getElementById('replyForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const form = this;
                    const formData = new FormData(form);

                    fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message || 'Ошибка при отправке ответа');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Ошибка при отправке ответа');
                        });
                });
            </script>
        @endpush
    @endsection
