<template>
    <nav v-if="hasPages" class="flex items-center justify-center space-x-2" aria-label="Пагинация">
        <!-- Previous Button -->
        <button
            @click="goToPage(currentPage - 1)"
            :disabled="isFirstPage"
            :class="[
                'flex items-center justify-center w-10 h-10 rounded-xl font-semibold transition-all duration-200',
                isFirstPage
                    ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-600 cursor-not-allowed'
                    : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-500 hover:to-purple-600 hover:text-white dark:hover:text-white border border-gray-200 dark:border-gray-700 hover:border-transparent shadow-sm hover:shadow-md'
            ]"
            aria-label="Попередня сторінка"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <!-- Page Numbers -->
        <template v-for="(page, index) in displayPages" :key="index">
            <!-- Three Dots Separator -->
            <span
                v-if="page === '...'"
                class="flex items-center justify-center w-10 h-10 text-gray-400 dark:text-gray-600 font-semibold"
            >
                {{ page }}
            </span>

            <!-- Page Number -->
            <button
                v-else
                @click="goToPage(page)"
                :class="[
                    'flex items-center justify-center min-w-[2.5rem] h-10 px-3 rounded-xl font-semibold transition-all duration-200',
                    page === currentPage
                        ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-md'
                        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md'
                ]"
                :aria-current="page === currentPage ? 'page' : null"
                :aria-label="`Перейти на сторінку ${page}`"
            >
                {{ page }}
            </button>
        </template>

        <!-- Next Button -->
        <button
            @click="goToPage(currentPage + 1)"
            :disabled="isLastPage"
            :class="[
                'flex items-center justify-center w-10 h-10 rounded-xl font-semibold transition-all duration-200',
                isLastPage
                    ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-600 cursor-not-allowed'
                    : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-500 hover:to-purple-600 hover:text-white dark:hover:text-white border border-gray-200 dark:border-gray-700 hover:border-transparent shadow-sm hover:shadow-md'
            ]"
            aria-label="Наступна сторінка"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </nav>
</template>

<script>
export default {
    name: 'Pagination',
    props: {
        currentPage: {
            type: Number,
            required: true
        },
        lastPage: {
            type: Number,
            required: true
        },
        // Для стандартной Laravel пагинации через Blade
        elements: {
            type: Array,
            default: null
        },
        // Для AJAX пагинации
        onEachSide: {
            type: Number,
            default: 1
        }
    },
    computed: {
        hasPages() {
            return this.lastPage > 1;
        },
        isFirstPage() {
            return this.currentPage === 1;
        },
        isLastPage() {
            return this.currentPage === this.lastPage;
        },
        displayPages() {
            // Если есть элементы от Laravel (Blade шаблон), используем их
            if (this.elements && this.elements.length > 0) {
                const pages = [];
                this.elements.forEach(element => {
                    if (typeof element === 'string') {
                        pages.push('...');
                    } else if (Array.isArray(element)) {
                        Object.keys(element).forEach(page => {
                            pages.push(Number(page));
                        });
                    }
                });
                return pages;
            }
            
            // Иначе формируем страницы сами (для AJAX пагинации)
            return this.generatePages();
        }
    },
    methods: {
        generatePages() {
            const pages = [];
            const window = this.onEachSide || 1;
            
            if (this.lastPage <= 7) {
                // Если страниц 7 или меньше, показываем все
                for (let i = 1; i <= this.lastPage; i++) {
                    pages.push(i);
                }
            } else {
                // Всегда показываем первую страницу
                pages.push(1);
                
                // Показываем точки, если текущая страница далеко от начала
                if (this.currentPage > window + 3) {
                    pages.push('...');
                }
                
                // Показываем страницы вокруг текущей
                const start = Math.max(2, this.currentPage - window);
                const end = Math.min(this.lastPage - 1, this.currentPage + window);
                
                for (let i = start; i <= end; i++) {
                    if (i !== 1 && i !== this.lastPage) {
                        pages.push(i);
                    }
                }
                
                // Показываем точки, если текущая страница далеко от конца
                if (this.currentPage < this.lastPage - window - 2) {
                    pages.push('...');
                }
                
                // Всегда показываем последнюю страницу
                if (this.lastPage > 1 && !pages.includes(this.lastPage)) {
                    pages.push(this.lastPage);
                }
            }
            
            return pages;
        },
        goToPage(page) {
            if (page < 1 || page > this.lastPage || page === this.currentPage || typeof page === 'string') {
                return;
            }
            
            // Эмитируем событие для родительского компонента (для AJAX)
            this.$emit('page-changed', page);
            
            // Если есть элементы с URL (Blade шаблон), используем их
            if (this.elements && this.elements.length > 0) {
                const element = this.elements.find(el => Array.isArray(el) && el[page]);
                if (element && element[page]) {
                    window.location.href = element[page];
                    return;
                }
            }
        }
    }
}
</script>

<style scoped>
/* Дополнительные стили при необходимости */
</style>
