@extends('layouts.app')

@section('title', 'Рецензія на ' . $book->title . ' - Книжковий форум')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
<style>
/* Forum-style layout */
.forum-container {
    margin: 0 auto;
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
    padding: 1rem 0;
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
    line-height: 1.7;
    color: hsl(var(--foreground));
}

.post-actions {
    padding: 0;
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
    padding: 0.5rem 0;
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
    background: hsl(var(--muted) / 0.2);
    border-top: 1px solid hsl(var(--border));
}

/* Comment Tree Structure */
.comments-tree {
    space-y: 0.5rem;
}

.comment-branch {
    background: hsl(var(--card));
    border: 1px solid hsl(var(--border));
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
    overflow: hidden;
    transition: all 0.2s ease;
}

.comment-branch:hover {
    box-shadow: 0 2px 8px hsl(var(--foreground) / 0.1);
}

.comment-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: hsl(var(--muted) / 0.3);
    border-bottom: 1px solid hsl(var(--border));
}

.comment-author {
    flex: 1;
}

.toggle-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.5rem;
    background: transparent;
    border: 1px solid hsl(var(--border));
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.75rem;
    color: hsl(var(--muted-foreground));
}

.toggle-btn:hover {
    background: hsl(var(--muted));
    color: hsl(var(--foreground));
}

.toggle-icon {
    transition: transform 0.3s ease;
}

.toggle-icon.rotated {
    transform: rotate(-90deg);
}

.comment-content {
    padding: 1rem;
    line-height: 1.6;
    color: hsl(var(--foreground));
}

.comment-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 1rem;
    background: hsl(var(--muted) / 0.2);
    border-top: 1px solid hsl(var(--border));
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: transparent;
    border: none;
    border-radius: 0.375rem;
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

/* Compact Reply Input */
.reply-input {
    border-top: 1px solid #e2e8f0;
    background: hsl(var(--muted) / 0.1);
    transition: all 0.3s ease;
    overflow: hidden;
}

.reply-input.hidden {
    max-height: 0;
    opacity: 0;
    padding: 0;
}

