<template>
    <div>
        <!-- Reply Form -->
        <div v-if="isAuthenticated && !isDiscussionClosed" class="mb-6">
            <form @submit.prevent="submitMainReply" class="space-y-4">
                <textarea v-model="mainReplyContent"
                          rows="1" 
                          placeholder="Що ви думаєте про це?"
                          class="w-full px-4 py-4 bg-light-bg-secondary dark:bg-gray-700 border border-light-border dark:border-dark-border rounded-xl text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none transition-colors"
                          required></textarea>
                <div class="flex justify-end">
                    <button type="submit"
                            :disabled="isSubmitting || !mainReplyContent.trim()"
                            class="bg-gradient-to-r from-brand-500 to-accent-500 text-white px-6 py-3 rounded-xl font-medium hover:from-brand-600 hover:to-accent-600 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ isSubmitting ? 'Відправляємо...' : 'Відправити' }}
                    </button>
                </div>
            </form>
        </div>

        <div v-else-if="!isAuthenticated" class="mt-8 pt-8 text-center">
            <p class="text-light-text-secondary dark:text-dark-text-secondary mb-4">
                Увійдіть в систему, щоб відповісти на обговорення
            </p>
            <a href="/login"
               class="bg-gradient-to-r from-brand-500 to-accent-500 text-white px-6 py-3 rounded-xl font-medium hover:from-brand-600 hover:to-accent-600 transition-all duration-300 inline-block">
                Войти
            </a>
        </div>

        <div v-else-if="isDiscussionClosed" class="mt-8 pt-8 text-center">
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                <i class="fas fa-lock text-red-500 dark:text-red-400 text-2xl mb-2"></i>
                <p class="text-red-600 dark:text-red-400">
                    Це обговорення закрито для нових відповідей
                </p>
            </div>
        </div>

        <!-- Replies Section -->
        <div id="replies-section">
            <div class="flex items-center justify-between mb-6">
                <span class="text-lg font-bold text-light-text-primary dark:text-dark-text-primary">
                    Відповіді (<span id="replies-count">{{ localReplies.length }}</span>)
                </span>
            </div>

            <div class="space-y-6" id="replies-container">
                <template v-if="localReplies.length > 0">
                    <DiscussionReply v-for="reply in localReplies"
                                     :key="reply.id"
                                     :reply="reply"
                                     :level="0"
                                     :highlighted-reply-id="highlightedReplyId"
                                     :discussion-slug="discussionSlug"
                                     :current-user-id="currentUserId"
                                     :is-discussion-closed="isDiscussionClosed"
                                     :is-moderator="isModerator"
                                     @reply-added="handleReplyAdded"
                                     @reply-updated="handleReplyUpdated"
                                     @reply-deleted="handleReplyDeleted"
                                     @like-toggled="handleLikeToggled" />
                </template>

                <div v-else class="text-center py-12" id="no-replies-message">
                    <svg class="w-16 h-16 mx-auto mb-4 text-light-text-tertiary dark:text-dark-text-tertiary"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                    <h3 class="text-xl font-semibold text-light-text-secondary dark:text-dark-text-secondary mb-2">
                        Поки що немає відповідей
                    </h3>
                    <p class="text-light-text-tertiary dark:text-dark-text-tertiary">
                        Ставте першим, хто відповість на це обговорення!
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DiscussionReply from './DiscussionReply.vue';
import axios from 'axios';

