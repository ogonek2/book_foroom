<template>
    <div 
        @click="$emit('click', notification)"
        :class="[
            'notification-item bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 cursor-pointer transition-all duration-200 hover:shadow-md',
            !notification.is_read ? 'unread-notification' : ''
        ]"
    >
        <div class="flex items-start space-x-4">
            <!-- Icon -->
            <div class="flex-shrink-0">
                <div v-if="notification.type === 'review_reply'" class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div v-else-if="notification.type === 'discussion_reply'" class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div v-else class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <!-- Sender Avatar -->
                        <div v-if="notification.sender" class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-white">{{ notification.sender.name.charAt(0) }}</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ notification.message }}
                        </p>
                    </div>
                    
                    <!-- Time -->
                    <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 ml-2">
                        {{ formatTime(notification.created_at) }}
                    </span>
                </div>
                
                <!-- Preview Content -->
                <p v-if="notification.data && notification.data.reply_content" class="text-sm text-gray-600 dark:text-gray-400 mt-2 line-clamp-2 bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3">
                    "{{ notification.data.reply_content }}"
                </p>
                
                <!-- Book/Discussion Title -->
                <div v-if="notification.data" class="mt-2 flex items-center text-xs text-gray-500 dark:text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path v-if="notification.type === 'review_reply'" d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        <path v-else fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                    </svg>
                    <span v-if="notification.data.book_title">{{ notification.data.book_title }}</span>
                    <span v-else-if="notification.data.discussion_title">{{ notification.data.discussion_title }}</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex-shrink-0 flex items-center space-x-2">
                <!-- Unread indicator -->
                <div v-if="!notification.is_read" class="w-3 h-3 bg-purple-500 rounded-full"></div>
                
                <!-- Delete Button -->
                <button @click.stop="$emit('delete', notification.id)" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'NotificationCard',
    props: {
        notification: {
            type: Object,
            required: true
        }
    },
    emits: ['click', 'delete'],
    methods: {
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
            
            return date.toLocaleDateString('uk-UA', { day: 'numeric', month: 'short', year: 'numeric' });
        }
    }
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
