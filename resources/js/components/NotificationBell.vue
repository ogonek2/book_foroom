<template>
    <div class="relative">
        <!-- Notification Bell Button -->
        <button @click="toggleDropdown" class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition-colors focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            
            <!-- Unread Badge -->
            <span v-if="unreadCount > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full">
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown Menu -->
        <transition name="dropdown">
            <div v-if="isOpen" class="absolute right-0 mt-2 w-80 md:w-96 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50" @click.stop>
                <!-- Header -->
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Сповіщення</h3>
                    <div class="flex items-center space-x-2">
                        <button v-if="unreadCount > 0" @click="markAllAsRead" class="text-xs text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-medium transition-colors">
                            Позначити всі
                        </button>
                        <button @click="deleteAllRead" class="text-xs text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 font-medium transition-colors">
                            Очистити
                        </button>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="max-h-96 overflow-y-auto">
                    <div v-if="loading" class="p-8 text-center">
                        <svg class="animate-spin h-8 w-8 text-purple-500 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">Завантаження...</p>
                    </div>

                    <div v-else-if="notifications.length === 0" class="p-8 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">Немає сповіщень</p>
                    </div>

                    <div v-else>
                        <div v-for="notification in notifications" :key="notification.id" 
                             @click="handleNotificationClick(notification)"
                             :class="[
                                 'px-4 py-3 border-b border-gray-100 dark:border-gray-700 cursor-pointer transition-all duration-200',
                                 notification.is_read ? 'bg-white dark:bg-gray-800' : 'bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30'
                             ]">
                            <div class="flex items-start space-x-3">
                                <!-- Icon -->
                                <div class="flex-shrink-0 mt-1">
                                    <div v-if="notification.type === 'review_reply' || notification.type === 'review_comment_reply'" class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div v-else-if="notification.type === 'discussion_reply' || notification.type === 'discussion_comment_reply'" class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div v-else class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ notification.message }}
                                    </p>
                                    <p v-if="notification.data && notification.data.reply_content" class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                        {{ notification.data.reply_content }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                        {{ formatTime(notification.created_at) }}
                                    </p>
                                </div>

                                <!-- Delete Button -->
                                <button @click.stop="deleteNotification(notification.id)" class="flex-shrink-0 p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Unread indicator -->
                            <div v-if="!notification.is_read" class="absolute left-2 top-1/2 transform -translate-y-1/2 w-2 h-2 bg-purple-500 rounded-full"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 text-center">
                    <a href="/notifications" class="text-sm text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-medium transition-colors">
                        Переглянути всі сповіщення
                    </a>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'NotificationBell',
    data() {
        return {
            isOpen: false,
            notifications: [],
            unreadCount: 0,
            loading: false,
            pollingInterval: null,
        };
    },
    mounted() {
        this.fetchNotifications();
        this.startPolling();
        
        // Close dropdown when clicking outside
        document.addEventListener('click', this.closeDropdown);
    },
    beforeDestroy() {
        this.stopPolling();
        document.removeEventListener('click', this.closeDropdown);
    },
    methods: {
        async fetchNotifications() {
            this.loading = true;
            try {
                const response = await axios.get('/notifications/api');
                this.notifications = response.data.notifications;
                this.unreadCount = response.data.unread_count;
            } catch (error) {
                console.error('Error fetching notifications:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async fetchUnreadCount() {
            try {
                const response = await axios.get('/notifications/unread-count');
                this.unreadCount = response.data.count;
            } catch (error) {
                console.error('Error fetching unread count:', error);
            }
        },
        
        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.fetchNotifications();
            }
        },
        
        closeDropdown(event) {
            if (!this.$el.contains(event.target)) {
                this.isOpen = false;
            }
        },
        
        async handleNotificationClick(notification) {
            // Mark as read
            if (!notification.is_read) {
                await this.markAsRead(notification.id);
            }
            
            // Navigate to the notification target
            const url = this.getNotificationUrl(notification);
            if (url) {
                window.location.href = url;
            }
        },
        
        getNotificationUrl(notification) {
            if (notification.type === 'review_reply' && notification.data) {
                // Используем book_slug (новые уведомления) или fallback на book_id (старые уведомления)
                const bookIdentifier = notification.data.book_slug || notification.data.book_id;
                return `/books/${bookIdentifier}/reviews/${notification.data.review_id}`;
            } else if (notification.type === 'review_comment_reply' && notification.data) {
                // Для ответов на коментарі до рецензії переходимо на окрему сторінку коментаря
                const bookIdentifier = notification.data.book_slug || notification.data.book_id;
                const commentId = notification.data.review_id; // ID комментария, на который ответили
                if (bookIdentifier && commentId) {
                    // Переходимо на окрему сторінку коментаря (з хлібними крихтами до рецензії)
                    return `/books/${bookIdentifier}/reviews/${commentId}`;
                }
            } else if (['review_like', 'review_like_milestone', 'review_comment_like'].includes(notification.type) && notification.data) {
                // Для лайков рецензий переходим на страницу рецензии
                const bookIdentifier = notification.data.book_slug || notification.data.book_id;
                const reviewId = notification.data.review_id || notification.data.likeable_id;
                if (bookIdentifier && reviewId) {
                    return `/books/${bookIdentifier}/reviews/${reviewId}`;
                }
            } else if (['discussion_reply', 'discussion_comment_reply'].includes(notification.type) && notification.data) {
                // Используем discussion_slug (новые уведомления) или fallback на discussion_id (старые уведомления)
                const discussionIdentifier = notification.data.discussion_slug || notification.data.discussion_id;
                // Для ответов используем отдельную страницу reply
                const replyId = notification.data.reply_id || notification.data.parent_reply_id;
                if (discussionIdentifier && replyId) {
                    return `/discussions/${discussionIdentifier}/replies/${replyId}`;
                } else if (discussionIdentifier) {
                    return `/discussions/${discussionIdentifier}`;
                }
            } else if (['discussion_like', 'discussion_like_milestone', 'discussion_comment_like', 'discussion_mention', 'discussion_reply_mention'].includes(notification.type) && notification.data) {
                // Используем discussion_slug для лайков и упоминаний
                const discussionIdentifier = notification.data.discussion_slug || notification.data.discussion_id;
                const replyId = notification.data.reply_id;
                // Для лайков ответов используем отдельную страницу reply
                if (discussionIdentifier && replyId) {
                    return `/discussions/${discussionIdentifier}/replies/${replyId}`;
                } else if (discussionIdentifier) {
                    return `/discussions/${discussionIdentifier}`;
                }
            }
            return null;
        },
        
        async markAsRead(id) {
            try {
                await axios.post(`/notifications/${id}/read`);
                
                // Update local state
                const notification = this.notifications.find(n => n.id === id);
                if (notification) {
                    notification.is_read = true;
                }
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },
        
        async markAllAsRead() {
            try {
                await axios.post('/notifications/mark-all-read');
                
                // Update local state
                this.notifications.forEach(n => n.is_read = true);
                this.unreadCount = 0;
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        },
        
        async deleteNotification(id) {
            try {
                await axios.delete(`/notifications/${id}`);
                
                // Remove from local state
                const index = this.notifications.findIndex(n => n.id === id);
                if (index !== -1) {
                    if (!this.notifications[index].is_read) {
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                    this.notifications.splice(index, 1);
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
            }
        },
        
        async deleteAllRead() {
            const confirmed = await confirm('Ви впевнені, що хочете видалити всі прочитані сповіщення?', 'Підтвердження', 'warning');
            if (!confirmed) {
                return;
            }
            
            try {
                await axios.delete('/notifications/read/all');
                
                // Remove read notifications from local state
                this.notifications = this.notifications.filter(n => !n.is_read);
            } catch (error) {
                console.error('Error deleting read notifications:', error);
            }
        },
        
        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 1) return 'щойно';
            if (diffMins < 60) return `${diffMins} хв тому`;
            if (diffHours < 24) return `${diffHours} год тому`;
            if (diffDays < 7) return `${diffDays} дн тому`;
            
            return date.toLocaleDateString('uk-UA', { day: 'numeric', month: 'short' });
        },
        
        startPolling() {
            // Poll for new notifications every 30 seconds
            this.pollingInterval = setInterval(() => {
                this.fetchUnreadCount();
            }, 30000);
        },
        
        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
            }
        }
    }
};
</script>

<style scoped>
.dropdown-enter-active, .dropdown-leave-active {
    transition: all 0.2s ease;
}

.dropdown-enter-from, .dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

