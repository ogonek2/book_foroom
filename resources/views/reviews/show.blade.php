@extends('layouts.app')

@section('title', 'Рецензія на ' . $book->title . ' - Книжковий форум')

@push('styles')
<style>
/* Forum-style layout */
.forum-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 2rem;
    align-items: start;
}

.forum-content {
    min-width: 0; /* Prevent grid overflow */
}

.forum-header {
    background: linear-gradient(135deg, hsl(var(--primary)) 0%, hsl(var(--accent)) 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 1rem;
}

.forum-post {
    background: hsl(var(--card));
    border: 1px solid hsl(var(--border));
    border-radius: 0.75rem;
    margin-bottom: 1rem;
    overflow: hidden;
    transition: all 0.2s ease;
}

.forum-post:hover {
    box-shadow: 0 4px 12px hsl(var(--foreground) / 0.1);
}

.post-header {
    background: hsl(var(--muted) / 0.5);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid hsl(var(--border));
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.post-author {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

/* Old author styles removed - now using user-mini-header */

.post-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.875rem;
    color: hsl(var(--muted-foreground));
}

.post-content {
    padding: 1.5rem;
    line-height: 1.7;
    color: hsl(var(--foreground));
}

.post-actions {
    padding: 1rem 1.5rem;
    border-top: 1px solid hsl(var(--border));
    background: hsl(var(--muted) / 0.3);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.action-buttons {
    display: flex;
    gap: 1rem;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: none;
    background: transparent;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
    color: hsl(var(--muted-foreground));
}

.action-btn:hover {
    background: hsl(var(--muted));
    color: hsl(var(--foreground));
}

.action-btn.liked {
    color: #ef4444;
    background: #fef2f2;
}

.action-btn.liked:hover {
    background: #fee2e2;
}

/* User mini header styles */
.user-mini-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1rem;
    color: white;
    position: relative;
    overflow: hidden;
}

.avatar-guest {
    background: linear-gradient(135deg, #6b7280, #9ca3af);
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-fallback {
    background: linear-gradient(135deg, hsl(var(--primary)), hsl(var(--accent)));
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.user-info {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.user-name {
    font-weight: 600;
    font-size: 0.875rem;
    color: hsl(var(--foreground));
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.guest-badge {
    background: #f97316;
    color: white;
    font-size: 0.75rem;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    font-weight: 500;
}

.user-timestamp {
    font-size: 0.75rem;
    color: hsl(var(--muted-foreground));
}

/* Dark theme adjustments */
@media (prefers-color-scheme: dark) {
    .guest-badge {
        background: #ea580c;
    }
}

.reply-form-content {
    padding: 1.5rem;
    background: hsl(var(--muted) / 0.2);
    border-top: 1px solid hsl(var(--border));
}

.comments-section {
    margin-top: 2rem;
}

.comment {
    margin-left: 2rem;
    margin-bottom: 1rem;
    position: relative;
}

.comment::before {
    content: '';
    position: absolute;
    left: -1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: hsl(var(--muted));
}

.comment .comment {
    margin-left: 1.5rem;
}

.comment .comment::before {
    background: hsl(var(--muted-foreground) / 0.3);
}

.rating-stars {
    display: flex;
    gap: 0.25rem;
}

.star {
    width: 1.25rem;
    height: 1.25rem;
    color: #fbbf24;
}

.star.empty {
    color: hsl(var(--muted-foreground) / 0.3);
}

/* Sticky sidebar */
.book-sidebar {
    position: sticky;
    top: 2rem;
    background: hsl(var(--card));
    border: 1px solid hsl(var(--border));
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 4px 12px hsl(var(--foreground) / 0.05);
}

.book-cover {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 0.5rem;
    box-shadow: 0 4px 12px hsl(var(--foreground) / 0.1);
    margin-bottom: 1rem;
}

.book-details h1 {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: hsl(var(--foreground));
    line-height: 1.3;
}

.book-details p {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    color: hsl(var(--muted-foreground));
}

.book-stats {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: hsl(var(--muted) / 0.5);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: hsl(var(--foreground));
    transition: background-color 0.2s ease;
}

.stat-item:hover {
    background: hsl(var(--muted) / 0.7);
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: hsl(var(--muted-foreground));
}

.empty-state svg {
    width: 4rem;
    height: 4rem;
    margin: 0 auto 1rem;
    opacity: 0.5;
}

/* Animations and loading states */
@keyframes spin {
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Notification styles */
.notification {
    backdrop-filter: blur(10px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Smooth transitions for interactive elements */
.action-btn {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.action-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Reply form - always visible for main review */
.reply-form {
    transition: all 0.3s ease;
    max-height: 500px;
    opacity: 1;
    visibility: visible;
}

/* Hidden reply forms for comments */
.reply-form.hidden {
    max-height: 0;
    opacity: 0;
    visibility: hidden;
}

/* New reply animation */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.comment.new-reply {
    animation: slideInUp 0.3s ease-out;
}

@media (max-width: 768px) {
    .forum-container {
        padding: 0 0.5rem;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .book-sidebar {
        position: static;
        order: -1;
    }
    
    .post-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .post-meta {
        align-self: flex-end;
    }
    
    .comment {
        margin-left: 1rem;
    }
    
    .comment .comment {
        margin-left: 1rem;
    }
}
</style>
@endpush

@section('main')
<div class="min-h-screen bg-background">
    <div class="forum-container py-8">
        <!-- Main Content -->
        <div class="forum-content">

        <!-- Main Review Post -->
        <div class="forum-post">
            <div class="post-header">
                <div class="post-author">
                    @include('partials.user-mini-header', [
                        'user' => $review->isGuest() ? null : $review->user,
                        'timestamp' => $review->created_at->diffForHumans(),
                        'showGuest' => $review->isGuest()
                    ])
                </div>
                <div class="post-meta">
                    <div class="rating-stars">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="star {{ $i <= $review->rating ? '' : 'empty' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span>{{ $review->rating }}/5</span>
                </div>
            </div>
            
            <div class="post-content">
                {{ $review->content }}
            </div>
            
            <div class="post-actions">
                <div class="action-buttons">
                    <button onclick="toggleLike({{ $review->id }})" 
                            class="action-btn {{ auth()->check() && $review->isLikedBy(auth()->id()) ? 'liked' : '' }}">
                        <svg class="w-4 h-4" fill="{{ auth()->check() && $review->isLikedBy(auth()->id()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span id="likes-count-{{ $review->id }}">{{ $review->likes_count ?? 0 }}</span>
                    </button>
                </div>
                
                <div class="text-sm text-muted-foreground">
                    <span class="replies-count">{{ $review->replies_count ?? 0 }}</span> {{ $review->replies_count == 1 ? 'відповідь' : ($review->replies_count < 5 ? 'відповіді' : 'відповідей') }}
                </div>
            </div>
            
            <!-- Reply Form - Always visible -->
            <div class="reply-form active">
                <div class="reply-form-content">
                    <h4 class="text-lg font-semibold mb-3">Ваша відповідь</h4>
                    <form onsubmit="submitReply(event, {{ $review->id }}, null)">
                        @csrf
                        <textarea name="content" 
                                  rows="4" 
                                  class="w-full p-3 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent bg-background text-foreground resize-none"
                                  placeholder="Напишіть вашу відповідь..." 
                                  required></textarea>
                        <div class="flex justify-end gap-2 mt-3">
                            <button type="submit" 
                                    class="px-6 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors">
                                Відповісти
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="comments-section">
            <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Обговорення (<span class="replies-count">{{ $review->replies_count ?? 0 }}</span>)
            </h2>
            
            @if($review->replies && $review->replies->count() > 0)
                <div class="space-y-4" id="comments-container">
                    @include('reviews.partials.replies', [
                        'replies' => $review->replies,
                        'depth' => 0,
                        'book' => $book,
                        'parentReview' => $review
                    ])
                </div>
            @else
                <div class="empty-state">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="text-xl font-semibold mb-2">Поки немає відповідей</h3>
                    <p class="mb-4">Станьте першим, хто поділиться своєю думкою</p>
                    <button onclick="toggleReplyForm({{ $review->id }})" 
                            class="px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors">
                        Написати перший коментар
                    </button>
                </div>
            @endif
        </div>
        </div>

        <!-- Sticky Book Sidebar -->
        <div class="book-sidebar">
            <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=450&fit=crop&crop=center' }}" 
                 alt="{{ $book->title }}" 
                 class="book-cover">
            <div class="book-details">
                <h1>{{ $book->title }}</h1>
                <p>{{ $book->author }}</p>
                <!-- Navigation Button -->
                <div class="mt-4">
                    <a href="{{ route('books.show', $book) }}" 
                       class="w-full bg-primary text-primary-foreground py-2 rounded-lg hover:bg-primary/90 transition-colors text-center block">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Назад до книги
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Основная кнопка теперь работает через inline onclick

// Submit reply with reactive UI update
window.submitReply = function(event, reviewId, parentId) {
    console.log('submitReply called with reviewId:', reviewId, 'parentId:', parentId);
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const content = formData.get('content');
    
    console.log('Form content:', content);
    
    if (!content.trim()) {
        showNotification('Будь ласка, введіть текст відповіді', 'error');
        return;
    }
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Відправляємо...';
    submitBtn.disabled = true;
    
    fetch(`/books/{{ $book->id }}/reviews/${reviewId}/replies`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            content: content,
            parent_id: parentId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            form.reset();
            toggleReplyForm(reviewId);
            
            // Add new reply to UI reactively
            addReplyToUI(data.reply, parentId);
            
            // Update replies count
            UIState.updateRepliesCount(UIState.repliesCount + 1);
            
            showNotification('Відповідь додано!', 'success');
        } else {
            throw new Error(data.message || 'Помилка при додаванні відповіді');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Помилка при додаванні відповіді: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Add new reply to UI reactively
function addReplyToUI(reply, parentId) {
    const replyHTML = createReplyHTML(reply);
    
    if (parentId) {
        // Find parent reply and add as nested
        const parentElement = document.querySelector(`[data-reply-id="${parentId}"]`);
        if (parentElement) {
            const nestedContainer = parentElement.querySelector('.nested-replies') || createNestedContainer(parentElement);
            nestedContainer.insertAdjacentHTML('afterbegin', replyHTML);
            
            // Add animation to new nested reply
            const newReply = nestedContainer.firstElementChild;
            newReply.classList.add('new-reply');
        }
    } else {
        // Add to main comments section
        let commentsSection = document.querySelector('#comments-container');
        
        // If no comments exist, create the container and hide empty state
        if (!commentsSection) {
            const emptyState = document.querySelector('.empty-state');
            if (emptyState) {
                emptyState.style.display = 'none';
            }
            
            commentsSection = document.createElement('div');
            commentsSection.className = 'space-y-4';
            commentsSection.id = 'comments-container';
            
            const commentsDiv = document.querySelector('.comments-section');
            commentsDiv.appendChild(commentsSection);
        }
        
        commentsSection.insertAdjacentHTML('afterbegin', replyHTML);
        
        // Add animation to new reply
        const newReply = commentsSection.firstElementChild;
        newReply.classList.add('new-reply');
    }
    
    // Scroll to new reply
    setTimeout(() => {
        const newReply = document.querySelector(`[data-reply-id="${reply.id}"]`);
        if (newReply) {
            newReply.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }, 100);
}

// Create nested replies container if it doesn't exist
function createNestedContainer(parentElement) {
    const container = document.createElement('div');
    container.className = 'nested-replies mt-4 space-y-4';
    parentElement.appendChild(container);
    return container;
}

// Create HTML for new reply
function createReplyHTML(reply) {
    const isGuest = reply.is_guest || false;
    const authorName = reply.author_name || 'Анонімний користувач';
    const avatarHTML = isGuest ? 
        `<div class="user-avatar">
            <div class="avatar-guest">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>` :
        `<div class="user-avatar">
            <div class="avatar-fallback">
                ${authorName.charAt(0).toUpperCase()}
            </div>
        </div>`;
    
    const guestBadge = isGuest ? 
        '<span class="guest-badge">Гість</span>' : '';
    
    return `
        <div class="comment" data-reply-id="${reply.id}">
            <div class="forum-post">
                <div class="post-header">
                    <div class="post-author">
                        <div class="user-mini-header">
                            ${avatarHTML}
                            <div class="user-info">
                                <div class="user-name">
                                    ${authorName} ${guestBadge}
                                </div>
                                <div class="user-timestamp">щойно</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="post-content">
                    ${reply.content}
                </div>
                
                <div class="post-actions">
                    <div class="action-buttons">
                        <button onclick="toggleLike(${reply.id})" class="action-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span id="likes-count-${reply.id}">0</span>
                        </button>
                        
                        <button onclick="
                            const form = document.getElementById('reply-form-${reply.id}');
                            form.classList.toggle('hidden');
                            if (!form.classList.contains('hidden')) {
                                setTimeout(() => {
                                    const textarea = form.querySelector('textarea');
                                    if (textarea) textarea.focus();
                                }, 100);
                            }
                        " class="action-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Відповісти
                        </button>
                    </div>
                </div>
                
                <!-- Reply Form - Hidden by default -->
                <div id="reply-form-${reply.id}" class="reply-form hidden">
                    <div class="reply-form-content">
                        <h4 class="text-lg font-semibold mb-3">Відповісти</h4>
                        <form onsubmit="submitReply(event, {{ $review->id }}, ${reply.id})">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                            <textarea name="content" 
                                      rows="3" 
                                      class="w-full p-3 border border-input rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent bg-background text-foreground resize-none"
                                      placeholder="Напишіть вашу відповідь..." 
                                      required></textarea>
                            <div class="flex justify-end gap-2 mt-3">
                                <button type="button" 
                                        onclick="document.getElementById('reply-form-${reply.id}').classList.add('hidden')"
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
        </div>
    `;
}

// Toggle like with reactive UI update
window.toggleLike = function(reviewId) {
    console.log('toggleLike called with reviewId:', reviewId);
    @auth
    const button = document.querySelector(`[onclick="toggleLike(${reviewId})"]`);
    const countElement = document.getElementById(`likes-count-${reviewId}`);
    
    console.log('Button element:', button);
    console.log('Count element:', countElement);
    
    // Add loading state
    const originalContent = button.innerHTML;
    button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    button.disabled = true;
    
    fetch(`/books/{{ $book->id }}/reviews/${reviewId}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update like count
            if (countElement) {
                countElement.textContent = data.likes_count;
            }
            
            // Update button state
            if (data.is_liked) {
                button.classList.add('liked');
                button.innerHTML = originalContent.replace('fill="none"', 'fill="currentColor"');
            } else {
                button.classList.remove('liked');
                button.innerHTML = originalContent;
            }
            
            // Show success animation
            button.style.transform = 'scale(1.1)';
            setTimeout(() => {
                button.style.transform = 'scale(1)';
            }, 200);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Помилка при оновленні лайка', 'error');
        button.innerHTML = originalContent;
    })
    .finally(() => {
        button.disabled = false;
    });
    @else
    showNotification('Будь ласка, увійдіть в систему, щоб ставити лайки', 'warning');
    @endauth
}

// Show notification
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    notification.className += ` ${colors[type] || colors.info}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to all reply buttons
    document.querySelectorAll('.reply-button').forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            toggleReplyForm(reviewId);
        });
    });
});
</script>
@endpush
@endsection