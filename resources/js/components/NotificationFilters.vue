<template>
    <div class="flex items-center space-x-4 text-sm">
        <button 
            @click="$emit('filter-change', 'all')"
            :class="[
                'hover:text-purple-600 dark:hover:text-purple-400 transition-colors',
                currentFilter === 'all' ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-600 dark:text-gray-400'
            ]"
        >
            Всі ({{ totalCount }})
        </button>
        <button 
            @click="$emit('filter-change', 'unread')"
            :class="[
                'hover:text-purple-600 dark:hover:text-purple-400 transition-colors',
                currentFilter === 'unread' ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-600 dark:text-gray-400'
            ]"
        >
            Непрочитані ({{ unreadCount }})
        </button>
        <button 
            @click="$emit('filter-change', 'replies')"
            :class="[
                'hover:text-purple-600 dark:hover:text-purple-400 transition-colors',
                currentFilter === 'replies' ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-600 dark:text-gray-400'
            ]"
        >
            Відповіді
        </button>
        <button 
            @click="$emit('filter-change', 'likes')"
            :class="[
                'hover:text-purple-600 dark:hover:text-purple-400 transition-colors',
                currentFilter === 'likes' ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-600 dark:text-gray-400'
            ]"
        >
            Лайки
        </button>
    </div>
</template>

<script>
export default {
    name: 'NotificationFilters',
    props: {
        currentFilter: {
            type: String,
            required: true
        },
        totalCount: {
            type: Number,
            required: true
        },
        unreadCount: {
            type: Number,
            required: true
        }
    },
    emits: ['filter-change']
};
</script>
