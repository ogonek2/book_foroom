<template>
    <div class="discussion-reply" :class="{ 'highlighted-reply': isHighlighted }" :data-reply-id="reply.id" :data-depth="level">
        <div class="rounded-xl p-0 py-1 sm:py-2 px-1 sm:px-2">
            <!-- Header -->
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <template v-if="reply.user && reply.user.username">
                        <a :href="profileUrl(reply.user.username)"
                           class="flex items-center space-x-2 sm:space-x-3 group"
                           @click.stop>
                            <img :src="reply.user.avatar_display || reply.user.avatar || '/storage/avatars/default.png'"
                                 :alt="reply.user.name"
                                 class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex-shrink-0 transition-transform duration-200 group-hover:scale-110">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center space-x-2">
                                    <div class="text-light-text-primary dark:text-dark-text-primary font-medium text-sm sm:text-base truncate group-hover:text-brand-500 dark:group-hover:text-brand-400 transition-colors">
                                        {{ reply.user.name }}
                                    </div>
                                    <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs sm:text-sm">
                                        {{ formatDate(reply.created_at) }}
                                    </div>
                                </div>
                                <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs sm:text-sm truncate group-hover:text-brand-500 dark:group-hover:text-brand-400 transition-colors">
                                    {{ '@' + reply.user.username }}
                                </div>
                            </div>
                        </a>
                    </template>
                    <template v-else>
                        <img :src="reply.user.avatar_display || reply.user.avatar || '/storage/avatars/default.png'" 
                             :alt="reply.user.name"
                             class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex-shrink-0">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center space-x-2">
                                <div class="text-light-text-primary dark:text-dark-text-primary font-medium text-sm sm:text-base truncate">
                                    {{ reply.user?.name || 'Користувач' }}
                                </div>
                                <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs sm:text-sm">
                                    {{ formatDate(reply.created_at) }}
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Reply Content -->
            <div v-if="!isEditing" 
                 class="text-light-text-primary dark:text-dark-text-primary leading-relaxed text-sm sm:text-base break-words"
                 style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;"
                 v-html="formatContent(reply.content)">
            </div>

            <!-- Edit Form -->
            <div v-if="isEditing" class="mb-3 sm:mb-4">
                <textarea v-model="editContent"
                          class="w-full p-3 sm:p-4 bg-light-bg-secondary dark:bg-gray-700 rounded-xl text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none transition-colors text-sm sm:text-base"
                          rows="3"></textarea>
                <div class="flex justify-end space-x-2 mt-3">
                    <button @click="cancelEdit"
                            class="px-3 py-2 sm:px-4 bg-gray-500 dark:bg-gray-600 text-white rounded-lg hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors text-sm">
                        Скасувати
                    </button>
                    <button @click="saveEdit"
                            :disabled="isSaving"
                            class="px-3 py-2 sm:px-4 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors text-sm">
                        {{ isSaving ? 'Збереження...' : 'Зберегти' }}
                    </button>
                </div>
            </div>

            <!-- Actions -->
            <div v-if="isAuthenticated" class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <!-- Toggle Button -->
                    <button v-if="reply.replies && reply.replies.length > 0"
                            @click="toggleReplies"
                            class="toggle-button flex items-center space-x-1 sm:space-x-2 text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary text-xs sm:text-sm">
                        <i :class="['fas text-xs toggle-icon', repliesExpanded ? 'fa-chevron-down' : 'fa-chevron-right']"></i>
                        <span class="font-medium">
                            <span>{{ repliesExpanded ? 'Згорнути' : 'Відповіді' }}</span>
                            <span class="text-light-text-tertiary dark:text-dark-text-tertiary">({{ reply.replies.length }})</span>
                        </span>
                    </button>

                    <!-- Like Button -->
                    <button @click="toggleLike"
                            class="flex items-center py-1 rounded-lg transition-colors">
                        <i :class="['fas fa-heart text-xs sm:text-sm', isLiked ? 'text-red-500 dark:text-red-400' : 'text-light-text-tertiary dark:text-dark-text-tertiary']"></i>
                        <span class="ml-1 text-light-text-tertiary dark:text-dark-text-tertiary text-xs sm:text-sm">
                            {{ likesCount }}
                        </span>
                    </button>
                </div>

                <div class="flex items-center space-x-2">
                    <!-- Reply Button -->
                    <button v-if="!isDiscussionClosed"
                            @click="showReplyForm = !showReplyForm"
                            class="flex items-center space-x-1 text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 transition-colors text-xs sm:text-sm">
                        <i class="fas fa-reply text-xs"></i>
                        <span>Відповісти</span>
                    </button>

                    <!-- Edit/Delete menu for owner -->
                    <div v-if="canEdit" class="relative group">
                        <button class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary transition-colors p-1">
                            <i class="fas fa-ellipsis-h text-xs"></i>
                        </button>
                        <div class="absolute right-0 top-8 bg-white dark:bg-gray-800 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10 min-w-32">
                            <div class="py-2">
                                <button @click="startEdit"
                                        class="block w-full text-left px-3 py-2 text-sm text-light-text-secondary dark:text-dark-text-secondary hover:bg-light-bg-secondary dark:hover:bg-gray-700 hover:text-light-text-primary dark:hover:text-dark-text-primary">
                                    Редагувати
                                </button>
                                <button @click="deleteReply"
                                        class="block w-full text-left px-3 py-2 text-sm text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                    Вилучити
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reply Form -->
            <div v-if="showReplyForm" class="mb-3 sm:mb-4 mt-3">
                <textarea v-model="replyContent"
                          class="w-full p-3 sm:p-4 bg-light-bg-secondary dark:bg-gray-700 rounded-xl text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none transition-colors text-sm sm:text-base"
                          rows="3" 
                          placeholder="Напишіть відповідь..."
                          ref="replyTextarea"></textarea>
                <div class="flex justify-end space-x-2 mt-3">
                    <button @click="cancelReply"
                            class="px-3 py-2 sm:px-4 bg-gray-500 dark:bg-gray-600 text-white rounded-lg hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors text-sm">
                        Скасувати
                    </button>
                    <button @click="submitReply"
                            :disabled="isSubmitting || !replyContent.trim()"
                            class="px-3 py-2 sm:px-4 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ isSubmitting ? 'Відправляємо...' : 'Відправити' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Nested Replies -->
        <div v-if="reply.replies && reply.replies.length > 0 && repliesExpanded" class="mt-4">
            <div class="space-y-4 replies-container">
                <DiscussionReply v-for="nestedReply in reply.replies"
                                 :key="nestedReply.id"
                                 :reply="nestedReply"
                                 :level="level + 1"
                                 :highlighted-reply-id="highlightedReplyId"
                                 :discussion-slug="discussionSlug"
                                 :current-user-id="currentUserId"
                                 :is-discussion-closed="isDiscussionClosed"
                                 :is-moderator="isModerator"
                                 ref="replyComponents"
                                 @reply-added="handleReplyAdded"
                                 @reply-updated="handleReplyUpdated"
                                 @reply-deleted="handleReplyDeleted"
                                 @like-toggled="handleLikeToggled" />
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'DiscussionReply',
    props: {
        reply: {
            type: Object,
            required: true
        },
        level: {
            type: Number,
            default: 0
        },
        discussionSlug: {
            type: String,
            required: true
        },
        currentUserId: {
            type: Number,
            default: null
        },
        isDiscussionClosed: {
            type: Boolean,
            default: false
        },
        isModerator: {
            type: Boolean,
            default: false
        },
        highlightedReplyId: {
            type: Number,
            default: null
        }
    },
    data() {
        return {
            repliesExpanded: false,
            showReplyForm: false,
            replyContent: '',
            isSubmitting: false,
            isEditing: false,
            editContent: '',
            isSaving: false,
            isLiked: this.reply.is_liked || false,
            likesCount: this.reply.likes_count || 0
        };
    },
    computed: {
        isAuthenticated() {
            return this.currentUserId !== null;
        },
        canEdit() {
            return this.reply.user_id === this.currentUserId || this.isModerator;
        },
        isHighlighted() {
            return this.highlightedReplyId === this.reply.id;
        }
    },
    watch: {
        highlightedReplyId(newVal) {
            if (newVal === this.reply.id) {
                // If this reply is highlighted, expand it if it has replies
                if (this.reply.replies && this.reply.replies.length > 0 && !this.repliesExpanded) {
                    this.repliesExpanded = true;
                }
            }
        }
    },
    methods: {
        toggleReplies() {
            this.repliesExpanded = !this.repliesExpanded;
        },
        
        expandReplies() {
            if (!this.repliesExpanded && this.reply.replies && this.reply.replies.length > 0) {
                this.repliesExpanded = true;
            }
        },
        
        formatContent(content) {
            if (!content) return '';
            
            // Escape HTML to prevent XSS
            const escaped = content
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
            
            // Replace @username mentions with styled links
            // Supports Cyrillic, Latin, numbers, underscores, and hyphens
            const mentionRegex = /@([a-zA-Zа-яА-ЯёЁіІїЇєЄ0-9_-]+)/gu;
            
            return escaped.replace(mentionRegex, (match, username) => {
                const profileUrl = `/users/${username}`;
                return `<a href="${profileUrl}" class="mention-link text-brand-500 dark:text-brand-400 hover:text-brand-600 dark:hover:text-brand-300 font-medium transition-colors" target="_blank">@${username}</a>`;
            });
        },
        
        async toggleLike() {
            try {
                const response = await axios.post(`/discussions/${this.discussionSlug}/replies/${this.reply.id}/like`);

                if (response.data.success) {
                    this.isLiked = response.data.liked;
                    this.likesCount = response.data.likes_count;
                    this.$emit('like-toggled', this.reply.id, response.data.liked, response.data.likes_count);
                }
            } catch (error) {
                console.error('Error toggling like:', error);
            }
        },
        
        cancelReply() {
            this.showReplyForm = false;
            this.replyContent = '';
        },
        
        async submitReply() {
            if (!this.replyContent.trim() || this.isSubmitting) return;

            this.isSubmitting = true;

            try {
                const response = await axios.post(`/discussions/${this.discussionSlug}/replies`, {
                    content: this.replyContent,
                    parent_id: this.reply.id
                });
                
                if (response.data.success) {
                    this.$emit('reply-added', response.data.reply, this.reply.id);
                    this.replyContent = '';
                    this.showReplyForm = false;
                    this.repliesExpanded = true;
                    this.showNotification('Відповідь успішно додано!', 'success');
                } else {
                    this.showNotification(response.data.message || 'Помилка при відправці відповіді', 'error');
                }
            } catch (error) {
                console.error('Error submitting reply:', error);
                this.showNotification('Помилка при відправці відповіді', 'error');
            } finally {
                this.isSubmitting = false;
            }
        },
        
        profileUrl(username) {
            return username ? `/users/${username}` : '#';
        },
        
        startEdit() {
            this.isEditing = true;
            this.editContent = this.reply.content;
        },
        
        cancelEdit() {
            this.isEditing = false;
            this.editContent = '';
        },
        
        async saveEdit() {
            if (!this.editContent.trim() || this.isSaving) return;

            this.isSaving = true;

            try {
                const response = await axios.put(`/discussions/${this.discussionSlug}/replies/${this.reply.id}`, {
                    content: this.editContent
                });
                
                if (response.data.success) {
                    this.$emit('reply-updated', response.data.reply);
                    this.isEditing = false;
                    this.showNotification('Відповідь оновлено!', 'success');
                } else {
                    this.showNotification(response.data.message || 'Помилка при оновленні відповіді', 'error');
                }
            } catch (error) {
                console.error('Error updating reply:', error);
                this.showNotification('Помилка при оновленні відповіді', 'error');
            } finally {
                this.isSaving = false;
            }
        },
        
        async deleteReply() {
            const confirmed = await confirm('Ви впевнені, що хочете видалити цю відповідь?', 'Підтвердження', 'warning');
            if (!confirmed) return;

            try {
                const response = await axios.delete(`/discussions/${this.discussionSlug}/replies/${this.reply.id}`);
                
                if (response.data.success) {
                    this.$emit('reply-deleted', this.reply.id);
                    this.showNotification('Відповідь видалено!', 'success');
                } else {
                    this.showNotification(response.data.message || 'Помилка при видаленні відповіді', 'error');
                }
            } catch (error) {
                console.error('Error deleting reply:', error);
                this.showNotification('Помилка при видаленні відповіді', 'error');
            }
        },
        
        handleReplyAdded(reply, parentId) {
            this.$emit('reply-added', reply, parentId);
        },
        
        handleReplyUpdated(reply) {
            this.$emit('reply-updated', reply);
        },
        
        handleReplyDeleted(replyId) {
            this.$emit('reply-deleted', replyId);
        },
        
        handleLikeToggled(replyId, liked, count) {
            this.$emit('like-toggled', replyId, liked, count);
        },
        
        formatDate(dateString) {
            if (!dateString) return 'щойно';
            
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);

            if (diffInSeconds < 60) return 'щойно';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} хв тому`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} год тому`;
            if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} дн тому`;
            
            return date.toLocaleDateString('uk-UA', { day: 'numeric', month: 'short', year: 'numeric' });
        },
        
        showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' :
                'bg-blue-500'
            } text-white font-medium`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    },
    watch: {
        showReplyForm(newValue) {
            if (newValue) {
                this.$nextTick(() => {
                    this.$refs.replyTextarea?.focus();
                });
            }
        }
    }
};
</script>