.reply-input-wrapper {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.reply-textarea {
    width: 100%;
    min-height: 1.5rem;
    max-height: 8rem;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    background: hsl(var(--background));
    color: hsl(var(--foreground));
    font-size: 0.875rem;
    line-height: 1.5;
    resize: none;
    transition: all 0.2s ease;
    overflow: hidden;
}

.reply-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.reply-textarea:not(:placeholder-shown) {
    min-height: auto;
}

.reply-textarea::placeholder {
    color: hsl(var(--muted-foreground));
}

.reply-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

.cancel-btn {
    padding: 0.5rem 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.cancel-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #475569;
}

.submit-btn {
    padding: 0.5rem 1rem;
    background: #3b82f6;
    border: 1px solid #3b82f6;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
    font-weight: 500;
    color: white;
}

.submit-btn:hover {
    background: #2563eb;
    border-color: #2563eb;
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Reddit-style Thread Lines for Nested Comments */
.nested-comments {
    margin-left: 0.75rem;
    padding-left: 0.75rem;
    position: relative;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: visible;
    opacity: 1;
}

/* Thread lines removed for cleaner look */

.nested-comments.collapsed {
    max-height: 0 !important;
    opacity: 0;
    margin-left: 0;
    padding-left: 0;
    padding-top: 0;
    padding-bottom: 0;
}

/* Comment Branch Styling with Thread Connection */
.comment-branch {
    position: relative;
    background: hsl(var(--card));
    border: 1px solid hsl(var(--border));
    border-radius: 0.75rem;
    margin-bottom: 0.75rem;
    overflow: visible;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px hsl(var(--foreground) / 0.1);
}

.comment-branch:hover {
    box-shadow: 0 4px 12px hsl(var(--foreground) / 0.15);
    transform: translateY(-1px);
}

/* Nested comment styling */
.nested-comments .comment-branch {
    background: hsl(var(--card));
    border: 1px solid hsl(var(--border));
    border-left: 2px solid #3b82f6;
    border-radius: 0.5rem;
    margin-bottom: 0.5rem;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.nested-comments .comment-branch:hover {
    border-left-color: #2563eb;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Level 2 styling */
.nested-comments .nested-comments .comment-branch {
    border-left: 1px solid #10b981;
}

.nested-comments .nested-comments .comment-branch:hover {
    border-left-color: #059669;
}

/* Level 3 styling */
.nested-comments .nested-comments .nested-comments .comment-branch {
    border-left: 1px solid #f59e0b;
}

.nested-comments .nested-comments .nested-comments .comment-branch:hover {
    border-left-color: #d97706;
}

/* Level 4 styling */
.nested-comments .nested-comments .nested-comments .nested-comments .comment-branch {
    border-left: 1px solid #8b5cf6;
}

.nested-comments .nested-comments .nested-comments .nested-comments .comment-branch:hover {
    border-left-color: #7c3aed;
}

/* Level 5 styling */
.nested-comments .nested-comments .nested-comments .nested-comments .nested-comments .comment-branch {
    border-left: 1px solid #ef4444;
}

.nested-comments .nested-comments .nested-comments .nested-comments .nested-comments .comment-branch:hover {
    border-left-color: #dc2626;
}

/* Comment Header with Visual Indicators */
.comment-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, hsl(var(--muted) / 0.4) 0%, hsl(var(--muted) / 0.2) 100%);
    border-bottom: 1px solid hsl(var(--border));
    position: relative;
}

.nested-comments .comment-header {
    background: linear-gradient(135deg, hsl(var(--primary) / 0.1) 0%, hsl(var(--primary) / 0.05) 100%);
    padding: 0.5rem 0.75rem;
}

/* Toggle Button Styling */
.toggle-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.75rem;
    background: hsl(var(--primary) / 0.1);
    border: 1px solid hsl(var(--primary) / 0.3);
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.75rem;
    color: hsl(var(--primary));
    font-weight: 500;
}

.toggle-btn:hover {
    background: hsl(var(--primary) / 0.2);
    border-color: hsl(var(--primary) / 0.5);
    transform: scale(1.05);
}

.toggle-icon {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.toggle-icon.rotated {
    transform: rotate(-90deg);
}

/* Comment Content Styling */
.comment-content {
    padding: 1rem;
    line-height: 1.7;
    color: hsl(var(--foreground));
    background: hsl(var(--background));
    position: relative;
}

.nested-comments .comment-content {
    padding: 0.75rem;
    background: hsl(var(--card));
    border-radius: 0 0 0.5rem 0.5rem;
    margin: 0.25rem;
    margin-top: 0;
}

/* Comment Actions with Better Spacing */
.comment-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, hsl(var(--muted) / 0.2) 0%, transparent 100%);
    border-top: 1px solid hsl(var(--border));
}

.nested-comments .comment-actions {
    padding: 0.5rem 0.75rem;
    background: hsl(var(--muted) / 0.1);
    margin: 0 0.25rem 0.25rem 0.25rem;
    border-radius: 0 0 0.5rem 0.5rem;
}

/* Action Buttons with Better Visual Feedback */
.action-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: transparent;
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 0.875rem;
    color: hsl(var(--muted-foreground));
    font-weight: 500;
}

.action-btn:hover {
    background: transparent;
    color: hsl(var(--foreground));
    transform: translateY(-1px);
}

.action-btn.liked {
    color: #ef4444;
    background: transparent;
}

.action-btn.liked:hover {
    background: transparent;
    color: #dc2626;
}

/* Comment Actions Layout */
.comment-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: hsl(var(--muted) / 0.05);
    border-top: 1px solid hsl(var(--border));
}

.comment-actions-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* Comment Controls */
.comment-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.control-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    background: transparent;
    border: none;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    color: hsl(var(--muted-foreground));
}

.control-btn:hover {
    background: hsl(var(--muted) / 0.5);
    transform: translateY(-1px);
}

.control-btn.edit-btn:hover {
    color: #3b82f6;
    background: rgba(59, 130, 246, 0.1);
}

.control-btn.delete-btn:hover {
    color: #ef4444;
    background: rgba(239, 68, 68, 0.1);
}

.control-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.control-btn:disabled:hover {
    background: transparent;
    color: hsl(var(--muted-foreground));
    transform: none;
}

/* Edit Form Styles */
.edit-form {
    padding: 1rem;
    background: hsl(var(--muted) / 0.3);
    border-radius: 0.5rem;
    margin: 0.5rem 0;
}

.edit-form-wrapper {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.edit-textarea {
    width: 100%;
    min-height: 4rem;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    background: hsl(var(--background));
    color: hsl(var(--foreground));
    font-size: 0.875rem;
    line-height: 1.5;
    resize: vertical;
    transition: all 0.2s ease;
    font-family: inherit;
}

.edit-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.edit-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.cancel-edit-btn {
    padding: 0.5rem 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.cancel-edit-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #475569;
}

.save-edit-btn {
    padding: 0.5rem 1rem;
    background: #3b82f6;
    border: 1px solid #3b82f6;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
    font-weight: 500;
    color: white;
}

.save-edit-btn:hover {
    background: #2563eb;
    border-color: #2563eb;
}

.save-edit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Depth Indicators for Visual Hierarchy */
.comment-branch[data-depth="1"] {
    border-left: 1px solid hsl(220, 70%, 50%);
}

.comment-branch[data-depth="2"] {
    border-left: 1px solid hsl(160, 70%, 50%);
}

.comment-branch[data-depth="3"] {
    border-left: 1px solid hsl(30, 70%, 50%);
}

.comment-branch[data-depth="4"] {
    border-left: 1px solid hsl(280, 70%, 50%);
}

.comment-branch[data-depth="5"] {
    border-left: 1px solid hsl(0, 70%, 50%);
}

/* Smooth Expand/Collapse Animation */
.nested-comments {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Reply Input Styling in Nested Context */
.nested-comments .reply-input {
    margin: 0.25rem;
    border-radius: 0.5rem;
    background: hsl(var(--muted) / 0.05);
}

.nested-comments .reply-input-wrapper {
    padding: 0.75rem;
}

/* Visual Connection Lines */
.nested-comments::after {
    content: '';
    position: absolute;
    left: -1.5rem;
    top: -0.75rem;
    width: 1.5rem;
    height: 1.5rem;
    border-left: 2px solid hsl(var(--primary) / 0.3);
    border-bottom: 2px solid hsl(var(--primary) / 0.3);
    border-radius: 0 0 0 0.5rem;
}

/* Dark Theme Adjustments */
@media (prefers-color-scheme: dark) {
    .nested-comments .comment-branch {
        background: hsl(var(--card));
        border-color: hsl(var(--border));
    }
    
    .nested-comments .comment-branch:hover {
        background: hsl(var(--card));
        border-color: hsl(var(--border));
    }
    
    .nested-comments .comment-header {
        background: linear-gradient(135deg, hsl(var(--primary) / 0.15) 0%, hsl(var(--primary) / 0.08) 100%);
    }
    
    .nested-comments .comment-content {
        background: hsl(var(--card) / 0.8);
    }
    
    .nested-comments .comment-actions {
        background: hsl(var(--muted) / 0.15);
    }
    
    /* Thread lines removed for dark theme too */
    
    /* Dark theme for reply inputs */
    .reply-input {
        border-top-color: #374151;
    }
    
    .reply-textarea {
        border-color: #374151;
        background: hsl(var(--background));
        color: hsl(var(--foreground));
    }
    
    .reply-textarea:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.1);
    }
    
    .cancel-btn {
        background: #374151;
        border-color: #4b5563;
        color: #d1d5db;
    }
    
    .cancel-btn:hover {
        background: #4b5563;
        border-color: #6b7280;
        color: #f3f4f6;
    }
    
    .submit-btn {
        background: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }
    
    .submit-btn:hover {
        background: #2563eb;
        border-color: #2563eb;
    }
    
    /* Dark theme for action buttons */
    .action-btn {
        color: #9ca3af;
    }
    
    .action-btn:hover {
        color: #f3f4f6;
    }
    
    .action-btn.liked {
        color: #ef4444;
    }
    
    .action-btn.liked:hover {
        color: #dc2626;
    }
    
    /* Dark theme for comment controls */
    .comment-controls {
        color: #9ca3af;
    }
    
    .control-btn {
        color: #9ca3af;
    }
    
    .control-btn:hover {
        background: rgba(156, 163, 175, 0.1);
    }
    
    .control-btn.edit-btn:hover {
        color: #60a5fa;
        background: rgba(96, 165, 250, 0.1);
    }
    
    .control-btn.delete-btn:hover {
        color: #f87171;
        background: rgba(248, 113, 113, 0.1);
    }
}

/* Focus States for Accessibility */
.comment-branch:focus-within {
    outline: 2px solid hsl(var(--primary) / 0.5);
    outline-offset: 2px;
}

.action-btn:focus {
    outline: 2px solid hsl(var(--primary) / 0.5);
    outline-offset: 2px;
}

.toggle-btn:focus {
    outline: 2px solid hsl(var(--primary) / 0.7);
    outline-offset: 2px;
}

/* Animation for New Comments */
@keyframes slideInFromTop {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.comment-branch.new-reply {
    animation: slideInFromTop 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Subtle Pulse Animation for Toggle Buttons */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.toggle-btn:hover .toggle-icon {
    animation: pulse 2s infinite;
}

/* Improved Typography for Better Readability */
.comment-content {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    word-wrap: break-word;
    hyphens: auto;
}

.comment-content p {
    margin-bottom: 0.75rem;
}

.comment-content p:last-child {
    margin-bottom: 0;
}

/* Auto-resize textarea */
.reply-textarea {
    overflow: hidden;
    resize: none;
}

.reply-textarea[style*="height"] {
    height: auto !important;
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
    box-shadow: 0 4px 12px hsl(var(--foreground) / 0.05);
}

.book-cover {
    width: 100%;
    height: auto;
    aspect-ratio: 3/4;
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
    
    /* Mobile comment adjustments */
    .comment-branch {
        margin-bottom: 0.5rem;
        border-radius: 0.5rem;
    }
    
    .comment-header {
        padding: 0.5rem 0.75rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .comment-content {
        padding: 0.75rem;
        font-size: 0.875rem;
    }
    
    .comment-actions {
        padding: 0.5rem 0.75rem;
        gap: 0.5rem;
    }
    
    .action-btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
    }
    
    /* Mobile nested comments - prevent horizontal overflow */
    .nested-comments {
        margin-left: 0.5rem;
        padding-left: 0.5rem;
        max-width: calc(100vw - 2rem);
        overflow-x: hidden;
    }
    
    /* Limit nesting depth on mobile */
    .nested-comments .nested-comments {
        margin-left: 0.375rem;
        padding-left: 0.375rem;
    }
    
    .nested-comments .nested-comments .nested-comments {
        margin-left: 0.125rem;
        padding-left: 0.125rem;
    }
    
    /* Reset indentation for deep nesting (4+ levels) */
    .nested-comments .nested-comments .nested-comments .nested-comments {
        margin-left: 0;
        padding-left: 0;
        border-left: none;
        background: hsl(var(--primary) / 0.05);
        border-radius: 0.5rem;
        margin-top: 0.5rem;
    }
    
    .nested-comments .nested-comments .nested-comments .nested-comments::before {
        display: none;
    }
    
    .nested-comments .nested-comments .nested-comments .nested-comments::after {
        display: none;
    }
    
    .nested-comments::before {
        width: 2px;
        left: -2px;
    }
    
    .nested-comments .comment-branch {
        margin-bottom: 0.375rem;
        max-width: 100%;
        overflow: visible;
    }
    
    .nested-comments .comment-header {
        padding: 0.375rem 0.5rem;
        flex-wrap: wrap;
    }
    
    .nested-comments .comment-content {
        padding: 0.5rem;
        word-break: break-word;
        overflow-wrap: break-word;
    }
    
    .nested-comments .comment-actions {
        padding: 0.375rem 0.5rem;
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    .reply-input-wrapper {
        padding: 0.75rem;
    }
    
    .reply-textarea {
        font-size: 0.875rem;
        padding: 0.5rem;
    }
    
    .reply-buttons {
        gap: 0.25rem;
    }
    
    .cancel-btn,
    .submit-btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }
    
    /* Hide connection lines on mobile for cleaner look */
    .nested-comments::after {
        display: none;
    }
    
    /* Adjust depth indicators for mobile */
    .comment-branch[data-depth="1"] {
        border-left-width: 1px;
    }
    
    .comment-branch[data-depth="2"],
    .comment-branch[data-depth="3"],
    .comment-branch[data-depth="4"],
    .comment-branch[data-depth="5"] {
        border-left-width: 1px;
    }
    
    /* Deep nesting indicator for mobile */
    .nested-comments .nested-comments .nested-comments .nested-comments .comment-branch {
        border-left: 1px solid hsl(var(--primary) / 0.6) !important;
        background: hsl(var(--primary) / 0.05);
    }
    
    /* Add visual indicator for deep nesting */
    .nested-comments .nested-comments .nested-comments .nested-comments .comment-header::before {
        content: "↳ ";
        color: hsl(var(--primary));
        font-weight: bold;
        margin-right: 0.25rem;
    }
}

/* Extra small screens (phones in portrait) */
@media (max-width: 480px) {
    .nested-comments {
        margin-left: 0.375rem;
        padding-left: 0.375rem;
        max-width: calc(100vw - 1rem);
    }
    
    .nested-comments .nested-comments {
        margin-left: 0.25rem;
        padding-left: 0.25rem;
    }
    
    /* Reset all indentation after 2 levels on very small screens */
    .nested-comments .nested-comments .nested-comments {
        margin-left: 0;
        padding-left: 0;
        border-left: none;
        background: hsl(var(--primary) / 0.05);
        border-radius: 0.5rem;
        margin-top: 0.25rem;
    }
    
    .nested-comments .nested-comments .nested-comments::before,
    .nested-comments .nested-comments .nested-comments::after {
        display: none;
    }
    
    .nested-comments .nested-comments .nested-comments .comment-branch {
        border-left: 2px solid hsl(var(--primary) / 0.4) !important;
        background: hsl(var(--primary) / 0.03);
    }
    
    .nested-comments .nested-comments .nested-comments .comment-header::before {
        content: "↳ ";
        color: hsl(var(--primary));
        font-weight: bold;
        margin-right: 0.25rem;
    }
    
    /* Ensure all content fits */
    .comment-branch {
        max-width: 100%;
        overflow: visible;
    }
    
    .comment-content {
        word-break: break-word;
        overflow-wrap: break-word;
        hyphens: auto;
    }
    
    .comment-actions {
        flex-wrap: wrap;
        gap: 0.25rem;
    }
    
    .action-btn {
        min-width: auto;
        flex: 1;
        justify-content: center;
    }
}
</style>
@endpush

@section('main')
<div class="min-h-screen bg-background">
    <div class="forum-container">
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
                    
                    <!-- Review Controls (only for own reviews) -->
                    @if(auth()->check() && $review->user_id == auth()->id())
                        <div class="comment-controls">
                            <button onclick="editComment({{ $review->id }})" class="control-btn edit-btn" title="Редагувати рецензію">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            
                            <button onclick="deleteComment({{ $review->id }})" class="control-btn delete-btn" title="Видалити рецензію">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
                
                <div class="text-sm text-muted-foreground">
                    <span class="replies-count">{{ $review->replies_count ?? 0 }}</span> {{ $review->replies_count == 1 ? 'відповідь' : ($review->replies_count < 5 ? 'відповіді' : 'відповідей') }}
                </div>
            </div>
            
            <!-- Reply Form теперь обрабатывается Vue компонентом -->
        </div>

        <!-- Vue.js Replies Component -->
        @php
        // Рекурсивная функция для формирования данных ответов
        $formatReply = function($reply) use (&$formatReply) {
            return [
                'id' => $reply->id,
                'content' => $reply->content,
                'created_at' => $reply->created_at->toISOString(),
                'user_id' => $reply->user_id,
                'parent_id' => $reply->parent_id,
                'is_guest' => $reply->isGuest(),
                'user' => $reply->user ? [
                    'id' => $reply->user->id,
                    'name' => $reply->user->name,
                    'username' => $reply->user->username,
                    'avatar_display' => $reply->user->avatar_display ?? null,
                ] : null,
                'is_liked_by_current_user' => auth()->check() ? $reply->isLikedBy(auth()->id()) : false,
                'likes_count' => $reply->likes_count ?? 0,
                'replies_count' => $reply->replies_count ?? 0,
                'replies' => $reply->replies->map($formatReply)->toArray(),
            ];
        };
        
        $repliesData = $review->replies->map($formatReply)->toArray();
        @endphp
        
        <div id="review-replies-app">
            <reviews-replies-list 
                :replies="{{ json_encode($repliesData) }}"
                book-slug="{{ $book->slug }}"
                :review-id="{{ $review->id }}"
                :current-user-id="{{ auth()->id() }}"
                :is-moderator="{{ auth()->check() && auth()->user()->isModerator() ? 'true' : 'false' }}">
            </reviews-replies-list>
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
// Vue приложение для ответов на рецензии
document.addEventListener('DOMContentLoaded', function() {
    const reviewRepliesApp = new Vue({
        el: '#review-replies-app',
        data: {
            // Данные передаются через props
        },
        methods: {
            showNotification(message, type = 'success') {
                // Уведомление обрабатывается в компоненте
            }
        }
    });
});

// Функция для лайка главной рецензии (не обрабатывается Vue)
window.toggleLike = function(reviewId) {
    @auth
    const button = document.querySelector(`[onclick="toggleLike(${reviewId})"]`);
    const countElement = document.getElementById(`likes-count-${reviewId}`);
    
    if (!button || !countElement) return;
    
    const originalContent = button.innerHTML;
    button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    button.disabled = true;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    fetch(`/books/{{ $book->slug }}/reviews/${reviewId}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            countElement.textContent = data.likes_count;
            button.innerHTML = originalContent;
            
            const countSpan = button.querySelector('span');
            if (countSpan) {
                countSpan.textContent = data.likes_count;
            }
            
            const svg = button.querySelector('svg');
            if (data.is_liked) {
                button.classList.add('liked');
                if (svg) svg.setAttribute('fill', 'currentColor');
            } else {
                button.classList.remove('liked');
                if (svg) svg.setAttribute('fill', 'none');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = originalContent;
    })
    .finally(() => {
        button.disabled = false;
    });
    @else
    alert('Будь ласка, увійдіть в систему, щоб ставити лайки');
    @endauth
}

// Функции для редактирования/удаления главной рецензии (не обрабатывается Vue)
window.editComment = function(commentId) {
    // Эта функция больше не используется для ответов (обрабатывается Vue)
    // Оставляем только для совместимости, если используется где-то еще
}

window.deleteComment = function(commentId) {
    // Эта функция больше не используется для ответов (обрабатывается Vue)
    // Оставляем только для совместимости, если используется где-то еще
}
</script>
@endpush
@endsection