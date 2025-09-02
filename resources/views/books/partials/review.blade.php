<div class="review-item bg-white/60 dark:bg-slate-800/60 backdrop-blur-sm rounded-2xl p-6 border border-white/30 dark:border-slate-700/30 hover:bg-white/80 dark:hover:bg-slate-800/80 transition-all duration-300 hover:shadow-lg" data-review-id="{{ $review->id }}">
    <div class="flex space-x-6">
        <!-- Like/Dislike Section -->
        <div class="flex flex-col items-center space-y-3">
            <!-- Like Button -->
            <button onclick="likeReview({{ $review->id }})" 
                    class="like-btn p-3 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-300 {{ auth()->check() && $review->isLikedBy(auth()->id()) ? 'text-red-500 bg-red-50 dark:bg-red-900/20' : 'text-slate-400 hover:text-red-500 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                <svg class="w-6 h-6" fill="{{ auth()->check() && $review->isLikedBy(auth()->id()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
            
            <div class="likes-count text-lg font-bold text-slate-700 dark:text-slate-300">
                {{ $review->likes_count ?? 0 }}
            </div>
            
            <!-- Dislike Button -->
            <button onclick="dislikeReview({{ $review->id }})" 
                    class="dislike-btn p-3 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-300 {{ auth()->check() && $review->isDislikedBy(auth()->id()) ? 'text-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'text-slate-400 hover:text-blue-500 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                <svg class="w-6 h-6" fill="{{ auth()->check() && $review->isDislikedBy(auth()->id()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.912c.163 0 .322.028.475.082l3.276 1.5c.337.154.563.504.563.877v6.541c0 .373-.226.723-.563.877l-3.276 1.5a2 2 0 01-.475.082H10V14z"/>
                </svg>
            </button>
            
            <div class="dislikes-count text-lg font-bold text-slate-700 dark:text-slate-300">
                {{ $review->dislikes_count ?? 0 }}
            </div>
        </div>

        <!-- Review Content -->
        <div class="flex-1 min-w-0">
            <!-- Review Header -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center space-x-4">
                    @if($review->isGuest())
                        <!-- Guest Avatar -->
                        <div class="w-12 h-12 bg-gradient-to-br from-slate-400 to-slate-600 rounded-2xl flex items-center justify-center relative shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-orange-500 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-xs text-white font-bold">Г</span>
                            </div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-slate-900 dark:text-white flex items-center">
                                {{ $review->getAuthorName() }}
                                <span class="ml-3 px-3 py-1 text-sm bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 rounded-full font-semibold">Гость</span>
                            </div>
                            <div class="text-base text-slate-500 dark:text-slate-400 font-medium">{{ $review->created_at->diffForHumans() }}</div>
                        </div>
                    @else
                        <!-- User Avatar -->
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <span class="text-lg font-bold text-white">{{ substr($review->getAuthorName(), 0, 1) }}</span>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-slate-900 dark:text-white">{{ $review->getAuthorName() }}</div>
                            <div class="text-base text-slate-500 dark:text-slate-400 font-medium">{{ $review->created_at->diffForHumans() }}</div>
                        </div>
                    @endif
                </div>
                
                <!-- Rating Stars (только для основных рецензий) -->
                @if($review->rating && !$review->isReply())
                    <div class="flex items-center space-x-2 bg-slate-50 dark:bg-slate-700/50 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-600">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-300 dark:text-slate-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                        <span class="ml-3 text-lg font-bold text-slate-700 dark:text-slate-300">{{ $review->rating }}/5</span>
                    </div>
                @endif
            </div>

            <!-- Review Text -->
            <div class="prose prose-slate dark:prose-invert max-w-none mb-6">
                <p class="text-lg text-slate-700 dark:text-slate-300 leading-relaxed font-medium">{{ $review->content }}</p>
            </div>

            <!-- Review Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-700">
                <div class="flex items-center space-x-6">
                    <button class="flex items-center space-x-3 text-base text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-300 font-semibold hover:scale-105" 
                            onclick="toggleReplyForm({{ $review->id }})">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>Ответить</span>
                    </button>
                    
                    @if(auth()->check() && auth()->id() === $review->user_id)
                        <button class="flex items-center space-x-3 text-base text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-300 font-semibold hover:scale-105" 
                                onclick="editReview({{ $review->id }})">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Редактировать</span>
                        </button>
                        
                        <button class="flex items-center space-x-3 text-base text-slate-600 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-all duration-300 font-semibold hover:scale-105" 
                                onclick="deleteReview({{ $review->id }})">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Удалить</span>
                        </button>
                    @endif
                </div>
                
                <!-- Replies Count -->
                @if($review->replies_count > 0)
                    <div class="flex items-center space-x-2 text-slate-500 dark:text-slate-400 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>{{ $review->replies_count }} {{ $review->replies_count == 1 ? 'ответ' : ($review->replies_count < 5 ? 'ответа' : 'ответов') }}</span>
                    </div>
                @endif
            </div>

            <!-- Reply Form -->
            <div id="replyForm{{ $review->id }}" class="hidden mt-6 p-6 bg-slate-50 dark:bg-slate-700/50 rounded-2xl border border-slate-200 dark:border-slate-600">
                <h5 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Ваш ответ</h5>
                <form onsubmit="submitReply(event, {{ $review->id }}, null)" class="space-y-4">
                    @csrf
                    <textarea name="content" rows="3" class="w-full px-4 py-3 text-base border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-4 focus:ring-indigo-500 focus:border-transparent dark:bg-slate-600 dark:text-white resize-none" placeholder="Напишите ваш ответ..." required></textarea>
                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="toggleReplyForm({{ $review->id }})" class="px-6 py-3 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors font-semibold">Отмена</button>
                        <button type="submit" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-3 rounded-xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-lg hover:shadow-xl">
                            Ответить
                        </button>
                    </div>
                </form>
            </div>

            <!-- Replies Section -->
            @if($review->replies_count > 0)
                <div class="mt-6">
                    <button id="replyToggle{{ $review->id }}" 
                            class="reply-toggle-btn flex items-center space-x-2 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-300 font-semibold">
                        <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <span>Показать ответы ({{ $review->replies_count }})</span>
                    </button>
                    
                    <div id="repliesContainer{{ $review->id }}" class="hidden mt-4">
                        <div id="repliesContent{{ $review->id }}" class="space-y-4">
                            @if($review->replies && $review->replies->count() > 0)
                                @include('books.partials.replies', ['replies' => $review->replies, 'depth' => 0])
                            @endif
                        </div>
                        
                        @if($review->replies_count > 10)
                            <div class="mt-4 text-center">
                                <button onclick="loadMoreReplies({{ $review->id }})" 
                                        class="bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 px-6 py-3 rounded-xl font-semibold hover:bg-slate-200 dark:hover:bg-slate-600 transition-all duration-300">
                                    Загрузить еще
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>