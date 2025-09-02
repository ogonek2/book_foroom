@if($replies && $replies->count() > 0)
    <div class="replies-container">
        @foreach($replies as $reply)
            @php
                $currentDepth = $depth + 1;
                $isThirdLevel = $currentDepth == 3; // Третий уровень - ветка
                $isFourthLevel = $currentDepth >= 4; // Четвертый уровень и глубше - в один ряд
                $branchId = 'branch_' . $reply->id . '_' . $currentDepth;
            @endphp
            
            <div class="reply-item ml-2 border-l-2 border-gray-200 dark:border-gray-700 pl-2">
                <!-- Заголовок ветки с кнопкой сворачивания/разворачивания -->
                <div class="branch-header">
                    <div class="flex items-center space-x-2">
                        <!-- Avatar -->
                        @if($reply->user)
                            <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-secondary-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-white">{{ $reply->user->name[0] }}</span>
                            </div>
                        @else
                            <div class="w-8 h-8 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center flex-shrink-0 relative">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-orange-500 rounded-full flex items-center justify-center">
                                    <span class="text-xs text-white font-bold">Г</span>
                                </div>
                            </div>
                        @endif

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <!-- Header -->
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="font-medium text-gray-900 dark:text-white text-sm">
                                    {{ $reply->user ? $reply->user->name : 'Гость' }}
                                </span>
                                @if(!$reply->user)
                                    <span class="px-2 py-1 text-xs bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 rounded-full">Гость</span>
                                @endif
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $reply->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <!-- Reply Text -->
                            <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed mb-2">
                                {{ $reply->content }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Кнопка сворачивания/разворачивания ветки -->
                    @if($reply->replies && $reply->replies->count() > 0)
                        <button id="branchToggle{{ $branchId }}" 
                                onclick="toggleBranch('{{ $branchId }}')" 
                                class="branch-toggle-btn collapsed">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Развернуть
                        </button>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-3 mb-3">
                    <!-- Like/Dislike Section -->
                    <div class="flex items-center space-x-2">
                        <!-- Like Button -->
                        <button onclick="likeReview({{ $reply->id }})"
                                class="like-btn p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ auth()->check() && $reply->isLikedBy(auth()->id()) ? 'text-red-500' : 'text-gray-400 hover:text-red-500' }}">
                            <svg class="w-4 h-4" fill="{{ auth()->check() && $reply->isLikedBy(auth()->id()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        <span class="likes-count text-xs text-gray-600 dark:text-gray-400">{{ $reply->likes_count ?? 0 }}</span>

                        <!-- Dislike Button -->
                        <button onclick="dislikeReview({{ $reply->id }})"
                                class="dislike-btn p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ auth()->check() && $reply->isDislikedBy(auth()->id()) ? 'text-blue-500' : 'text-blue-400 hover:text-blue-500' }}">
                            <svg class="w-4 h-4" fill="{{ auth()->check() && $reply->isDislikedBy(auth()->id()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.912c.163 0 .322.028.475.082l3.276 1.5c.337.154.563.504.563.877v6.541c0 .373-.226.723-.563.877l-3.276 1.5a2 2 0 01-.475.082H10V14z"/>
                            </svg>
                        </button>
                        <span class="dislikes-count text-xs text-gray-600 dark:text-gray-400">{{ $reply->dislikes_count ?? 0 }}</span>
                    </div>

                    <!-- Reply Button -->
                    <button onclick="toggleReplyForm({{ $reply->id }})" 
                            class="text-xs text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                        Ответить
                    </button>
                </div>

                <!-- Reply Form -->
                <div id="replyForm{{ $reply->id }}" class="hidden mt-2 p-2 bg-gray-50 dark:bg-gray-800/30 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h5 class="text-xs font-medium text-gray-900 dark:text-white mb-2">Ваш ответ</h5>
                    <form onsubmit="submitReply(event, {{ $reply->id }}, null)" class="space-y-2">
                        @csrf
                        <textarea 
                            name="content" 
                            rows="2" 
                            class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            placeholder="Напишите ваш ответ..."
                            required
                        ></textarea>
                        <div class="flex items-center justify-end space-x-2">
                            <button type="button" onclick="toggleReplyForm({{ $reply->id }})" class="px-2 py-1 text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                                Отмена
                            </button>
                            <button type="submit" class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded transition-colors">
                                Ответить
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Содержимое ветки (ответы) -->
                @if($reply->replies && $reply->replies->count() > 0)
                    <div id="branchContent{{ $branchId }}" class="branch-content collapsed">
                        @if($isFourthLevel)
                            <!-- Четвертый уровень и глубше - собираем ВСЕ ответы всех глубин в один ряд -->
                            @php
                                $allDeepReplies = collect();
                                $collectDeepReplies = function($replies, $parentReply) use (&$collectDeepReplies, &$allDeepReplies) {
                                    if($replies && $replies->count() > 0) {
                                        foreach($replies as $deepReply) {
                                            $allDeepReplies->push([
                                                'reply' => $deepReply,
                                                'parent' => $parentReply
                                            ]);
                                            // Рекурсивно собираем ответы на ответы
                                            if($deepReply->replies && $deepReply->replies->count() > 0) {
                                                $collectDeepReplies($deepReply->replies, $deepReply);
                                            }
                                        }
                                    }
                                };
                                $collectDeepReplies($reply->replies, $reply);
                            @endphp
                            
                            @if($allDeepReplies->count() > 0)
                                <div class="flat-replies mt-3">
                                    @foreach($allDeepReplies as $item)
                                        <div class="flat-reply-item">
                                            <!-- Индикатор кому отвечено -->
                                            <div class="reply-to-indicator">
                                                Ответ для <span class="reply-to-name">{{ $item['parent']->user ? $item['parent']->user->name : 'Гость' }}</span>
                                            </div>
                                            
                                            <!-- Содержимое ответа -->
                                            <div class="flex items-start space-x-2">
                                                <!-- Avatar -->
                                                @if($item['reply']->user)
                                                    <div class="w-5 h-5 bg-gradient-to-br from-primary-500 to-secondary-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                        <span class="text-xs font-bold text-white">{{ $item['reply']->user->name[0] }}</span>
                                                    </div>
                                                @else
                                                    <div class="w-5 h-5 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center flex-shrink-0 relative">
                                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                        </svg>
                                                        <div class="absolute -bottom-0.5 -right-0.5 w-1.5 h-1.5 bg-orange-500 rounded-full flex items-center justify-center">
                                                            <span class="text-xs text-white font-bold">Г</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                <!-- Content -->
                                                <div class="flex-1 min-w-0">
                                                    <!-- Header -->
                                                    <div class="flex items-center space-x-2 mb-1">
                                                        <span class="font-medium text-gray-900 dark:text-white text-xs">
                                                            {{ $item['reply']->user ? $item['reply']->user->name : 'Гость' }}
                                                        </span>
                                                        @if(!$item['reply']->user)
                                                            <span class="px-1 py-0.5 text-xs bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 rounded-full">Гость</span>
                                                        @endif
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $item['reply']->created_at->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Reply Text -->
                                                    <p class="text-gray-700 dark:text-gray-300 text-xs leading-relaxed mb-1">
                                                        {{ $item['reply']->content }}
                                                    </p>
                                                    
                                                    <!-- Actions -->
                                                    <div class="flex items-center space-x-2">
                                                        <!-- Like/Dislike Section -->
                                                        <div class="flex items-center space-x-1">
                                                            <!-- Like Button -->
                                                            <button onclick="likeReview({{ $item['reply']->id }})"
                                                                    class="like-btn p-0.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-400 hover:text-red-500">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                                </svg>
                                                            </button>
                                                            <span class="likes-count text-xs text-gray-600 dark:text-gray-400">{{ $item['reply']->likes_count ?? 0 }}</span>
                                                            
                                                            <!-- Dislike Button -->
                                                            <button onclick="dislikeReview({{ $item['reply']->id }})"
                                                                    class="dislike-btn p-0.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-400 hover:text-blue-500">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.912c.163 0 .322.028.475.082l3.276 1.5c.337.154.563.504.563.877v6.541c0 .373-.226.723-.563.877l-3.276 1.5a2 2 0 01-.475.082H10V14z"/>
                                                                </svg>
                                                            </button>
                                                            <span class="dislikes-count text-xs text-gray-600 dark:text-gray-400">{{ $item['reply']->dislikes_count ?? 0 }}</span>
                                                        </div>
                                                        
                                                        <!-- Reply Button -->
                                                        <button onclick="toggleReplyForm({{ $item['reply']->id }})" 
                                                                class="text-xs text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                                            Ответить
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Reply Form -->
                                                    <div id="replyForm{{ $item['reply']->id }}" class="hidden mt-2 p-2 bg-gray-50 dark:bg-gray-800/30 rounded-lg border border-gray-200 dark:border-gray-700">
                                                        <h5 class="text-xs font-medium text-gray-900 dark:text-white mb-1">Ваш ответ</h5>
                                                        <form onsubmit="submitReply(event, {{ $item['reply']->id }}, null)" class="space-y-1">
                                                            @csrf
                                                            <textarea 
                                                                name="content" 
                                                                rows="1" 
                                                                class="w-full px-1.5 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-primary-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                                                placeholder="Напишите ваш ответ..."
                                                                required
                                                            ></textarea>
                                                            <div class="flex items-center justify-end space-x-1">
                                                                <button type="button" onclick="toggleReplyForm({{ $item['reply']->id }})" class="px-1.5 py-0.5 text-xs text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                                                                    Отмена
                                                                </button>
                                                                <button type="submit" class="px-2 py-0.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded transition-colors">
                                                                    Ответить
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <!-- Первый, второй и третий уровень - рекурсивно (показываем как ветки) -->
                            @include('books.partials.replies', ['replies' => $reply->replies, 'depth' => $depth + 1])
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif
