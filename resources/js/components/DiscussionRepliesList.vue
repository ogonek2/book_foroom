<template>
    <div>
        <!-- Reply Form -->
        <div v-if="isAuthenticated && !isDiscussionClosed" class="mb-6">
            <form @submit.prevent="submitMainReply" class="space-y-4">
                <div class="relative">
                    <div class="relative">
                        <textarea v-model="mainReplyContent"
                                  @input="handleMainReplyInput"
                                  @keydown="handleMainReplyKeydown"
                                  ref="mainReplyTextarea"
                                  rows="1" 
                                  placeholder="Що ви думаєте про це? (використовуйте @ для згадування користувачів)"
                                  class="w-full px-4 py-4 bg-light-bg-secondary dark:bg-gray-700 border border-light-border dark:border-dark-border rounded-xl text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none transition-colors"
                                  required></textarea>
                        
                        <!-- Mention Dropdown -->
                        <div v-if="showMentionDropdown && mentionUsers.length > 0" 
                             class="absolute z-[9999] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl max-h-60 overflow-y-auto mt-1"
                             :style="{ 
                                 top: '100%',
                                 left: '0px', 
                                 minWidth: '250px',
                                 maxWidth: '300px'
                             }">
                        <div v-for="(user, index) in mentionUsers" 
                             :key="user.id"
                             @click="insertMainMention(user.username)"
                             @mouseenter="selectedMentionIndex = index"
                             :class="[
                                 'px-4 py-3 flex items-center space-x-3 cursor-pointer transition-colors',
                                 selectedMentionIndex === index 
                                     ? 'bg-brand-50 dark:bg-brand-900/20' 
                                     : 'hover:bg-gray-50 dark:hover:bg-gray-700'
                             ]">
                            <img :src="user.avatar_display || user.avatar || '/storage/avatars/default.png'" 
                                 :alt="user.name"
                                 class="w-8 h-8 rounded-full flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ user.name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    @{{ user.username }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                            :disabled="isSubmitting || !mainReplyContent.trim()"
                            class="bg-gradient-to-r from-brand-500 to-accent-500 text-white px-6 py-3 rounded-xl font-medium hover:from-brand-600 hover:to-accent-600 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ isSubmitting ? 'Відправляємо...' : 'Відправити' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- CTA для неавторизованных пользователей -->
        <div v-if="!isAuthenticated" class="mb-8">
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

        <!-- Сообщение о закрытом обсуждении (только для авторизованных) -->
        <div v-if="isAuthenticated && isDiscussionClosed" class="mb-8">
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-center">
                <i class="fas fa-lock text-red-500 dark:text-red-400 text-2xl mb-2"></i>
                <p class="text-red-600 dark:text-red-400">
                    Це обговорення закрито для нових відповідей
                </p>
            </div>
        </div>

        <!-- Replies Section -->
        <div v-if="isAuthenticated" id="replies-section">
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
            isSubmitting: false,
            // Mention autocomplete
            mentionUsers: [],
            showMentionDropdown: false,
            mentionPosition: { top: 0, left: 0 },
            mentionQuery: '',
            selectedMentionIndex: -1,
            mentionStartPos: -1,
            mentionSearchTimeout: null,
        };
    },
    computed: {
        isAuthenticated() {
            return this.currentUserId !== null && this.currentUserId !== undefined && this.currentUserId !== '';
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
        
        handleMainReplyInput(event) {
            const textarea = event.target;
            if (!textarea) return;
            
            setTimeout(() => {
                const cursorPos = textarea.selectionStart;
                const currentValue = textarea.value || this.mainReplyContent;
                const textBeforeCursor = currentValue.substring(0, cursorPos);
                
                console.log('DiscussionRepliesList - Input detected:', { cursorPos, currentValue, textBeforeCursor });
                
                const atIndex = textBeforeCursor.lastIndexOf('@');
                
                if (atIndex === -1) {
                    console.log('No @ found, hiding dropdown');
                    this.hideMentionDropdown();
                    return;
                }
                
                const textAfterAt = textBeforeCursor.substring(atIndex + 1);
                if (textAfterAt.includes(' ') || textAfterAt.includes('\n')) {
                    console.log('Space or newline after @, hiding dropdown');
                    this.hideMentionDropdown();
                    return;
                }
                
                const query = textAfterAt.trim();
                console.log('Searching users with query:', query);
                this.mentionQuery = query;
                this.mentionStartPos = atIndex;
                this.searchUsers(query);
                this.calculateMentionPosition(textarea);
            }, 10);
        },
        
        async searchUsers(query) {
            if (this.mentionSearchTimeout) {
                clearTimeout(this.mentionSearchTimeout);
            }
            
            console.log('DiscussionRepliesList - searchUsers called with query:', query);
            
            this.mentionSearchTimeout = setTimeout(async () => {
                try {
                    const url = `/api/users/search${query ? '?q=' + encodeURIComponent(query) : ''}`;
                    console.log('Fetching from URL:', url);
                    const response = await fetch(url);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    console.log('Users received:', data.users);
                    this.mentionUsers = data.users || [];
                    this.showMentionDropdown = this.mentionUsers.length > 0;
                    this.selectedMentionIndex = -1;
                    
                    console.log('Dropdown state:', { 
                        showMentionDropdown: this.showMentionDropdown, 
                        usersCount: this.mentionUsers.length 
                    });
                    
                    if (this.showMentionDropdown) {
                        this.$nextTick(() => {
                            const textarea = this.$refs.mainReplyTextarea;
                            if (textarea) {
                                this.calculateMentionPosition(textarea);
                            }
                        });
                    }
                } catch (error) {
                    console.error('Error searching users:', error);
                    this.mentionUsers = [];
                    this.showMentionDropdown = false;
                }
            }, 200);
        },
        
        calculateMentionPosition(textarea) {
            // Позиционирование не нужно, используется top: 100% для размещения под textarea
            // Метод оставлен для совместимости, но не используется
        },
        
        insertMainMention(username) {
            const textarea = this.$refs.mainReplyTextarea;
            if (!textarea) return;
            
            const cursorPos = textarea.selectionStart;
            const currentValue = textarea.value || this.mainReplyContent;
            const textBeforeCursor = currentValue.substring(0, cursorPos);
            const textAfterCursor = currentValue.substring(cursorPos);
            
            const atIndex = textBeforeCursor.lastIndexOf('@');
            if (atIndex === -1) return;
            
            const beforeMention = currentValue.substring(0, atIndex);
            const afterMention = textAfterCursor;
            
            this.mainReplyContent = beforeMention + '@' + username + ' ' + afterMention;
            
            this.$nextTick(() => {
                const newCursorPos = atIndex + username.length + 2;
                textarea.setSelectionRange(newCursorPos, newCursorPos);
                textarea.focus();
            });
            
            this.hideMentionDropdown();
        },
        
        handleMainReplyKeydown(event) {
            if (!this.showMentionDropdown || this.mentionUsers.length === 0) {
                return;
            }
            
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                this.selectedMentionIndex = Math.min(
                    this.selectedMentionIndex + 1,
                    this.mentionUsers.length - 1
                );
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                this.selectedMentionIndex = Math.max(this.selectedMentionIndex - 1, -1);
            } else if (event.key === 'Enter' || event.key === 'Tab') {
                if (this.selectedMentionIndex >= 0 && this.selectedMentionIndex < this.mentionUsers.length) {
                    event.preventDefault();
                    const selectedUser = this.mentionUsers[this.selectedMentionIndex];
                    this.insertMainMention(selectedUser.username);
                }
            } else if (event.key === 'Escape') {
                this.hideMentionDropdown();
            }
        },
        
        hideMentionDropdown() {
            this.showMentionDropdown = false;
            this.mentionUsers = [];
            this.selectedMentionIndex = -1;
            this.mentionQuery = '';
            this.mentionStartPos = -1;
            if (this.mentionSearchTimeout) {
                clearTimeout(this.mentionSearchTimeout);
                this.mentionSearchTimeout = null;
            }
        },
        
        async submitMainReply() {
            if (!this.mainReplyContent.trim() || this.isSubmitting) return;

            this.hideMentionDropdown();
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