<style scoped>
/* Discussion Reply Styles */
.discussion-reply {
    margin-bottom: 1rem;
    transition: all 0.2s ease-in-out;
}

.replies-container {
    transition: all 0.3s ease-in-out;
}

.toggle-button {
    transition: all 0.2s ease-in-out;
}

.toggle-icon {
    transition: transform 0.2s ease-in-out;
}

/* Depth Indicators for Visual Hierarchy */
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

    .discussion-reply[data-depth="1"],
    .discussion-reply[data-depth="2"],
    .discussion-reply[data-depth="3"],
    .discussion-reply[data-depth="4"],
    .discussion-reply[data-depth="5"] {
        border-left-width: 1px;
        padding-left: 0.25rem;
    }
}

/* Highlight styles for target reply */
.highlighted-reply {
    border: 2px solid #f59e0b !important;
    border-radius: 12px !important;
    background-color: rgba(245, 158, 11, 0.1) !important;
    animation: pulse-highlight 2s ease-in-out;
}

@keyframes pulse-highlight {
    0% { background-color: rgba(245, 158, 11, 0.2); }
    50% { background-color: rgba(245, 158, 11, 0.15); }
    100% { background-color: rgba(245, 158, 11, 0.1); }
}

/* Mention link styles */
.mention-link {
    color: rgb(139, 92, 246);
    font-weight: 500;
    text-decoration: none;
    transition: color 0.2s ease;
}

.mention-link:hover {
    color: rgb(124, 58, 237);
    text-decoration: underline;
}

.dark .mention-link {
    color: rgb(167, 139, 250);
}

.dark .mention-link:hover {
    color: rgb(196, 181, 253);
}
</style>