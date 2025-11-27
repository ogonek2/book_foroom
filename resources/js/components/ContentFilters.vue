<template>
    <div :class="mobile ? '' : ''">
        <h3 v-if="!mobile" class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
            <i class="fas fa-filter text-purple-500"></i>
            Фільтри
        </h3>

        <!-- Filter Toggle Switch -->
        <div :class="mobile ? 'space-y-4 mb-6' : 'space-y-5 mb-8'">
            <label v-if="mobile" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3 uppercase tracking-wide">
                Тип контенту
            </label>
            <label v-else class="block text-sm font-semibold text-slate-600 dark:text-slate-400 mb-3 uppercase tracking-wide">
                Тип контенту
            </label>
            
            <!-- Toggle Switch Container -->
            <div class="bg-slate-100 dark:bg-slate-800/60 rounded-2xl p-1.5 flex items-center gap-1.5">
                <button v-for="filter in filters" :key="filter.value"
                        @click="setFilter(filter.value)"
                        :class="[
                            'flex-1 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-300',
                            activeFilter === filter.value
                                ? 'bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-500 text-white shadow-lg shadow-purple-500/30'
                                : 'bg-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'
                        ]">
                    {{ filter.label }}
                </button>
            </div>
        </div>

        <!-- Sorting -->
        <div :class="mobile ? 'mb-6' : 'mb-8'">
            <label class="block text-sm font-semibold text-slate-600 dark:text-slate-400 mb-3 uppercase tracking-wide">
                Сортування
            </label>
            <div class="relative">
                <select v-model="localSortBy" 
                        @change="updateSorting"
                        class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none cursor-pointer transition-all duration-200 hover:bg-slate-200 dark:hover:bg-slate-700">
                    <option value="newest">Найновіші</option>
                    <option value="oldest">Найстаріші</option>
                    <option value="popular">Популярні</option>
                    <option value="trending">Трендові</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                    <i class="fas fa-chevron-down text-slate-500 dark:text-slate-400 text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Create Discussion Button (only for desktop) -->
        <a v-if="!mobile && createDiscussionUrl" :href="createDiscussionUrl"
           class="w-full bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-500 text-white px-4 py-3 rounded-xl font-semibold hover:from-violet-600 hover:via-purple-600 hover:to-fuchsia-600 transition-all duration-300 transform hover:scale-[1.02] shadow-lg shadow-purple-500/30 flex items-center justify-center gap-2 mb-3">
            <i class="fas fa-plus-circle"></i>
            Створити тему
        </a>

        <!-- Reset Filters -->
        <button v-if="!mobile" @click="resetFilters"
                class="w-full bg-slate-100 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 px-4 py-2.5 rounded-xl font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-all duration-200 flex items-center justify-center gap-2">
            <i class="fas fa-redo text-xs"></i>
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
        },
        mobile: {
            type: Boolean,
            default: false
        },
        createDiscussionUrl: {
            type: String,
            default: null
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
        },

        updateSorting() {
            this.$emit('sort-changed', this.localSortBy);
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

<style scoped>
/* Custom select arrow styling */
select {
    background-image: none;
}

select::-ms-expand {
    display: none;
}

/* Smooth transitions for toggle switch */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hover effects */
button:hover {
    transform: translateY(-1px);
}

button:active {
    transform: translateY(0);
}
</style>
