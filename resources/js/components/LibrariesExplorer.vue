<template>
    <div class="min-h-screen from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
        <div class="mx-auto py-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-4xl font-black text-slate-900 dark:text-white mb-2">Добірки</h1>
                    <p class="text-lg text-slate-600 dark:text-slate-400">Знайдіть цікаві добірки книг або створіть власну</p>
                </div>
                <button
                    v-if="isAuthenticated"
                    @click="goToCreate"
                    class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-3 rounded-xl font-bold hover:from-orange-600 hover:to-red-600 transition-all duration-300 transform hover:scale-105 shadow-lg"
                >
                    <span class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span>Додати</span>
                    </span>
                </button>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar -->
                <div class="lg:w-1/4">
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/30 p-6">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Пошук</label>
                            <input
                                type="text"
                                v-model="filters.search"
                                @input="handleSearchInput"
                                class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white transition-all duration-200"
                                placeholder="Пошук добірок..."
                            >
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Категорії</label>
                            <select
                                v-model="filters.category"
                                @change="applyFilters"
                                class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
                            >
                                <option value="">Усі</option>
                                <option value="fiction">Художня література</option>
                                <option value="non-fiction">Нехудожня література</option>
                                <option value="science">Наукова література</option>
                                <option value="history">Історична література</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Сортування</label>
                            <select
                                v-model="filters.sort"
                                @change="applyFilters"
                                class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white"
                            >
                                <option value="popular">Популярні</option>
                                <option value="newest">Найновіші</option>
                                <option value="oldest">Найстаріші</option>
                                <option value="name">За назвою</option>
                                <option value="books_count">За кількістю книг</option>
                            </select>
                        </div>

                        <button
                            @click="resetFilters"
                            class="w-full bg-gradient-to-r from-pink-500 to-purple-500 text-white py-3 px-6 rounded-xl font-bold hover:from-pink-600 hover:to-purple-600 transition-all duration-300 transform hover:scale-105"
                        >
                            Скинути фільтри
                        </button>
                    </div>
                </div>

                <!-- Main content -->
                <div class="lg:w-3/4">
                    <div v-if="loading" class="py-16 flex items-center justify-center text-slate-500 dark:text-slate-400">
                        <svg class="animate-spin h-8 w-8 mr-3 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Завантаження добірок...
                    </div>

                    <template v-else>
                        <div v-if="hasLibraries" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <library-collection
                                v-for="library in libraries"
                                :key="library.id"
                                :library="library"
                                :is-authenticated="isAuthenticated"
                                :is-liked="library.is_liked"
                                :is-saved="library.is_saved"
                                :likes-count="library.likes_count"
                                @notification="showNotification"
                                @liked="handleLiked"
                                @saved="handleSaved"
                            ></library-collection>
                        </div>
                        <div v-else class="text-center py-12">
                            <div class="text-slate-400 dark:text-slate-500 text-6xl mb-4">
                                <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Добірки не знайдено</h3>
                            <p class="text-slate-600 dark:text-slate-400 mb-6">Спробуйте змінити фільтри або створити першу добірку</p>
                            <button
                                v-if="isAuthenticated"
                                @click="goToCreate"
                                class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-3 rounded-xl font-bold hover:from-orange-600 hover:to-red-600 transition-all duration-300"
                            >
                                Створити добірку
                            </button>
                        </div>
                    </template>

                    <div
                        v-if="!loading && hasLibraries && meta && meta.last_page > 1"
                        class="mt-8 flex items-center justify-center space-x-2"
                    >
                        <button
                            class="px-3 py-2 text-sm rounded-lg bg-light-bg-secondary dark:bg-dark-bg-secondary text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary disabled:opacity-50"
                            :disabled="meta.current_page <= 1"
                            @click="goToPage(meta.current_page - 1)"
                        >
                            Попередня
                        </button>
                        <button
                            v-for="page in pages"
                            :key="page"
                            class="px-3 py-2 text-sm rounded-lg"
                            :class="page === meta.current_page ? 'bg-brand-500 text-white' : 'bg-light-bg-secondary dark:bg-dark-bg-secondary text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary'"
                            @click="goToPage(page)"
                        >
                            {{ page }}
                        </button>
                        <button
                            class="px-3 py-2 text-sm rounded-lg bg-light-bg-secondary dark:bg-dark-bg-secondary text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary disabled:opacity-50"
                            :disabled="meta.current_page >= meta.last_page"
                            @click="goToPage(meta.current_page + 1)"
                        >
                            Наступна
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import LibraryCollection from './LibraryCollection.vue';

