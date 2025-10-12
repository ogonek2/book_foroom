<template>
    <div>
        <!-- Main Reply Form -->
        <div class="reply-form active">
            <div class="reply-form-content">
                <h4 class="text-lg font-semibold mb-3">Ваша відповідь</h4>
                <div class="reply-input-wrapper">
                    <textarea v-model="mainReplyContent" 
                              ref="mainReplyTextarea"
                              rows="1" 
                              class="reply-textarea"
                              placeholder="Напишіть вашу відповідь..." 
                              required></textarea>
                    <div class="reply-buttons">
                        <button type="button" 
                                @click="submitMainReply"
                                :disabled="isMainReplySubmitting || !mainReplyContent.trim()"
                                class="submit-btn">
                            {{ isMainReplySubmitting ? 'Відправляємо...' : 'Відправити' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Comments Section -->
        <div class="comments-section">
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
        <div v-else class="empty-state">
            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
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
        };
    },
    computed: {
        isAuthenticated() {
            return this.currentUserId !== null;
        }
    },
    methods: {
        async submitMainReply() {
            if (!this.mainReplyContent.trim()) {
                this.showNotification('Будь ласка, введіть текст відповіді.', 'error');
                return;
            }
            this.isMainReplySubmitting = true;
            try {
                const response = await axios.post(`/books/${this.bookSlug}/reviews/${this.reviewId}/replies`, {
                    content: this.mainReplyContent
                });
                if (response.data.success) {
                    this.mainReplyContent = '';
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
/* All styles are inherited from the parent page */
</style>
