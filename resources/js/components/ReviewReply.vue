<template>
    <div class="review-reply" :data-reply-id="reply.id" :data-depth="level">
        <div class="rounded-xl p-0 py-1 sm:py-2 px-1 sm:px-2">
            <!-- Header -->
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <template v-if="!reply.is_guest && reply.user && reply.user.username">
                        <a :href="profileUrl(reply.user.username)"
                           class="flex items-center space-x-2 sm:space-x-3 group"
                           @click.stop>
                            <div>
                                <img v-if="reply.user.avatar_display" 
                                     :src="reply.user.avatar_display"
                                     :alt="reply.user.name"
                                     class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex-shrink-0 transition-transform duration-200 group-hover:scale-110">
                                <div v-else class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center text-white font-bold text-xs flex-shrink-0 transition-transform duration-200 group-hover:scale-110">
                                    {{ (reply.user?.name || 'U').charAt(0).toUpperCase() }}
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center space-x-2">
                                    <div class="text-light-text-primary dark:text-dark-text-primary font-medium text-sm truncate group-hover:text-brand-500 dark:group-hover:text-brand-400 transition-colors">
                                        {{ reply.user?.name || 'Користувач' }}
                                    </div>
                                    <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs sm:text-sm">
                                        {{ formatDate(reply.created_at) }}
                                    </div>
                                </div>
                                <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs truncate group-hover:text-brand-500 dark:group-hover:text-brand-400 transition-colors">
                                    {{ '@' + reply.user.username }}
                                </div>
                            </div>
                        </a>
                    </template>
                    <template v-else>
                        <template v-if="reply.user && reply.user.avatar_display">
                            <img :src="reply.user.avatar_display" 
                                 :alt="reply.user?.name || 'Користувач'"
                                 class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex-shrink-0">
                        </template>
                        <div v-else-if="reply.is_guest" class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                            Г
                        </div>
                        <div v-else class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                            {{ (reply.user?.name || 'U').charAt(0).toUpperCase() }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center space-x-2">
                                <div class="text-light-text-primary dark:text-dark-text-primary font-medium text-sm truncate">
                                    {{ reply.is_guest ? 'Гість' : (reply.user?.name || 'Користувач') }}
                                    <span v-if="reply.is_guest" class="ml-2 px-2 py-0.5 bg-orange-500 text-white text-sm rounded-full">Гість</span>
                                </div>
                                <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs sm:text-sm">
                                    {{ formatDate(reply.created_at) }}
                                </div>
                            </div>
                            <a v-if="reply.user && reply.user.username"
                               :href="profileUrl(reply.user.username)"
                               class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs truncate block">
                                {{ '@' + reply.user.username }}
                            </a>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Reply Content -->
            <div v-if="!isEditing" 
                 class="text-light-text-primary dark:text-dark-text-primary leading-relaxed text-sm">
                {{ reply.content }}
            </div>

            <!-- Edit Form -->
            <div v-if="isEditing" class="mb-3 sm:mb-4">
                <textarea v-model="editContent"
                          ref="editContentTextarea"
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
                    <button @click="showReplyForm = !showReplyForm"
                            class="flex items-center space-x-1 text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 transition-colors text-xs sm:text-sm">
                        <i class="fas fa-reply text-xs"></i>
                        <span>Відповісти</span>
                    </button>

                    <!-- Edit Button (for owner) -->
                    <button v-if="canEdit" @click="startEdit"
                            class="text-light-text-secondary dark:text-dark-text-secondary hover:text-blue-500 dark:hover:text-blue-400 transition-colors p-1">
                        <i class="fas fa-edit text-xs"></i>
                    </button>

                    <!-- Delete Button (for owner) -->
                    <button v-if="canEdit" @click="deleteReply"
                            class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors p-1">
                        <i class="fas fa-trash text-xs"></i>
                    </button>

                    <!-- Report Button -->
                    <report-button 
                        :reportable-type="'App\\Models\\Review'"
                        :reportable-id="reply.id"
                        :content-preview="getContentPreview()"
                        :content-url="getContentUrl()">
                    </report-button>
                </div>
            </div>

            <!-- Reply Form -->
            <div v-if="showReplyForm" class="mb-3 sm:mb-4 mt-3">
                <textarea v-model="replyContent"
                          ref="replyContentTextarea"
                          class="w-full p-3 sm:p-4 bg-light-bg-secondary dark:bg-gray-700 rounded-xl text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none transition-colors text-sm sm:text-base"
                          rows="3" 
                          placeholder="Напишіть відповідь..."></textarea>
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
                <review-reply v-for="nestedReply in reply.replies"
                              :key="nestedReply.id"
                              :reply="nestedReply"
                              :level="level + 1"
                              :book-slug="bookSlug"
                              :review-id="reviewId"
                              :current-user-id="currentUserId"
                              :is-moderator="isModerator"
                              @reply-added="handleReplyAdded"
                              @reply-updated="handleReplyUpdated"
                              @reply-deleted="handleReplyDeleted"
                              @like-toggled="handleLikeToggled"
                              @show-notification="showNotification" />
            </div>
        </div>

    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ReviewReply',
    props: {
        reply: {
            type: Object,
            required: true
        },
        level: {
            type: Number,
            default: 0
        },
        bookSlug: {
            type: String,
            required: true
        },
        reviewId: {
            type: Number,
            required: true
        },
        currentUserId: {
            type: Number,
            default: null
        },
        isModerator: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            repliesExpanded: false,
            isLiked: this.reply.is_liked_by_current_user || false,
            likesCount: this.reply.likes_count || 0,
            isEditing: false,
            editContent: this.reply.content,
            isSaving: false,
            showReplyForm: false,
            replyContent: '',
            isSubmitting: false,
        };
    },
    computed: {
        hasReplies() {
            return this.reply.replies && this.reply.replies.length > 0;
        },
        isAuthenticated() {
            return this.currentUserId !== null;
        },
        canEdit() {
            return this.isAuthenticated && (this.reply.user_id === this.currentUserId || this.isModerator);
        }
    },
    methods: {
        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000); // seconds

            if (diff < 60) return 'щойно';
            if (diff < 3600) return `${Math.floor(diff / 60)} хв тому`;
            if (diff < 86400) return `${Math.floor(diff / 3600)} год тому`;
            if (diff < 604800) return `${Math.floor(diff / 86400)} дн тому`;
            
            return date.toLocaleDateString('uk-UA', {
                day: 'numeric',
                month: 'short',
                year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
            });
        },
        toggleReplies() {
            this.repliesExpanded = !this.repliesExpanded;
        },
        async toggleLike() {
            if (!this.isAuthenticated) {
                this.$emit('show-notification', 'Будь ласка, увійдіть, щоб поставити лайк.', 'error');
                return;
            }
            try {
                const response = await axios.post(`/books/${this.bookSlug}/reviews/${this.reply.id}/like`);
                if (response.data.success) {
                    this.isLiked = response.data.is_liked;
                    this.likesCount = response.data.likes_count;
                    this.$emit('like-toggled', { 
                        replyId: this.reply.id, 
                        isLiked: this.isLiked, 
                        likesCount: this.likesCount 
                    });
                }
            } catch (error) {
                console.error('Error toggling like:', error);
                this.$emit('show-notification', 'Помилка при зміні лайка.', 'error');
            }
        },
        startEdit() {
            this.isEditing = true;
            this.editContent = this.reply.content;
            this.$nextTick(() => {
                this.$refs.editContentTextarea.focus();
            });
        },
        cancelEdit() {
            this.isEditing = false;
            this.editContent = this.reply.content;
        },
        async saveEdit() {
            if (!this.editContent.trim()) {
                this.$emit('show-notification', 'Будь ласка, введіть текст коментаря.', 'error');
                return;
            }
            this.isSaving = true;
            try {
                const response = await axios.post(`/books/${this.bookSlug}/reviews/${this.reply.id}/update`, {
                    content: this.editContent
                });
                if (response.data.success) {
                    this.reply.content = response.data.content;
                    this.isEditing = false;
                    this.$emit('show-notification', 'Коментар оновлено!', 'success');
                    this.$emit('reply-updated', { ...this.reply, content: response.data.content });
                } else {
                    this.$emit('show-notification', response.data.message || 'Помилка при оновленні коментаря.', 'error');
                }
            } catch (error) {
                console.error('Error saving edit:', error);
                this.$emit('show-notification', error.response?.data?.message || 'Помилка при оновленні коментаря. Спробуйте ще раз.', 'error');
            } finally {
                this.isSaving = false;
            }
        },
        async deleteReply() {
            const confirmed = await confirm('Ви впевнені, що хочете видалити цей коментар?', 'Підтвердження', 'warning');
            if (!confirmed) return;
            try {
                const response = await axios.delete(`/books/${this.bookSlug}/reviews/${this.reply.id}/delete`);
                if (response.data.success) {
                    this.$emit('show-notification', 'Коментар видалено!', 'success');
                    this.$emit('reply-deleted', this.reply.id);
                } else {
                    this.$emit('show-notification', response.data.message || 'Помилка при видаленні коментаря.', 'error');
                }
            } catch (error) {
                console.error('Error deleting reply:', error);
                this.$emit('show-notification', error.response?.data?.message || 'Помилка при видаленні коментаря. Спробуйте ще раз.', 'error');
            }
        },
        toggleReplyForm() {
            this.showReplyForm = !this.showReplyForm;
            if (!this.showReplyForm) {
                this.replyContent = '';
            } else {
                this.$nextTick(() => {
                    this.$refs.replyContentTextarea.focus();
                });
            }
        },
        cancelReply() {
            this.showReplyForm = false;
            this.replyContent = '';
        },
        async submitReply() {
            if (!this.replyContent.trim()) {
                this.$emit('show-notification', 'Будь ласка, введіть текст відповіді.', 'error');
                return;
            }
            this.isSubmitting = true;
            try {
                const response = await axios.post(`/books/${this.bookSlug}/reviews/${this.reviewId}/replies`, {
                    content: this.replyContent,
                    parent_id: this.reply.id
                });
                if (response.data.success) {
                    this.replyContent = '';
                    this.showReplyForm = false;
                    this.$emit('reply-added', response.data.reply);
                    this.$emit('show-notification', 'Відповідь успішно додано!', 'success');
                } else {
                    this.$emit('show-notification', response.data.message || 'Помилка при відправці відповіді.', 'error');
                }
            } catch (error) {
                console.error('Error submitting reply:', error);
                this.$emit('show-notification', error.response?.data?.message || 'Помилка при відправці відповіді. Спробуйте ще раз.', 'error');
            } finally {
                this.isSubmitting = false;
            }
        },
        handleReplyAdded(newReply) {
            // Проверяем, является ли новый ответ прямым дочерним элементом
            if (newReply.parent_id === this.reply.id) {
                // Только если это прямой ребенок, добавляем к текущему компоненту
                if (!this.reply.replies) {
                    this.$set(this.reply, 'replies', []);
                }
                this.reply.replies.unshift(newReply);
                this.$set(this.reply, 'replies_count', (this.reply.replies_count || 0) + 1);
            }
            // Всегда пробрасываем событие выше для обновления общего счетчика
            this.$emit('reply-added', newReply);
        },
        handleReplyUpdated(updatedReply) {
            const index = this.reply.replies.findIndex(r => r.id === updatedReply.id);
            if (index !== -1) {
                this.$set(this.reply.replies, index, updatedReply);
            }
            this.$emit('reply-updated', updatedReply);
        },
        handleReplyDeleted(deletedReplyId) {
            this.reply.replies = this.reply.replies.filter(r => r.id !== deletedReplyId);
            this.$set(this.reply, 'replies_count', Math.max(0, (this.reply.replies_count || 0) - 1));
            this.$emit('reply-deleted', deletedReplyId);
        },
        handleLikeToggled(payload) {
            this.$emit('like-toggled', payload);
        },
        showNotification(message, type = 'info') {
            // Эмитим событие родителю для единообразия
            this.$emit('show-notification', message, type);
        },

        getContentPreview() {
            // Возвращаем превью контента для жалобы
            if (this.reply.content) {
                const text = this.reply.content;
                return text.substring(0, 100) + (text.length > 100 ? '...' : '');
            }
            return 'Відповідь на рецензію';
        },

        getContentUrl() {
            // Возвращаем URL контента
            return `/books/${this.bookSlug}/reviews/${this.reviewId}#reply-${this.reply.id}`;
        },
        profileUrl(username) {
            return username ? `/users/${username}` : '#';
        }
    }
};
</script>

