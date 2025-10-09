<template>
    <div class="min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Сповіщення</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">Відстежуйте всі оновлення та активність</p>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <button v-if="unreadCount > 0" @click="markAllAsRead" class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Позначити всі
                        </button>
                        <button @click="deleteAllRead" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Очистити прочитані
                        </button>
                    </div>
                </div>
                
                <!-- Filters -->
                <notification-filters 
                    :current-filter="currentFilter"
                    :total-count="totalCount"
                    :unread-count="unreadCount"
                    @filter-change="onFilterChange"
                />
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center py-20">
                <svg class="animate-spin h-12 w-12 text-purple-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Notifications List -->
            <div v-else-if="notifications.length > 0" class="space-y-3">
                <notification-card
                    v-for="notification in notifications"
                    :key="notification.id"
                    :notification="notification"
                    @click="handleNotificationClick"
                    @delete="deleteNotification"
                />
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <div class="mb-6">
                    <svg class="w-24 h-24 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Немає сповіщень</h3>
                <p class="text-gray-500 dark:text-gray-400">Ваші сповіщення з'являться тут</p>
            </div>

            <!-- Pagination -->
            <notification-pagination
                v-if="lastPage > 1"
                :current-page="currentPage"
                :last-page="lastPage"
                @page-change="loadPage"
            />
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import NotificationFilters from './NotificationFilters.vue';
import NotificationCard from './NotificationCard.vue';
import NotificationPagination from './NotificationPagination.vue';

export default {
    name: 'NotificationsPage',
    components: {
        NotificationFilters,
        NotificationCard,
        NotificationPagination
    },
    data() {
        return {
            notifications: [],
            unreadCount: 0,
            totalCount: 0,
            loading: false,
            currentFilter: 'all',
            currentPage: 1,
            lastPage: 1
        };
    },
    mounted() {
        this.fetchNotifications();
    },
    methods: {
        async fetchNotifications(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: page,
                    filter: this.currentFilter
                });
                
                const response = await axios.get(`/notifications/api?${params}`);
                
                this.notifications = response.data.notifications;
                this.unreadCount = response.data.unread_count;
                this.totalCount = response.data.total;
                this.currentPage = response.data.current_page;
                this.lastPage = response.data.last_page;
                
            } catch (error) {
                console.error('Error fetching notifications:', error);
                this.showNotification('Помилка завантаження сповіщень', 'error');
            } finally {
                this.loading = false;
            }
        },
        
        onFilterChange(filter) {
            this.currentFilter = filter;
            this.currentPage = 1;
            this.fetchNotifications();
        },
        
        loadPage(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.fetchNotifications(page);
                window.scrollTo({ top: 0, behavior: 'smooth' });
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
                return `/books/${notification.data.book_id}#review-${notification.data.review_id}`;
            } else if (notification.type === 'discussion_reply' && notification.data) {
                return `/discussions/${notification.data.discussion_slug}#reply-${notification.data.reply_id}`;
            }
            return null;
        },
        
        async markAsRead(id) {
            try {
                await axios.post(`/notifications/${id}/read`);
                
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
                
                this.notifications.forEach(n => n.is_read = true);
                this.unreadCount = 0;
                this.showNotification('Всі сповіщення позначено як прочитані', 'success');
            } catch (error) {
                console.error('Error marking all as read:', error);
                this.showNotification('Помилка при позначенні', 'error');
            }
        },
        
        async deleteNotification(id) {
            try {
                await axios.delete(`/notifications/${id}`);
                
                const index = this.notifications.findIndex(n => n.id === id);
                if (index !== -1) {
                    if (!this.notifications[index].is_read) {
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                    this.notifications.splice(index, 1);
                }
                this.showNotification('Сповіщення видалено', 'success');
            } catch (error) {
                console.error('Error deleting notification:', error);
                this.showNotification('Помилка видалення', 'error');
            }
        },
        
        async deleteAllRead() {
            if (!confirm('Ви впевнені, що хочете видалити всі прочитані сповіщення?')) {
                return;
            }
            
            try {
                await axios.delete('/notifications/read/all');
                
                this.notifications = this.notifications.filter(n => !n.is_read);
                this.showNotification('Прочитані сповіщення видалено', 'success');
            } catch (error) {
                console.error('Error deleting read notifications:', error);
                this.showNotification('Помилка видалення', 'error');
            }
        },
        
        showNotification(message, type = 'success') {
            this.$emit('notification', { message, type });
        }
    }
};
</script>

<style scoped>
.notification-item {
    transition: all 0.2s ease;
}
.notification-item:hover {
    transform: translateY(-1px);
}
.unread-notification {
    border-left: 4px solid #8b5cf6;
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.05) 0%, rgba(236, 72, 153, 0.05) 100%);
}
</style>
