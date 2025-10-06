@php
    $level = $level ?? 0;
@endphp

<div class="discussion-reply" data-reply-id="{{ $reply->id }}" data-depth="{{ $level }}">
    <div class="rounded-xl p-0 py-1 sm:py-2 px-1 sm:px-2">
        <!-- Mobile: Compact header -->
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center space-x-2 sm:space-x-3">
                <img src="{{ $reply->user->avatar_display }}" alt="{{ $reply->user->name }}"
                    class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex-shrink-0">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center space-x-2">
                        <div
                            class="text-light-text-primary dark:text-dark-text-primary font-medium text-sm sm:text-base truncate">
                            {{ $reply->user->name }}
                        </div>
                        <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs sm:text-sm">
                            {{ $reply->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <a href="{{ route('users.public.profile', $reply->user->username) }}"
                        class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs sm:text-sm truncate block">
                        {{ '@' . $reply->user->username }}
                    </a>
                </div>
            </div>

        </div>
        <!-- Reply Content -->
        <div class="text-light-text-primary dark:text-dark-text-primary leading-relaxed mb-3 sm:mb-4 whitespace-pre-wrap text-sm sm:text-base"
            id="replyContent{{ $reply->id }}">{{ $reply->content }}</div>

        @auth
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <!-- Toggle Button -->
                    @if ($reply->replies->count() > 0)
                        <button onclick="toggleReplies({{ $reply->id }})"
                            class="toggle-button flex items-center space-x-1 sm:space-x-2 text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary text-xs sm:text-sm"
                            id="toggleBtn{{ $reply->id }}" data-reply-id="{{ $reply->id }}">
                            <i class="fas fa-chevron-right toggle-icon text-xs" id="toggleIcon{{ $reply->id }}"></i>
                            <span class="font-medium">
                                <span id="toggleText{{ $reply->id }}">Відповіді</span>
                                <span
                                    class="text-light-text-tertiary dark:text-dark-text-tertiary">({{ $reply->replies->count() }})</span>
                            </span>
                        </button>
                    @endif

                    <!-- Like Button -->
                    <button onclick="toggleReplyLike({{ $reply->id }})"
                        class="flex items-center py-1 rounded-lg transition-colors"
                        id="replyLikeButton{{ $reply->id }}">
                        <i
                            class="fas fa-heart text-xs sm:text-sm {{ $reply->isLikedBy(Auth::id()) ? 'text-red-500 dark:text-red-400' : 'text-light-text-tertiary dark:text-dark-text-tertiary' }}"></i>
                        <span class="ml-1 text-light-text-tertiary dark:text-dark-text-tertiary text-xs sm:text-sm"
                            id="replyLikeCount{{ $reply->id }}">{{ $reply->likes_count }}</span>
                    </button>
                </div>

                <div class="flex items-center space-x-2">
                    <!-- Reply Button -->
                    @if (!$discussion->is_closed)
                        <button onclick="showReplyForm({{ $reply->id }})"
                            class="flex items-center space-x-1 text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 transition-colors text-xs sm:text-sm">
                            <i class="fas fa-reply text-xs"></i>
                            <span>Відповісти</span>
                        </button>
                    @endif

                    <!-- Edit/Delete menu for owner -->
                    @if ($reply->user_id === Auth::id() || Auth::user()->isModerator())
                        <div class="relative group">
                            <button
                                class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary transition-colors p-1">
                                <i class="fas fa-ellipsis-h text-xs"></i>
                            </button>
                            <div
                                class="absolute right-0 top-8 bg-white dark:bg-gray-800 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10 min-w-32">
                                <div class="py-2">
                                    @if ($reply->user_id === Auth::id())
                                        <button onclick="editReply({{ $reply->id }})"
                                            class="block w-full text-left px-3 py-2 text-sm text-light-text-secondary dark:text-dark-text-secondary hover:bg-light-bg-secondary dark:hover:bg-gray-700 hover:text-light-text-primary dark:hover:text-dark-text-primary">
                                            Редагувати
                                        </button>
                                        <button onclick="deleteReply({{ $reply->id }})"
                                            class="block w-full text-left px-3 py-2 text-sm text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                            Вилучити
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endauth

        <!-- Edit Form (hidden by default) -->
        <div id="editForm{{ $reply->id }}" class="hidden mb-3 sm:mb-4">
            <textarea id="editContent{{ $reply->id }}"
                class="w-full p-3 sm:p-4 bg-light-bg-secondary dark:bg-gray-700 rounded-xl text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none transition-colors text-sm sm:text-base"
                rows="3">{{ $reply->content }}</textarea>
            <div class="flex justify-end space-x-2 mt-3">
                <button onclick="cancelEdit({{ $reply->id }})"
                    class="px-3 py-2 sm:px-4 bg-gray-500 dark:bg-gray-600 text-white rounded-lg hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors text-sm">
                    Скасувати
                </button>
                <button onclick="saveEdit({{ $reply->id }})"
                    class="px-3 py-2 sm:px-4 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors text-sm">
                    Зберегти
                </button>
            </div>
        </div>

        <!-- Reply Form (hidden by default) -->
        <div id="replyForm{{ $reply->id }}" class="hidden mb-3 sm:mb-4">
            <form id="nestedReplyForm{{ $reply->id }}"
                action="{{ route('discussions.replies.store', $discussion) }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                <textarea name="content" id="replyContent{{ $reply->id }}"
                    class="w-full p-3 sm:p-4 bg-light-bg-secondary dark:bg-gray-700 rounded-xl text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none transition-colors text-sm sm:text-base"
                    rows="3" placeholder="Напишіть відповідь..." required></textarea>
                <div class="flex justify-end space-x-2 mt-3">
                    <button type="button" onclick="cancelReply({{ $reply->id }})"
                        class="px-3 py-2 sm:px-4 bg-gray-500 dark:bg-gray-600 text-white rounded-lg hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors text-sm">
                        Скасувати
                    </button>
                    <button type="button" onclick="submitReplyDisscussion({{ $reply->id }})"
                        class="px-3 py-2 sm:px-4 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors text-sm">
                        Відправити
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Nested Replies -->
    @if ($reply->replies->count() > 0)
        <div class="mt-4">

            <!-- Replies Container -->
            <div class="space-y-4 replies-container" id="repliesContainer{{ $reply->id }}" style="display: none;">
                @foreach ($reply->replies as $nestedReply)
                    @include('discussions.partials.reply', [
                        'reply' => $nestedReply,
                        'level' => $level + 1,
                    ])
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('styles')
    <style>
        /* Discussion Reply Styles */
        .discussion-reply {
            margin-bottom: 1rem;
            transition: all 0.2s ease-in-out;
        }


        .replies-container {
            transition: all 0.3s ease-in-out;
        }

        .replies-container.collapsed {
            max-height: 0;
            opacity: 0;
            margin: 0;
        }

        .replies-container.expanded {
            max-height: 1000px;
            opacity: 1;
        }

        .toggle-button {
            transition: all 0.2s ease-in-out;
        }

        .toggle-icon {
            transition: transform 0.2s ease-in-out;
        }

        .toggle-icon.rotated {
            transform: rotate(-90deg);
        }

        /* Depth Indicators for Visual Hierarchy (like reviews) */
        .discussion-reply[data-depth="1"] {
            border-left: 1px solid hsl(220, 70%, 50%);
            padding-left: 0.5rem;
        }

        .discussion-reply[data-depth="2"] {
            border-left: 1px solid hsl(160, 70%, 50%);
            padding-left: 0.5rem;
        }

        .discussion-reply[data-depth="3"] {
            border-left: 1px solid hsl(30, 70%, 50%);
            padding-left: 0.5rem;
        }

        .discussion-reply[data-depth="4"] {
            border-left: 1px solid hsl(280, 70%, 50%);
            padding-left: 0.5rem;
        }

        .discussion-reply[data-depth="5"] {
            border-left: 1px solid hsl(0, 70%, 50%);
            padding-left: 0.5rem;
        }

        /* Deep nesting indicator */
        .discussion-reply[data-depth="6"] {
            border-left: 1px solid hsl(220, 70%, 50%);
            padding-left: 0.5rem;
            background: hsl(220, 70%, 5%);
        }

        .dark .discussion-reply[data-depth="6"] {
            background: hsl(220, 70%, 10%);
        }

        /* Mobile optimizations */
        @media (max-width: 640px) {
            .discussion-reply {
                margin-bottom: 0.5rem;
            }

            .discussion-reply .rounded-xl {
                padding: 0.5rem;
            }

            .discussion-reply img {
                width: 1.5rem;
                height: 1.5rem;
            }

            .discussion-reply .text-sm {
                font-size: 0.75rem;
            }

            .discussion-reply .text-xs {
                font-size: 0.625rem;
            }

            /* Adjust depth indicators for mobile */
            .discussion-reply[data-depth="1"],
            .discussion-reply[data-depth="2"],
            .discussion-reply[data-depth="3"],
            .discussion-reply[data-depth="4"],
            .discussion-reply[data-depth="5"] {
                border-left-width: 1px;
                padding-left: 0.25rem;
            }

            /* Prevent button overlap on mobile */
            .discussion-reply .flex.items-center.justify-between {
                gap: 0.5rem;
            }

            .discussion-reply .flex.items-center.space-x-2 {
                gap: 0.25rem;
            }

            /* Ensure dropdown menu doesn't overflow */
            .discussion-reply .relative.group .absolute {
                right: -0.5rem;
                top: 1.5rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Initialize form handlers when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            setupFormHandlers();
        });

        // toggleReplies function is defined in show.blade.php

        // Reply like functionality
        function toggleReplyLike(replyId) {
            fetch(`/discussions/{{ $discussion->id }}/replies/${replyId}/like`, {
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
                        const button = document.getElementById(`replyLikeButton${replyId}`);
                        const count = document.getElementById(`replyLikeCount${replyId}`);
                        const icon = button.querySelector('i');

                        if (data.liked) {
                            icon.classList.remove('text-slate-400');
                            icon.classList.add('text-red-500');
                        } else {
                            icon.classList.remove('text-red-500');
                            icon.classList.add('text-slate-400');
                        }

                        count.textContent = data.likes_count;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Show reply form
        function showReplyForm(replyId) {
            const form = document.getElementById(`replyForm${replyId}`);
            form.classList.remove('hidden');
            const textarea = form.querySelector('textarea[name="content"]');
            if (textarea) {
                textarea.focus();
            }
        }

        // Cancel reply
        function cancelReply(replyId) {
            const form = document.getElementById(`replyForm${replyId}`);
            form.classList.add('hidden');
            const textarea = form.querySelector('textarea[name="content"]');
            if (textarea) {
                textarea.value = '';
            }
        }

        // Simple submit reply function
        function submitReplyDisscussion(replyId) {
            const form = document.getElementById(`nestedReplyForm${replyId}`);
            const textarea = form.querySelector('textarea[name="content"]');
            const submitButton = form.querySelector('button[type="button"]');

            if (!textarea || !textarea.value.trim()) {
                alert('Будь ласка, введіть текст відповіді');
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = 'Відправляємо...';

            const formData = new FormData();
            formData.append('content', textarea.value);
            formData.append('parent_id', replyId);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        textarea.value = '';
                        form.classList.add('hidden');

                        // Add new reply to DOM
                        addReplyToDOM(data.reply, replyId);

                        showNotification('Відповідь успішно додано!', 'success');
                    } else {
                        alert(data.message || 'Помилка при відправці відповіді');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Помилка при відправці відповіді');
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Відправити';
                });
        }

        // Add new reply to DOM
        function addReplyToDOM(replyData, parentReplyId) {
            let repliesContainer = document.getElementById(`repliesContainer${parentReplyId}`);

            // If container doesn't exist, create it
            if (!repliesContainer) {
                const parentReply = document.querySelector(`[data-reply-id="${parentReplyId}"]`);
                if (!parentReply) {
                    console.error('Parent reply not found:', parentReplyId);
                    return;
                }

                // Create container div
                repliesContainer = document.createElement('div');
                repliesContainer.className = 'space-y-4 replies-container';
                repliesContainer.id = `repliesContainer${parentReplyId}`;
                repliesContainer.style.display = 'block';

                // Find where to insert it (after the main reply content)
                const insertPoint = parentReply.querySelector('.rounded-xl');
                if (insertPoint && insertPoint.parentNode) {
                    insertPoint.parentNode.appendChild(repliesContainer);
                } else {
                    parentReply.appendChild(repliesContainer);
                }

                // Create and show toggle button if it doesn't exist
                let toggleButton = document.getElementById(`toggleBtn${parentReplyId}`);
                if (!toggleButton) {
                    const actionsDiv = parentReply.querySelector('.flex.items-center.justify-between');
                    if (actionsDiv) {
                        const toggleHtml = `
                            <div class="flex items-center space-x-3 sm:space-x-4">
                                <button onclick="toggleReplies(${parentReplyId})"
                                    class="toggle-button flex items-center space-x-1 sm:space-x-2 text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary text-xs sm:text-sm"
                                    id="toggleBtn${parentReplyId}" data-reply-id="${parentReplyId}">
                                    <i class="fas fa-chevron-down toggle-icon text-xs" id="toggleIcon${parentReplyId}"></i>
                                    <span class="font-medium">
                                        <span id="toggleText${parentReplyId}">Згорнути</span>
                                        <span class="text-light-text-tertiary dark:text-dark-text-tertiary">(1)</span>
                                    </span>
                                </button>
                            </div>
                        `;
                        
                        const likeButton = actionsDiv.querySelector('.flex.items-center.space-x-3');
                        if (likeButton) {
                            likeButton.insertAdjacentHTML('beforebegin', toggleHtml);
                        }
                    }
                }
            }

            // Create new reply element
            const replyElement = createReplyElement(replyData, parentReplyId);

            // Add to container at the beginning (newest first)
            repliesContainer.insertBefore(replyElement, repliesContainer.firstChild);

            // Update toggle button text and count
            const toggleButton = document.getElementById(`toggleBtn${parentReplyId}`);
            if (toggleButton) {
                const countSpan = toggleButton.querySelector('.text-light-text-tertiary');
                if (countSpan) {
                    const currentCount = parseInt(countSpan.textContent.match(/\((\d+)\)/)?.[1] || '0');
                    countSpan.textContent = `(${currentCount + 1})`;
                }
            }

            // Smooth scroll to new reply
            replyElement.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }

        // Create reply element from data
        function createReplyElement(replyData, parentReplyId) {
            const level = getReplyLevel(parentReplyId) + 1;
            const currentUserId = {{ auth()->id() ?? 'null' }};
            const isDiscussionClosed = {{ $discussion->is_closed ? 'true' : 'false' }};

            const replyDiv = document.createElement('div');
            replyDiv.className = 'discussion-reply';
            replyDiv.setAttribute('data-reply-id', replyData.id);
            replyDiv.setAttribute('data-depth', level);

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

            replyDiv.innerHTML = `<div class="rounded-xl p-0 py-1 sm:py-2 px-1 sm:px-2">
<div class="flex items-center justify-between mb-2">
<div class="flex items-center space-x-2 sm:space-x-3">
<img src="${replyData.user.avatar || replyData.user.avatar_display || '/storage/avatars/default.png'}" alt="${replyData.user.name}" class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex-shrink-0">
<div class="min-w-0 flex-1">
<div class="flex items-center space-x-2">
<div class="text-light-text-primary dark:text-dark-text-primary font-medium text-sm sm:text-base truncate">${replyData.user.name}</div>
<div class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs sm:text-sm">щойно</div>
</div>
<a href="/users/${replyData.user.username}" class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs sm:text-sm truncate block">@${replyData.user.username}</a>
</div>
</div>
</div>
<div class="text-light-text-primary dark:text-dark-text-primary leading-relaxed mb-3 sm:mb-4 whitespace-pre-wrap text-sm sm:text-base" id="replyContent${replyData.id}">${replyData.content}</div>
${actionsHtml}
</div>
<div id="editForm${replyData.id}" class="hidden mb-3 sm:mb-4">
<textarea id="editContent${replyData.id}" class="w-full p-3 sm:p-4 bg-light-bg-secondary dark:bg-gray-700 rounded-xl text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none transition-colors text-sm sm:text-base" rows="3">${replyData.content}</textarea>
<div class="flex justify-end space-x-2 mt-3">
<button onclick="cancelEdit(${replyData.id})" class="px-3 py-2 sm:px-4 bg-gray-500 dark:bg-gray-600 text-white rounded-lg hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors text-sm">Скасувати</button>
<button onclick="saveEdit(${replyData.id})" class="px-3 py-2 sm:px-4 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors text-sm">Зберегти</button>
</div>
</div>
<div id="replyForm${replyData.id}" class="hidden mb-3 sm:mb-4">
<form id="nestedReplyForm${replyData.id}" action="{{ route('discussions.replies.store', $discussion) }}" method="POST">
<input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
<input type="hidden" name="parent_id" value="${replyData.id}">
                        <textarea name="content" id="replyContentTextarea${replyData.id}" class="w-full p-3 sm:p-4 bg-light-bg-secondary dark:bg-gray-700 rounded-xl text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none transition-colors text-sm sm:text-base" rows="3" placeholder="Напишіть відповідь..." required></textarea>
<div class="flex justify-end space-x-2 mt-3">
<button type="button" onclick="cancelReply(${replyData.id})" class="px-3 py-2 sm:px-4 bg-gray-500 dark:bg-gray-600 text-white rounded-lg hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors text-sm">Скасувати</button>
<button type="button" onclick="submitReplyDisscussion(${replyData.id})" class="px-3 py-2 sm:px-4 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors text-sm">Відправити</button>
</div>
</form>
</div>`;

            return replyDiv;
        }

        // Get reply level for proper nesting
        function getReplyLevel(replyId) {
            const replyElement = document.querySelector(`[data-reply-id="${replyId}"]`);
            if (replyElement) {
                return parseInt(replyElement.getAttribute('data-depth')) || 0;
            }
            return 0;
        }

        // Update reply count
        function updateReplyCount(replyId) {
            // This function is now handled in addReplyToDOM
            // Keeping it for compatibility but the main logic is above
        }

        // Handle nested reply form submission with event delegation
        // Use global variable to avoid redeclaration errors
        if (typeof window.formHandlersSetup === 'undefined') {
            window.formHandlersSetup = false;
        }

        function setupFormHandlers() {
            if (window.formHandlersSetup) return;

            // Since we're using type="button" now, we don't need submit event handlers
            // But we keep this function for potential future use

            window.formHandlersSetup = true;
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                padding: 12px 24px;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                ${type === 'success' ? 'background-color: #10b981;' : 
                  type === 'error' ? 'background-color: #ef4444;' : 
                  'background-color: #3b82f6;'}
            `;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    document.body.removeChild(notification);
                }
            }, 3000);
        }

        // Edit reply
        function editReply(replyId) {
            const form = document.getElementById(`editForm${replyId}`);
            const content = document.getElementById(`replyContent${replyId}`);
            form.classList.remove('hidden');
            content.classList.add('hidden');
            document.getElementById(`editContent${replyId}`).focus();
        }

        // Cancel edit
        function cancelEdit(replyId) {
            const form = document.getElementById(`editForm${replyId}`);
            const content = document.getElementById(`replyContent${replyId}`);
            form.classList.add('hidden');
            content.classList.remove('hidden');
        }

        // Save edit
        function saveEdit(replyId) {
            const content = document.getElementById(`editContent${replyId}`).value.trim();
            if (!content) return;

            fetch(`/discussions/{{ $discussion->id }}/replies/${replyId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        content: content
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Ошибка при обновлении ответа');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при обновлении ответа');
                });
        }

        // Delete reply
        function deleteReply(replyId) {
            if (!confirm('Вы уверены, что хотите удалить этот ответ?')) return;

            fetch(`/discussions/{{ $discussion->id }}/replies/${replyId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Ошибка при удалении ответа');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при удалении ответа');
                });
        }
    </script>
@endpush