export default {
    name: 'DiscussionRepliesList',
    components: {
        DiscussionReply
    },
    props: {
        replies: {
            type: [Array, Object],
            default: () => []
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
            localReplies: Array.isArray(this.replies) ? [...this.replies] : Object.values(this.replies || {}),
            mainReplyContent: '',
            isSubmitting: false
        };
    },
    computed: {
        isAuthenticated() {
            return this.currentUserId !== null;
        }
    },
    mounted() {
        // Handle hash anchor on mount
        this.handleHashAnchor();
        
        // Listen for hash changes
        window.addEventListener('hashchange', this.handleHashAnchor);
    },
    beforeDestroy() {
        window.removeEventListener('hashchange', this.handleHashAnchor);
    },
    methods: {
        handleHashAnchor() {
            const hash = window.location.hash;
            if (!hash || !hash.startsWith('#reply-')) {
                return;
            }
            
            const replyId = parseInt(hash.replace('#reply-', ''));
            if (!replyId) {
                return;
            }
            
            // Wait for DOM to be ready
            this.$nextTick(() => {
                setTimeout(() => {
                    this.scrollToReply(replyId);
                }, 300);
            });
        },
        
        scrollToReply(replyId) {
            // Find the reply element
            const replyElement = document.querySelector(`[data-reply-id="${replyId}"]`);
            if (!replyElement) {
                // If reply is nested, we need to expand parent replies
                this.expandToReply(replyId);
                return;
            }
            
            // Expand all parent replies to make this reply visible
            this.expandParentReplies(replyId);
            
            // Scroll to the reply
            setTimeout(() => {
                replyElement.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                
                // Highlight the reply
                replyElement.classList.add('highlighted-reply');
                setTimeout(() => {
                    replyElement.classList.remove('highlighted-reply');
                }, 3000);
            }, 200);
        },
        
        expandToReply(replyId) {
            // Recursively find and expand parent replies
            const findAndExpand = (replies, targetId, parentIds = []) => {
                for (const reply of replies) {
                    if (reply.id === targetId) {
                        // Found it! Expand all parents
                        parentIds.forEach(pid => {
                            this.expandReplyById(pid);
                        });
                        return true;
                    }
                    if (reply.replies && reply.replies.length > 0) {
                        if (findAndExpand(reply.replies, targetId, [...parentIds, reply.id])) {
                            return true;
                        }
                    }
                }
                return false;
            };
            
            findAndExpand(this.localReplies, replyId);
            
            // Try to scroll after expanding
            setTimeout(() => {
                this.scrollToReply(replyId);
            }, 500);
        },
        
        expandParentReplies(replyId) {
            // Find all parent IDs leading to this reply
            const findParents = (replies, targetId, parents = []) => {
                for (const reply of replies) {
                    if (reply.id === targetId) {
                        return parents;
                    }
                    if (reply.replies && reply.replies.length > 0) {
                        const found = findParents(reply.replies, targetId, [...parents, reply.id]);
                        if (found !== null) {
                            return found;
                        }
                    }
                }
                return null;
            };
            
            const parentIds = findParents(this.localReplies, replyId);
            if (parentIds) {
                parentIds.forEach(pid => {
                    this.expandReplyById(pid);
                });
            }
        },
        
        expandReplyById(replyId) {
            // Find reply component via refs and expand it
            const findAndExpand = (components) => {
                if (!components || !Array.isArray(components)) return false;
                
                for (const component of components) {
                    if (component && component.reply && component.reply.id === replyId) {
                        // Found the target reply - expand it if it has nested replies
                        if (component.expandReplies) {
                            component.expandReplies();
                        }
                        return true;
                    }
                    
                    // Check nested replies
                    if (component && component.$refs && component.$refs.replyComponents) {
                        if (findAndExpand(component.$refs.replyComponents)) {
                            // If found in nested, expand this parent
                            if (component.expandReplies) {
                                component.expandReplies();
                            }
                            return true;
                        }
                    }
                }
                return false;
            };
            
            // Try to find via refs
            if (this.$refs.replyComponents) {
                findAndExpand(this.$refs.replyComponents);
            }
        },
        
        async submitMainReply() {
            if (!this.mainReplyContent.trim() || this.isSubmitting) return;

            this.isSubmitting = true;

            try {
                const response = await axios.post(`/discussions/${this.discussionSlug}/replies`, {
                    content: this.mainReplyContent
                });

                if (response.data.success) {
                    // Add new reply to the beginning of the list
                    this.localReplies.unshift(response.data.reply);
                    this.mainReplyContent = '';

                    // Update discussion replies count in the stats
                    this.updateDiscussionRepliesCount();

                    this.showNotification('Відповідь успішно додано!', 'success');

                    // Scroll to the new reply smoothly
                    setTimeout(() => {
                        const repliesContainer = document.querySelector('#replies-container');
                        const firstReply = repliesContainer?.firstElementChild;
                        if (firstReply) {
                            firstReply.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    }, 100);
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
        
        handleReplyAdded(reply, parentId) {
            // Find the parent reply and add the new reply to its children
            const addReplyToParent = (replies) => {
                for (const r of replies) {
                    if (r.id === parentId) {
                        if (!r.replies) {
                            this.$set(r, 'replies', []);
                        }
                        r.replies.unshift(reply);
                        return true;
                    }
                    if (r.replies && r.replies.length > 0) {
                        if (addReplyToParent(r.replies)) {
                            return true;
                        }
                    }
                }
                return false;
            };

            addReplyToParent(this.localReplies);
            this.updateDiscussionRepliesCount();
        },
        
        handleReplyUpdated(updatedReply) {
            // Find and update the reply in the tree
            const updateReplyInTree = (replies) => {
                for (let i = 0; i < replies.length; i++) {
                    if (replies[i].id === updatedReply.id) {
                        this.$set(replies, i, { ...replies[i], ...updatedReply });
                        return true;
                    }
                    if (replies[i].replies && replies[i].replies.length > 0) {
                        if (updateReplyInTree(replies[i].replies)) {
                            return true;
                        }
                    }
                }
                return false;
            };

            updateReplyInTree(this.localReplies);
        },
        
        handleReplyDeleted(replyId) {
            // Find and remove the reply from the tree
            const deleteReplyFromTree = (replies) => {
                for (let i = 0; i < replies.length; i++) {
                    if (replies[i].id === replyId) {
                        replies.splice(i, 1);
                        return true;
                    }
                    if (replies[i].replies && replies[i].replies.length > 0) {
                        if (deleteReplyFromTree(replies[i].replies)) {
                            return true;
                        }
                    }
                }
                return false;
            };

            deleteReplyFromTree(this.localReplies);
            this.updateDiscussionRepliesCount();
        },
        
        handleLikeToggled(replyId, liked, count) {
            // Update like status in the tree
            const updateLikeInTree = (replies) => {
                for (const reply of replies) {
                    if (reply.id === replyId) {
                        this.$set(reply, 'is_liked', liked);
                        this.$set(reply, 'likes_count', count);
                        return true;
                    }
                    if (reply.replies && reply.replies.length > 0) {
                        if (updateLikeInTree(reply.replies)) {
                            return true;
                        }
                    }
                }
                return false;
            };

            updateLikeInTree(this.localReplies);
        },
        
        updateDiscussionRepliesCount() {
            // Count all replies recursively
            const countReplies = (replies) => {
                let count = replies.length;
                for (const reply of replies) {
                    if (reply.replies && reply.replies.length > 0) {
                        count += countReplies(reply.replies);
                    }
                }
                return count;
            };

            const totalCount = countReplies(this.localReplies);

            // Update the replies count in the "Відповіді" section header
            const repliesCountSpan = document.querySelector('#replies-count');
            if (repliesCountSpan) {
                repliesCountSpan.textContent = totalCount;
            }

            // Update the replies count in the discussion stats (with icon)
            const statsCountElement = document.querySelector('.flex.items-center.space-x-2 .fas.fa-comments');
            if (statsCountElement) {
                const countSpan = statsCountElement.parentElement?.querySelector('span');
                if (countSpan) {
                    countSpan.textContent = `${totalCount} Відповідей`;
                }
            }
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
    }
};
</script>

<style scoped>
/* Component-specific styles if needed */
</style>