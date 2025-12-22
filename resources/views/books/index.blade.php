@extends('layouts.app')

@section('title', 'Каталог книг')

@section('main')
    @php
        $initialBooks = $books->getCollection()->map(function ($book) {
            return [
                'id' => $book->id,
                'slug' => $book->slug,
                'title' => $book->title,
                'book_name_ua' => $book->book_name_ua,
                'author' => $book->author,
                'cover_image' => $book->cover_image,
                'rating' => (float) $book->rating,
                'reviews_count' => (int) $book->reviews_count,
                'pages' => (int) $book->pages,
                'publication_year' => $book->publication_year,
            ];
        })->values();

        $initialPagination = [
            'current_page' => $books->currentPage(),
            'last_page' => $books->lastPage(),
            'per_page' => $books->perPage(),
            'total' => $books->total(),
        ];

        $initialCategories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'books_count' => $category->books_count ?? 0,
            ];
        })->values();

        $initialUserLibraries = $userLibraries->map(function ($library) {
            return [
                'id' => $library->id,
                'name' => $library->name,
                'is_private' => (bool) ($library->is_private ?? false),
            ];
        })->values();
    @endphp

    <div id="books-browser" class="max-w-7xl mx-auto" v-cloak>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Каталог книг</h1>
                    <p class="text-gray-600 dark:text-gray-400">Знайдіть цікаві книги для читання</p>
                </div>
            </div>
            <!-- Mobile Filter Button -->
            <button @click="showMobileFilters = true"
                class="lg:hidden flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold transition-colors shadow-lg mt-4">
                <i class="fa-solid fa-bars-staggered animate-pulse"></i>
                <span>Фільтри</span>
            </button>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar (Desktop) -->
            <div class="hidden lg:block lg:col-span-1">
                <div class="space-y-6">
                    <!-- Filters -->
                    <div
                        class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center justify-between">
                            Фільтри
                            <button @click="resetFilters"
                                class="text-xs font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 transition-colors">
                                Скинути
                            </button>
                        </h3>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Пошук</label>
                                <input type="text" v-model.trim="filters.search" @input="fetchBooksDebounced"
                                    placeholder="Назва, автор, опис..."
                                    class="w-full px-4 py-3 border-0 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-gray-600 transition-all duration-200">
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Рейтинг</label>
                                <double-range-slider
                                    :min="1"
                                    :max="10"
                                    v-model="filters.rating_range"
                                    @change="fetchBooks"
                                ></double-range-slider>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Сортування</label>
                                <select v-model="filters.sort" @change="fetchBooks"
                                    class="w-full px-4 py-3 border-0 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-gray-600 transition-all duration-200">
                                    <option value="rating">За рейтингом</option>
                                    <option value="title">За назвою</option>
                                    <option value="author">За автором</option>
                                    <option value="year">За роком</option>
                                    <option value="reviews">За кількістю рецензій</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div
                        class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Жанри</h3>
                        <div class="space-y-2 max-h-[420px] overflow-y-auto custom-scroll">
                            <button
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200"
                                :class="filters.category === '' ? 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-200' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/70'"
                                @click="selectCategory('')">
                                <span>Всі категорії</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">@{{ totalBooksCount }}</span>
                            </button>
                            <button v-for="category in categories" :key="category.slug"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200"
                                :class="filters.category === category.slug ? 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-200' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/70'"
                                @click="selectCategory(category.slug)">
                                <span class="text-left">@{{ category.name }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">@{{ category.books_count }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Books Grid -->
            <div class="lg:col-span-3">
                <transition name="fade" mode="out-in">
                    <div v-if="loading" key="loading"
                        class="flex items-center justify-center py-24 bg-white/60 dark:bg-gray-800/60 rounded-2xl border border-gray-200/30 dark:border-gray-700/30">
                        <div class="flex flex-col items-center space-y-4">
                            <svg class="animate-spin h-10 w-10 text-purple-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Завантажуємо книжки...</p>
                        </div>
                    </div>
                    <div v-else-if="books.length > 0" key="results">
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">
                            <book-card v-for="book in books" :key="book.id" :book="book" :user-libraries="userLibraries"
                                :is-authenticated="isAuthenticated" @notification="handleNotification" />
                        </div>

                    </div>
                    <div v-else key="empty"
                        class="text-center py-12 bg-white/60 dark:bg-gray-800/60 rounded-2xl border border-gray-200/30 dark:border-gray-700/30">
                        <div
                            class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Книги не знайдені</h3>
                        <p class="text-gray-500 dark:text-gray-400">Спробуйте змінити параметри пошуку або рейтинг</p>
                    </div>
                </transition>
                
                <!-- Pagination for initial load -->
                @if($books->hasPages())
                    <div class="mt-8">
                        {{ $books->appends(request()->query())->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Mobile Filters Sidebar -->
        <transition name="fade">
            <div v-if="showMobileFilters" class="lg:hidden fixed inset-0 z-50 overflow-hidden"
                @click.self="showMobileFilters = false">
                <!-- Overlay -->
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showMobileFilters = false"></div>

                <!-- Sidebar -->
                <transition name="slide-left">
                    <div v-if="showMobileFilters"
                        class="absolute right-0 top-0 h-full w-full max-w-sm bg-white dark:bg-gray-800 shadow-2xl overflow-y-auto">
                        <!-- Header -->
                        <div
                            class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between z-10">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Фільтри</h2>
                            <button @click="showMobileFilters = false"
                                class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Filters Content -->
                        <div class="p-6 space-y-6">
                            <!-- Filters -->
                            <div class="space-y-5">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Фільтри</h3>
                                    <button @click="resetFilters"
                                        class="text-xs font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 transition-colors">
                                        Скинути
                                    </button>
                                </div>

                                <div class="space-y-5">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Пошук</label>
                                        <input type="text" v-model.trim="filters.search" @input="fetchBooksDebounced"
                                            placeholder="Назва, автор, опис..."
                                            class="w-full px-4 py-3 border-0 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-gray-600 transition-all duration-200">
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Рейтинг</label>
                                        <double-range-slider
                                            :min="1"
                                            :max="10"
                                            v-model="filters.rating_range"
                                            @change="fetchBooks"
                                        ></double-range-slider>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Сортування</label>
                                        <select v-model="filters.sort" @change="fetchBooks"
                                            class="w-full px-4 py-3 border-0 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-gray-600 transition-all duration-200">
                                            <option value="rating">За рейтингом</option>
                                            <option value="title">За назвою</option>
                                            <option value="author">За автором</option>
                                            <option value="year">За роком</option>
                                            <option value="reviews">За кількістю рецензій</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Categories -->
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Жанри</h3>
                                <div class="space-y-2 max-h-[420px] overflow-y-auto custom-scroll">
                                    <button
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200"
                                        :class="filters.category === '' ? 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-200' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/70'"
                                        @click="selectCategory('')">
                                        <span>Всі категорії</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">@{{ totalBooksCount }}</span>
                                    </button>
                                    <button v-for="category in categories" :key="category.slug"
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-sm font-medium transition-all duration-200"
                                        :class="filters.category === category.slug ? 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-200' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/70'"
                                        @click="selectCategory(category.slug)">
                                        <span class="text-left">@{{ category.name }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">@{{ category.books_count
                                            }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Apply Button -->
                            <div
                                class="sticky bottom-0 pt-4 pb-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 -mx-6 px-6">
                                <button @click="showMobileFilters = false"
                                    class="w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold transition-colors shadow-lg">
                                    Застосувати фільтри
                                </button>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </div>


    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const bootBooksBrowser = () => {
                    const booksBrowser = new Vue({
                        el: '#books-browser',
                        computed: {
                            totalBooksCount() {
                                return this.categories.reduce((total, category) => total + (Number(category.books_count) || 0), 0);
                            }
                        },
                        data: {
                            books: @js($initialBooks),
                            pagination: @js($initialPagination),
                            userLibraries: @js($initialUserLibraries),
                            categories: @js($initialCategories),
                            filters: {
                                search: @json(request('search', '')),
                                category: @json(request('category', '')),
                                sort: @json(request('sort', 'rating')),
                                rating_range: [
                                    Number(@json(request('rating_min', 1))),
                                    Number(@json(request('rating_max', 10)))
                                ],
                            },
                            loading: false,
                            error: null,
                            debounceTimer: null,
                            isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
                            showMobileFilters: false,
                                    @auth
                                            user: {
                                            username: '{{ auth()->user()->username }}'
                                        }
                                    @else
                                        user: null
                                    @endauth
                                },
                    methods: {
                        fetchBooks(page = 1) {
                            const axiosInstance = window.axios || (window.Vue && window.Vue.prototype && window.Vue.prototype.$http);
                if (!axiosInstance) {
                    console.error('Axios is not available globally.');
                    this.error = 'Не вдалося підключити axios. Оновіть сторінку.';
                    this.loading = false;
                    return;
                }

                this.loading = true;
                this.error = null;

                const params = {
                    search: this.filters.search || undefined,
                    category: this.filters.category || undefined,
                    sort: this.filters.sort || 'rating',
                    rating_min: this.filters.rating_range[0],
                    rating_max: this.filters.rating_range[1],
                    page,
                };

                axiosInstance.get('{{ route('books.index') }}', {
                    params,
                    headers: {
                        'Accept': 'application/json'
                    },
                })
                    .then((response) => {
                        this.books = response.data.data || [];
                        this.pagination = response.data.meta || {
                            current_page: 1,
                            last_page: 1,
                            per_page: this.books.length,
                            total: this.books.length,
                        };
                    })
                    .catch((error) => {
                        console.error('Failed to fetch books:', error);
                        this.error = 'Не вдалося завантажити книги. Спробуйте ще раз пізніше.';
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
                fetchBooksDebounced() {
                clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.fetchBooks();
            }, 300);
                                    },
            selectCategory(slug) {
                this.filters.category = slug;
                this.fetchBooks();
            },
            resetFilters() {
                this.filters.search = '';
                this.filters.category = '';
                this.filters.sort = 'rating';
                this.filters.rating_range = [1, 10];
                this.fetchBooks();
            },
            changePage(page) {
                if (page < 1 || page > this.pagination.last_page || this.loading) {
                    return;
                }
                this.fetchBooks(page);
            },
            handleNotification(notification) {
                this.showNotification(notification.message, notification.type);
            },
            showNotification(message, type = 'success') {
                const existingNotifications = document.querySelectorAll('.notification');
                existingNotifications.forEach(notification => notification.remove());

                const notification = document.createElement('div');
                notification.className = `notification fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                    }`;
                notification.textContent = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                }, 100);

                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 3000);
            }
                                }
                            });
                        };

            if (!window.axios) {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/axios@1/dist/axios.min.js';
                script.onload = bootBooksBrowser;
                script.onerror = () => console.error('Failed to load axios from CDN.');
                document.head.appendChild(script);
            } else {
                bootBooksBrowser();
            }
                    });
        </script>
    @endpush

    @push('styles')
        <style>
            [v-cloak] {
                display: none;
            }

            .custom-scroll {
                overflow-x: hidden;
                padding-right: 0.25rem;
                scrollbar-width: thin;
                scrollbar-color: rgba(148, 163, 184, 0.45) transparent;
            }

            .custom-scroll::-webkit-scrollbar {
                width: 6px;
            }

            .custom-scroll::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scroll::-webkit-scrollbar-thumb {
                background-color: rgba(148, 163, 184, 0.45);
                border-radius: 12px;
            }

            .fade-enter-active,
            .fade-leave-active {
                transition: opacity 0.2s ease;
            }

            .fade-enter,
            .fade-leave-to {
                opacity: 0;
            }

            /* Mobile Filters Sidebar Animations */
            .slide-left-enter-active {
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .slide-left-leave-active {
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .slide-left-enter {
                transform: translateX(100%);
            }

            .slide-left-enter-to {
                transform: translateX(0);
            }

            .slide-left-leave {
                transform: translateX(0);
            }

            .slide-left-leave-to {
                transform: translateX(100%);
            }
        </style>
    @endpush
@endsection