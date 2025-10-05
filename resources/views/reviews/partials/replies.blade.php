@foreach($replies as $reply)
<div class="comment-branch" data-reply-id="{{ $reply->id }}" data-depth="{{ $depth }}">
    <!-- Comment Header -->
    <div class="comment-header">
        <div class="comment-author">
            @include('partials.user-mini-header', [
                'user' => $reply->isGuest() ? null : $reply->user,
                'timestamp' => $reply->created_at->diffForHumans(),
                'showGuest' => $reply->isGuest()
            ])
        </div>
        
        <!-- Toggle Button for nested replies -->
        @if($reply->replies && $reply->replies->count() > 0)
            <button onclick="toggleCommentBranch({{ $reply->id }})" class="toggle-btn">
                <svg class="w-4 h-4 toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
                <span class="reply-count">{{ $reply->replies->count() }}</span>
            </button>
        @endif
    </div>
    
    <!-- Comment Content -->
    <div class="comment-content">
        {{ $reply->content }}
    </div>
    
    <!-- Comment Actions -->
    <div class="comment-actions">
        <div class="comment-actions-left">
            <button onclick="toggleLike({{ $reply->id }})" 
                    class="action-btn {{ auth()->check() && $reply->isLikedBy(auth()->id()) ? 'liked' : '' }}">
                <svg class="w-4 h-4" fill="{{ auth()->check() && $reply->isLikedBy(auth()->id()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span id="likes-count-{{ $reply->id }}">{{ $reply->likes_count ?? 0 }}</span>
            </button>
            
            <button onclick="toggleReplyInput({{ $reply->id }})" class="action-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Відповісти
            </button>
        </div>
        
        <!-- Comment Controls (only for own comments) -->
        @if(auth()->check() && ($reply->user_id == auth()->id() || $reply->isGuest()))
            <div class="comment-controls">
                <button onclick="editComment({{ $reply->id }})" class="control-btn edit-btn" title="Редагувати">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>
                
                <button onclick="deleteComment({{ $reply->id }})" class="control-btn delete-btn" title="Видалити">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        @endif
    </div>
    
    <!-- Compact Reply Input -->
    <div id="reply-input-{{ $reply->id }}" class="reply-input hidden">
        <div class="reply-input-wrapper">
            <textarea 
                name="content" 
                rows="1" 
                class="reply-textarea"
                placeholder="Напишіть відповідь..." 
                required></textarea>
            <div class="reply-buttons">
                <button type="button" 
                        onclick="toggleReplyInput({{ $reply->id }})"
                        class="cancel-btn">
                    Скасувати
                </button>
                <button type="button" 
                        onclick="submitCompactReply({{ $reply->id }}, {{ $parentReview->id }})"
                        class="submit-btn">
                    Відправити
                </button>
            </div>
        </div>
    </div>
    
    <!-- Nested Comments (Collapsed by default) -->
    @if($reply->replies && $reply->replies->count() > 0)
        <div class="nested-comments collapsed" id="nested-comments-{{ $reply->id }}">
            @include('reviews.partials.replies', [
                'replies' => $reply->replies,
                'depth' => $depth + 1,
                'book' => $book,
                'parentReview' => $parentReview,
                'parentReply' => $reply
            ])
        </div>
    @endif
</div>
@endforeach