export default {
    name: 'LibrariesExplorer',
    components: { LibraryCollection },
    props: {
        initialLibraries: {
            type: Array,
            default: () => [],
        },
        initialMeta: {
            type: Object,
            default: () => ({ current_page: 1, last_page: 1, per_page: 12, total: 0 }),
        },
        initialFilters: {
            type: Object,
            default: () => ({}),
        },
        initialLinks: {
            type: Object,
            default: () => ({ prev: null, next: null }),
        },
        isAuthenticated: {
            type: Boolean,
            default: false,
        },
        createUrl: {
            type: String,
            default: '/libraries/create',
        },
    },
    data() {
        return {
            libraries: this.initialLibraries || [],
            meta: Object.assign({ current_page: 1, last_page: 1, per_page: 12, total: 0 }, this.initialMeta || {}),
            links: Object.assign({ prev: null, next: null }, this.initialLinks || {}),
            filters: {
                search: this.initialFilters.search || '',
                category: this.initialFilters.category || '',
                sort: this.initialFilters.sort || 'popular',
            },
            loading: false,
            error: null,
            searchTimer: null,
        };
    },
    computed: {
        hasLibraries() {
            return Array.isArray(this.libraries) && this.libraries.length > 0;
        },
        pages() {
            if (!this.meta || !this.meta.last_page) {
                return [];
            }
            const total = this.meta.last_page;
            const current = this.meta.current_page || 1;
            const delta = 2;
            let start = Math.max(1, current - delta);
            let end = Math.min(total, current + delta);

            while (end - start < 4 && start > 1) {
                start--;
            }
            while (end - start < 4 && end < total) {
                end++;
            }

            const list = [];
            for (let i = start; i <= end; i += 1) {
                list.push(i);
            }
            return list;
        },
    },
    methods: {
        async fetchLibraries(page = 1) {
            this.loading = true;
            this.error = null;
            try {
                const response = await axios.get('/libraries', {
                    params: {
                        search: this.filters.search || undefined,
                        category: this.filters.category || undefined,
                        sort: this.filters.sort || undefined,
                        page,
                    },
                    headers: {
                        Accept: 'application/json',
                    },
                });
                const data = response.data || {};
                this.libraries = data.data || [];
                this.meta = Object.assign({ current_page: page, last_page: 1, per_page: 12, total: 0 }, data.meta || {});
                this.links = Object.assign({ prev: null, next: null }, data.links || {});
                this.updateUrl();
            } catch (error) {
                console.error('Failed to fetch libraries', error);
                this.error = 'Не вдалося завантажити добірки.';
                this.showNotification(this.error, 'error');
            } finally {
                this.loading = false;
            }
        },
        handleSearchInput() {
            clearTimeout(this.searchTimer);
            this.searchTimer = setTimeout(() => {
                this.fetchLibraries(1);
            }, 400);
        },
        applyFilters() {
            clearTimeout(this.searchTimer);
            this.fetchLibraries(1);
        },
        resetFilters() {
            clearTimeout(this.searchTimer);
            this.filters.search = '';
            this.filters.category = '';
            this.filters.sort = 'popular';
            this.fetchLibraries(1);
        },
        goToPage(page) {
            if (!this.meta) {
                return;
            }
            const total = this.meta.last_page || 1;
            if (page < 1 || page > total) {
                return;
            }
            this.fetchLibraries(page);
        },
        updateUrl() {
            if (typeof window === 'undefined' || !window.history) {
                return;
            }
            const params = new URLSearchParams();
            if (this.filters.search) {
                params.set('search', this.filters.search);
            }
            if (this.filters.category) {
                params.set('category', this.filters.category);
            }
            if (this.filters.sort && this.filters.sort !== 'popular') {
                params.set('sort', this.filters.sort);
            }
            if (this.meta && this.meta.current_page && this.meta.current_page > 1) {
                params.set('page', this.meta.current_page);
            }
            const query = params.toString();
            const url = query ? `${window.location.pathname}?${query}` : window.location.pathname;
            window.history.replaceState({}, '', url);
        },
        goToCreate() {
            window.location.href = this.createUrl;
        },
        handleLiked({ libraryId, isLiked, likesCount }) {
            const library = this.libraries.find((item) => item.id === libraryId);
            if (library) {
                this.$set(library, 'is_liked', isLiked);
                this.$set(library, 'likes_count', likesCount);
            }
        },
        handleSaved({ libraryId, isSaved }) {
            const library = this.libraries.find((item) => item.id === libraryId);
            if (library) {
                this.$set(library, 'is_saved', isSaved);
            }
        },
        showNotification(message, type = 'success') {
            const existing = document.querySelectorAll('.notification');
            existing.forEach((node) => node.remove());

            const notification = document.createElement('div');
            notification.className = `notification fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);

            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        },
    },
    beforeDestroy() {
        clearTimeout(this.searchTimer);
    },
    mounted() {
        this.updateUrl();
    },
};
</script>
