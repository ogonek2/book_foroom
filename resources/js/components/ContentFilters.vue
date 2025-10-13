<template>
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-light-text-primary dark:text-dark-text-primary mb-6">
            Фільтр
        </h3>

        <!-- Filter Buttons -->
        <div class="space-y-3 mb-8">
            <button v-for="filter in filters" :key="filter.value"
                    @click="setFilter(filter.value)"
                    :class="[
                        'w-full px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                        activeFilter === filter.value
                            ? 'bg-brand-500 text-white'
                            : 'bg-gray-100 dark:bg-gray-700 text-light-text-primary dark:text-dark-text-primary hover:bg-gray-200 dark:hover:bg-gray-600'
                    ]">
                {{ filter.label }}
            </button>
        </div>

        <!-- Sorting -->
        <div class="mb-8">
            <label class="block text-sm font-medium text-light-text-secondary dark:text-dark-text-secondary mb-3">
                Сортування
            </label>
            <select v-model="localSortBy" 
                    @change="updateSorting"
                    class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-light-text-primary dark:text-dark-text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                <option value="newest">Найновіші</option>
                <option value="oldest">Найстаріші</option>
                <option value="popular">Популярні</option>
                <option value="trending">Трендові</option>
            </select>
        </div>

        <!-- Apply Filters Button -->
        <button @click="applyFilters"
                class="w-full bg-gradient-to-r from-brand-500 to-accent-500 text-white px-4 py-3 rounded-lg font-medium hover:from-brand-600 hover:to-accent-600 transition-all duration-300">
            Застосувати фільтри
        </button>

        <!-- Reset Filters -->
        <button @click="resetFilters"
                class="w-full mt-3 bg-gray-100 dark:bg-gray-700 text-light-text-primary dark:text-dark-text-primary px-4 py-2 rounded-lg font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            Скинути
        </button>
    </div>
</template>

<script>
export default {
    name: 'ContentFilters',
    props: {
        activeFilter: {
            type: String,
            default: 'all'
        },
        sortBy: {
            type: String,
            default: 'newest'
        }
    },
    data() {
        return {
            localSortBy: this.sortBy,
            filters: [
                { value: 'all', label: 'Усі' },
                { value: 'discussions', label: 'Обговорення' },
                { value: 'reviews', label: 'Рецензії' }
            ]
        };
    },
    watch: {
        sortBy(newVal) {
            this.localSortBy = newVal;
        }
    },
    methods: {
        setFilter(filterValue) {
            this.$emit('filter-changed', filterValue);
            this.updateUrl();
        },

        updateSorting() {
            this.$emit('sort-changed', this.localSortBy);
            this.updateUrl();
        },

        applyFilters() {
            this.$emit('filters-applied', {
                filter: this.activeFilter,
                sort: this.localSortBy
            });
        },

        resetFilters() {
            this.localSortBy = 'newest';
            this.$emit('filter-changed', 'all');
            this.$emit('sort-changed', 'newest');
            this.updateUrl();
        },

        updateUrl() {
            const url = new URL(window.location);
            url.searchParams.set('filter', this.activeFilter);
            url.searchParams.set('sort', this.localSortBy);
            
            // Обновляем URL без перезагрузки страницы
            window.history.pushState({}, '', url);
        },

        // Инициализация из URL
        initFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            const filter = urlParams.get('filter') || 'all';
            const sort = urlParams.get('sort') || 'newest';

            if (filter !== this.activeFilter) {
                this.$emit('filter-changed', filter);
            }
            
            if (sort !== this.localSortBy) {
                this.localSortBy = sort;
                this.$emit('sort-changed', sort);
            }
        }
    },
    mounted() {
        this.initFromUrl();
    }
};
</script>
