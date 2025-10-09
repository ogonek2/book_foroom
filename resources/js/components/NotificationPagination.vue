<template>
    <div class="mt-8 flex justify-center">
        <nav class="flex items-center space-x-2">
            <button 
                @click="$emit('page-change', currentPage - 1)" 
                :disabled="currentPage === 1"
                class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                Попередня
            </button>
            
            <span class="px-4 py-2 text-gray-600 dark:text-gray-400">
                Сторінка {{ currentPage }} з {{ lastPage }}
            </span>
            
            <button 
                @click="$emit('page-change', currentPage + 1)" 
                :disabled="currentPage === lastPage"
                class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                Наступна
            </button>
        </nav>
    </div>
</template>

<script>
export default {
    name: 'NotificationPagination',
    props: {
        currentPage: {
            type: Number,
            required: true
        },
        lastPage: {
            type: Number,
            required: true
        }
    },
    emits: ['page-change']
};
</script>
