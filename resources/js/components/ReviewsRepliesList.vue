<template>
    <div>
        <!-- Main Reply Form -->
        <div v-if="isAuthenticated" class="reply-form active">
            <div class="reply-form-content">
                <h4 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Ваша відповідь</h4>
                <div class="reply-input-wrapper">
                    <textarea v-model="mainReplyContent" 
                              ref="mainReplyTextarea"
                              rows="1"
                              :maxlength="maxLength"
                              class="reply-textarea w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 resize-none"
                              :class="{ 'border-red-300 dark:border-red-700 focus:ring-red-500': mainReplyContent.length >= maxLength }"
                              placeholder="Напишіть вашу відповідь..." 
                              @focus="expandTextarea"
                              @blur="collapseTextarea"
                              @input="validateLength"
                              required></textarea>
                    <div class="flex items-center justify-between mt-2">
                        <div class="text-xs text-slate-500 dark:text-slate-400" :class="{ 'text-red-500 dark:text-red-400': mainReplyContent.length >= maxLength }">
                            {{ mainReplyContent.length }}/{{ maxLength }} символів
                        </div>
                    </div>
                    <div class="reply-buttons mt-3 flex justify-end">
                        <button type="button" 
                                @click="submitMainReply"
                                :disabled="isMainReplySubmitting || !mainReplyContent.trim() || mainReplyContent.length > maxLength"
                                class="inline-flex items-center px-5 py-2 rounded-xl text-white bg-gradient-to-r from-indigo-500 to-purple-600 shadow-lg hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <span v-if="isMainReplySubmitting" class="mr-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            {{ isMainReplySubmitting ? 'Відправляємо...' : 'Відправити' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- CTA для неавторизованных пользователей -->
        <div v-else class="mb-8">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 border border-indigo-200 dark:border-indigo-800 rounded-2xl p-8 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-comments text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                        Увійдіть, щоб читати та писати коментарі
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400 mb-6">
                        Зареєструйтеся або увійдіть в систему, щоб брати участь в обговоренні та читати всі коментарі
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="/login"
                           class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Увійти
                        </a>
                        <a href="/register"
                           class="inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 border-2 border-indigo-500 dark:border-indigo-600 rounded-xl font-semibold hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all duration-300">
                            <i class="fas fa-user-plus mr-2"></i>
                            Зареєструватися
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Comments Section -->
        <div v-if="isAuthenticated" class="comments-section">
            <h2 class="text-md font-bold mb-4 flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Обговорення (<span class="replies-count">{{ totalRepliesCount }}</span>)
            </h2>
        
        <div v-if="localReplies.length > 0" class="comments-tree" id="comments-container">
            <review-reply v-for="reply in localReplies"
                          :key="reply.id"
                          :reply="reply"
                          :level="0"
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
            <div v-else class="text-center py-4">
                <i class="fas fa-comments text-4xl mb-4 text-slate-400 dark:text-slate-500"></i>
                <h3 class="text-xl font-semibold mb-2">Поки немає відповідей</h3>
                <p class="mb-4">Станьте першим, хто поділиться своєю думкою</p>
            </div>
        </div>
    </div>
</template>

<script>
import ReviewReply from './ReviewReply.vue';
import axios from 'axios';

export default {
    name: 'ReviewsRepliesList',
    components: {
        ReviewReply
    },
    props: {
        replies: {
            type: [Array, Object],
            default: () => []
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
            localReplies: Array.isArray(this.replies) ? [...this.replies] : Object.values(this.replies || {}),
            totalRepliesCount: 0,
            mainReplyContent: '',
            isMainReplySubmitting: false,
            maxLength: 400,
        };
    },
    computed: {
        isAuthenticated() {
            return this.currentUserId !== null && this.currentUserId !== undefined && this.currentUserId !== '';
        }
    },
    methods: {
        expandTextarea() {
            if (this.$refs.mainReplyTextarea) {
                this.$refs.mainReplyTextarea.rows = 3;
            }
        },
        collapseTextarea() {
            if (this.$refs.mainReplyTextarea && !this.mainReplyContent.trim()) {
                this.$refs.mainReplyTextarea.rows = 1;
            }
        },
        validateLength() {
            if (this.mainReplyContent.length > this.maxLength) {
                this.mainReplyContent = this.mainReplyContent.substring(0, this.maxLength);
            }
        },
        async submitMainReply() {
            if (!this.mainReplyContent.trim()) {
                this.showNotification('Будь ласка, введіть текст відповіді.', 'error');
                return;
            }
            if (this.mainReplyContent.length > this.maxLength) {
                this.showNotification(`Відповідь не може перевищувати ${this.maxLength} символів.`, 'error');
                return;
            }
            this.isMainReplySubmitting = true;
            try {
                const response = await axios.post(`/books/${this.bookSlug}/reviews/${this.reviewId}/replies`, {
                    content: this.mainReplyContent
                });
                if (response.data.success) {
                    this.mainReplyContent = '';
                    if (this.$refs.mainReplyTextarea) {
                        this.$refs.mainReplyTextarea.rows = 1;
                    }
                    this.handleReplyAdded(response.data.reply);
                    this.showNotification('Відповідь успішно додано!', 'success');
                } else {
                    this.showNotification(response.data.message || 'Помилка при відправці відповіді.', 'error');
                }
            } catch (error) {
                console.error('Error submitting main reply:', error);
                this.showNotification(error.response?.data?.message || 'Помилка при відправці відповіді. Спробуйте ще раз.', 'error');
            } finally {
                this.isMainReplySubmitting = false;
            }
        },
        handleReplyAdded(newReply) {
            // This is for nested replies being added
            // If the new reply is a direct child of the main review (level 0), add it to localReplies
            if (newReply.parent_id === this.reviewId) {
                this.localReplies.unshift(newReply);
                this.totalRepliesCount++;
            } else {
                // Find the parent and add the nested reply
                this.addNestedReply(this.localReplies, newReply);
                this.totalRepliesCount++;
            }
            this.updateRepliesCount();
        },
        addNestedReply(replies, newReply) {
            for (let i = 0; i < replies.length; i++) {
                if (replies[i].id === newReply.parent_id) {
                    if (!replies[i].replies) {
                        this.$set(replies[i], 'replies', []);
                    }
                    replies[i].replies.unshift(newReply);
                    this.$set(replies[i], 'replies_count', (replies[i].replies_count || 0) + 1);
                    return;
                }
                if (replies[i].replies && replies[i].replies.length > 0) {
                    this.addNestedReply(replies[i].replies, newReply);
                }
            }
        },
        handleReplyUpdated(updatedReply) {
            // Find and update the reply in localReplies or its nested replies
            this.updateReplyInList(this.localReplies, updatedReply);
        },
        updateReplyInList(replies, updatedReply) {
            for (let i = 0; i < replies.length; i++) {
                if (replies[i].id === updatedReply.id) {
                    this.$set(replies, i, updatedReply);
                    return;
                }
                if (replies[i].replies && replies[i].replies.length > 0) {
                    this.updateReplyInList(replies[i].replies, updatedReply);
                }
            }
        },
        handleReplyDeleted(deletedReplyId) {
            this.removeReplyFromList(this.localReplies, deletedReplyId);
            this.totalRepliesCount--;
            this.updateRepliesCount();
        },
        removeReplyFromList(replies, deletedReplyId) {
            for (let i = 0; i < replies.length; i++) {
                if (replies[i].id === deletedReplyId) {
                    replies.splice(i, 1);
                    return;
                }
                if (replies[i].replies && replies[i].replies.length > 0) {
                    const initialLength = replies[i].replies.length;
                    this.removeReplyFromList(replies[i].replies, deletedReplyId);
                    if (replies[i].replies.length < initialLength) {
                        this.$set(replies[i], 'replies_count', Math.max(0, (replies[i].replies_count || 0) - 1));
                    }
                }
            }
        },
        handleLikeToggled(payload) {
            // Update like state in the reply
            this.updateLikeInList(this.localReplies, payload);
        },
        updateLikeInList(replies, payload) {
            for (let i = 0; i < replies.length; i++) {
                if (replies[i].id === payload.replyId) {
                    this.$set(replies[i], 'is_liked_by_current_user', payload.isLiked);
                    this.$set(replies[i], 'likes_count', payload.likesCount);
                    return;
                }
                if (replies[i].replies && replies[i].replies.length > 0) {
                    this.updateLikeInList(replies[i].replies, payload);
                }
            }
        },
        updateRepliesCount() {
            // Update the total count in the UI
            const countElements = document.querySelectorAll('.replies-count');
            countElements.forEach(el => {
                el.textContent = this.totalRepliesCount;
            });
        },
        showNotification(message, type = 'info') {
            // Создаем красивое уведомление вместо alert
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
            
            // Анимация появления
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Автоматическое скрытие через 3 секунды
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        },
        calculateTotalReplies() {
            let count = 0;
            const countReplies = (replies) => {
                replies.forEach(reply => {
                    count++;
                    if (reply.replies && reply.replies.length > 0) {
                        countReplies(reply.replies);
                    }
                });
            };
            countReplies(this.localReplies);
            this.totalRepliesCount = count;
        }
    },
    created() {
        // Calculate total replies count including nested ones
        this.calculateTotalReplies();
    }
};
</script>

<style scoped>
.reply-form {
    margin-bottom: 2rem;
}

.reply-form-content {
    padding: 0;
}

.reply-input-wrapper {
    display: flex;
    flex-direction: column;
}

.reply-textarea {
    min-height: 2.5rem;
    line-height: 1.5;
}

.reply-textarea:focus {
    min-height: 5rem;
}

.reply-buttons {
    display: flex;
    justify-content: flex-end;
}
</style>