<style scoped>
/* Review Reply Styles */
.review-reply {
    margin-bottom: 1rem;
    transition: all 0.2s ease-in-out;
}

.replies-container {
    transition: all 0.3s ease-in-out;
}

/* Depth Indicators for Visual Hierarchy */
.review-reply[data-depth="1"] {
    border-left: 1px solid hsl(220, 70%, 50%);
    padding-left: 0.5rem;
}

.review-reply[data-depth="2"] {
    border-left: 1px solid hsl(160, 70%, 50%);
    padding-left: 0.5rem;
}

.review-reply[data-depth="3"] {
    border-left: 1px solid hsl(30, 70%, 50%);
    padding-left: 0.5rem;
}

.review-reply[data-depth="4"] {
    border-left: 1px solid hsl(280, 70%, 50%);
    padding-left: 0.5rem;
}

.review-reply[data-depth="5"] {
    border-left: 1px solid hsl(0, 70%, 50%);
    padding-left: 0.5rem;
}

.review-reply[data-depth="6"] {
    border-left: 1px solid hsl(220, 70%, 50%);
    padding-left: 0.5rem;
    background: hsl(220, 70%, 5%);
}

.dark .review-reply[data-depth="6"] {
    background: hsl(220, 70%, 10%);
}

/* Mobile optimizations */
@media (max-width: 640px) {
    .review-reply {
        margin-bottom: 0.5rem;
    }

    .review-reply .rounded-xl {
        padding: 0.5rem;
    }

    .review-reply img {
        width: 1.5rem;
        height: 1.5rem;
    }

    .review-reply[data-depth="1"],
    .review-reply[data-depth="2"],
    .review-reply[data-depth="3"],
    .review-reply[data-depth="4"],
    .review-reply[data-depth="5"] {
        border-left-width: 1px;
        padding-left: 0.25rem;
    }
}
</style>
