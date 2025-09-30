@foreach($replies as $reply)
<div class="reply-item" data-reply-id="{{ $reply->id }}" data-depth="{{ $depth }}">
    <div class="forum-post">
        <!-- Header -->
        <div class="post-header">
            <div class="post-author">
                @include('partials.user-mini-header', [
                    'user' => $reply->isGuest() ? null : $reply->user,
                    'timestamp' => $reply->created_at->diffForHumans(),
                    'showGuest' => $reply->isGuest()
                ])
            </div>
            
            @if($reply->replies && $reply->replies->count() > 0)
                <button onclick="toggleRepliesTree({{ $reply->id }})" class="action-btn text-xs">
                    <svg class="w-4 h-4 reply-toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <span class="reply-count">{{ $reply->replies->count() }}</span>
                </button>
            @endif
        </div>
        
        <!-- Content -->
        <div class="post-content px-4 py-3">
            @if($depth >= 5 && isset($parentReply))
                <div class="text-sm text-muted-foreground mb-2">
                    <span class="text-primary">→ відповідь для </span>
                    @if($parentReply->isGuest())
                        <span class="font-medium">Анонімний користувач</span>
                    @else
                        <a href="{{ route('profile.show', $parentReply->user->username) }}" class="font-medium hover:underline">
                            {{ $parentReply->user->name }}
                        </a>
                    @endif
                </div>
            @endif
            {{ $reply->content }}
        </div>
        
        <!-- Actions -->
        <div class="post-actions">
            <div class="action-buttons">
                <button onclick="toggleLike({{ $reply->id }})" 
                        class="action-btn {{ auth()->check() && $reply->isLikedBy(auth()->id()) ? 'liked' : '' }}">
                    <svg class="w-4 h-4" fill="{{ auth()->check() && $reply->isLikedBy(auth()->id()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span id="likes-count-{{ $reply->id }}">{{ $reply->likes_count ?? 0 }}</span>
                </button>
                
                <button onclick="toggleReplyForm({{ $reply->id }})" class="action-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    Відповісти
                </button>
            </div>
        </div>
        
        <!-- Reply Form -->
        <div id="reply-form-{{ $reply->id }}" class="reply-form hidden">
            <div class="reply-form-content px-4 py-4">
                <h4 class="text-lg font-semibold mb-3">Відповісти</h4>
                <form onsubmit="submitReply(event, {{ $parentReview->id }}, {{ $reply->id }})">
                    @csrf
                    <textarea name="content" 
                              rows="3" 
                              class="w-full p-3 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent bg-background text-foreground resize-none"
                              placeholder="Напишіть вашу відповідь..." 
                              required></textarea>
                    <div class="flex justify-end gap-2 mt-3">
                        <button type="button" 
                                onclick="toggleReplyForm({{ $reply->id }})"
                                class="px-4 py-2 text-sm text-muted-foreground hover:text-foreground">
                            Скасувати
                        </button>
                        <button type="submit" 
                                class="px-6 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors">
                            Відповісти
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Nested Replies -->
    @if($reply->replies && $reply->replies->count() > 0)
        <div class="nested-replies-container" id="replies-tree-{{ $reply->id }}">
            @include('reviews.partials.replies', [
                'replies' => $reply->replies,
                'depth' => $depth >= 5 ? 5 : $depth + 1,
                'book' => $book,
                'parentReview' => $parentReview,
                'parentReply' => $reply
            ])
        </div>
    @endif
</div>
@endforeach