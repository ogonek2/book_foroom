<div class="review-item rounded-2xl" data-review-id="{{ $review->id }}">
    <div class="flex space-x-6">
        <!-- Review Content -->
        <div class="flex-1 min-w-0">
            <!-- Review Header -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center space-x-4">
                    @if ($review->isGuest())
                        <!-- Guest Avatar -->
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-slate-400 to-slate-600 rounded-2xl flex items-center justify-center relative shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div
                                class="absolute -bottom-1 -right-1 w-5 h-5 bg-orange-500 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-xs text-white font-bold">Г</span>
                            </div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-slate-900 dark:text-white flex items-center">
                                {{ $review->getAuthorName() }}
                                <span
                                    class="ml-3 px-3 py-1 text-sm bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 rounded-full font-semibold">Гість</span>
                            </div>
                            <div class="text-base text-slate-500 dark:text-slate-400 font-medium">
                                {{ $review->created_at->diffForHumans() }}</div>
                        </div>
                    @else
                        <!-- User Avatar -->
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <span
                                class="text-lg font-bold text-white">{{ substr($review->getAuthorName(), 0, 1) }}</span>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-slate-900 dark:text-white">{{ $review->getAuthorName() }}
                            </div>
                            <div class="text-base text-slate-500 dark:text-slate-400 font-medium">
                                {{ $review->created_at->diffForHumans() }}</div>
                        </div>
                    @endif
                </div>

                <!-- Rating Stars (тільки для основних рецензій) -->
                @if ($review->rating && !$review->isReply())
                    <div
                        class="flex items-center space-x-2">
                        <span class="text-yellow-400 text-2xl"><i class="fas fa-star"></i></span>
                        <span
                            class="ml-3 text-lg font-bold text-slate-700 dark:text-slate-300">{{ $review->rating }}</span>
                    </div>
                @endif
            </div>

            <!-- Review Text -->
            <div class="prose prose-slate dark:prose-invert max-w-none mb-6">
                <p class="text-lg text-slate-700 dark:text-slate-300 leading-relaxed font-medium">
                    {{ $review->content }}</p>
            </div>


            <!-- Reply Form -->
            <div id="replyForm{{ $review->id }}"
                class="hidden mt-6 p-6 bg-slate-50 dark:bg-slate-700/50 rounded-2xl border border-slate-200 dark:border-slate-600">
                <h5 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Ваша відповідь</h5>
                <form onsubmit="submitReply(event, {{ $review->id }}, null)" class="space-y-4">
                    @csrf
                    <textarea name="content" rows="3"
                        class="w-full px-4 py-3 text-base border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-4 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-600 dark:text-white resize-none"
                        placeholder="Напишіть вашу відповідь..." required></textarea>
                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="toggleReplyForm({{ $review->id }})"
                            class="px-6 py-3 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors font-semibold">Скасувати</button>
                        <button type="submit"
                            class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-3 rounded-xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-lg hover:shadow-xl">
                            Відповісти
                        </button>
                    </div>
                </form>
            </div>

            <!-- Comments Section -->
            <div class="mt-6">
                <div class="flex items-center space-x-2">
                    <button onclick="likeReview({{ $review->id }})"
                        class="flex items-center space-x-1 text-slate-600 dark:text-slate-400 font-semibold {{ auth()->check() && $review->isLikedBy(auth()->id()) ? 'text-red-500 bg-red-50 dark:bg-red-900/20' : 'text-slate-400 hover:text-red-500 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                        <svg class="w-6 h-6"
                            fill="{{ auth()->check() && $review->isLikedBy(auth()->id()) ? 'currentColor' : 'none' }}"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span>{{ $review->likes_count ?? 0 }}</span>
                    </button>
                    <button id="commentsToggle{{ $review->id }}"
                        class="flex items-center space-x-1 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-300 font-semibold"
                        onclick="toggleComments({{ $review->id }})">
                        <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span id="commentsToggleText{{ $review->id }}">{{ $review->replies_count ?? 0 }}</span>
                    </button>
                </div>

                <div id="commentsContainer{{ $review->id }}" class="hidden mt-4">
                    <!-- Add Comment Form -->
                    <div class="p-4 mb-4">
                        <form onsubmit="submitComment(event, {{ $review->id }})" class="space-y-3">
                            @csrf
                            <textarea name="content" rows="3"
                                class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-600 dark:text-white resize-none"
                                placeholder="Напишіть ваш коментар..." required></textarea>
                            <div class="flex items-center justify-end space-x-2">
                                <button type="submit"
                                    class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-2 rounded-2xl">
                                    Додати коментар
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Comments List -->
                    <div id="commentsContent{{ $review->id }}" class="space-y-3">
                        @if ($review->replies && $review->replies->count() > 0)
                            @include('books.partials.replies', [
                                'replies' => $review->replies,
                                'depth' => 0,
                            ])
                        @endif
                    </div>

                    @if ($review->replies_count > 10)
                        <div class="mt-4 text-center">
                            <button onclick="loadMoreComments({{ $review->id }})"
                                class="bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 px-6 py-3 rounded-xl font-semibold hover:bg-slate-200 dark:hover:bg-slate-600 transition-all duration-300">
                                Завантажити ще
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
