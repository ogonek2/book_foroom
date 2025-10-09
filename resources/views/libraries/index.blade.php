@extends('layouts.app')

@section('title', 'Добірки - Книжковий форум')

@section('main')
    <div id="app" class="min-h-screen from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
        <div class="mx-auto py-8">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-4xl font-black text-slate-900 dark:text-white mb-2">Добірки</h1>
                    <p class="text-lg text-slate-600 dark:text-slate-400">Знайдіть цікаві добірки книг або створіть власну</p>
                </div>
                @auth
                    <button @click="openCreateModal" 
                            class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-3 rounded-xl font-bold hover:from-orange-600 hover:to-red-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>Додати</span>
                        </div>
                    </button>
                @endauth
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar -->
                <div class="lg:w-1/4">
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/30 p-6">
                        <!-- Search -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Пошук</label>
                            <input type="text" v-model="searchQuery" @input="applyFilters"
                                   class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white transition-all duration-200"
                                   placeholder="Пошук добірок...">
                        </div>

                        <!-- Categories -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Категорії</label>
                            <select v-model="selectedCategory" @change="applyFilters"
                                    class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                                <option value="">Усі</option>
                                <option value="fiction">Художня література</option>
                                <option value="non-fiction">Нехудожня література</option>
                                <option value="science">Наукова література</option>
                                <option value="history">Історична література</option>
                            </select>
                        </div>

                        <!-- Sort -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Сортування</label>
                            <select v-model="selectedSort" @change="applyFilters"
                                    class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white">
                                <option value="popular">Популярні</option>
                                <option value="newest">Найновіші</option>
                                <option value="oldest">Найстаріші</option>
                                <option value="name">За назвою</option>
                                <option value="books_count">За кількістю книг</option>
                            </select>
                        </div>

                        <!-- Apply Filters Button -->
                        <button @click="applyFilters" 
                                class="w-full bg-gradient-to-r from-pink-500 to-purple-500 text-white py-3 px-6 rounded-xl font-bold hover:from-pink-600 hover:to-purple-600 transition-all duration-300 transform hover:scale-105">
                            Застосувати фільтри
                        </button>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:w-3/4">
                    @if ($libraries->count() > 0)
                        <!-- Libraries Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach ($libraries as $library)
                                <library-collection
                                    :library='@json($library)'
                                    :is-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
                                    :is-liked="{{ auth()->check() && $library->likes()->where('user_id', auth()->id())->exists() ? 'true' : 'false' }}"
                                    :is-saved="{{ auth()->check() && DB::table('user_saved_libraries')->where('user_id', auth()->id())->where('library_id', $library->id)->exists() ? 'true' : 'false' }}"
                                    :likes-count="{{ $library->likes()->count() }}"
                                    @notification="handleNotification"
                                    @liked="handleLiked"
                                    @saved="handleSaved"
                                ></library-collection>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $libraries->appends(request()->query())->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <div class="text-slate-400 dark:text-slate-500 text-6xl mb-4">
                                <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Добірки не знайдено</h3>
                            <p class="text-slate-600 dark:text-slate-400 mb-6">Спробуйте змінити фільтри або створити першу добірку</p>
                            @auth
                                <button @click="openCreateModal"
                                        class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-3 rounded-xl font-bold hover:from-orange-600 hover:to-red-600 transition-all duration-300">
                                    Створити добірку
                                </button>
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Vue приложение для страницы добірок
            document.addEventListener('DOMContentLoaded', function() {
                const librariesApp = new Vue({
                    el: '#app',
                    data: {
                        searchQuery: '',
                        selectedCategory: '',
                        selectedSort: 'popular',
                        @auth
                        user: {
                            username: '{{ auth()->user()->username }}'
                        }
                        @endauth
                    },
                    methods: {
                        applyFilters() {
                            // Redirect with new query parameters
                            const params = new URLSearchParams();
                            if (this.searchQuery) params.append('search', this.searchQuery);
                            if (this.selectedCategory) params.append('category', this.selectedCategory);
                            if (this.selectedSort) params.append('sort', this.selectedSort);
                            
                            const queryString = params.toString();
                            const url = queryString ? `${window.location.pathname}?${queryString}` : window.location.pathname;
                            window.location.href = url;
                        },
                        openCreateModal() {
                            // TODO: Implement create library modal
                            console.log('Open create library modal');
                        },
                        handleNotification(notification) {
                            this.showNotification(notification.message, notification.type);
                        },
                        handleLiked(data) {
                            // Update like status for specific library
                            console.log('Library liked:', data);
                        },
                        handleSaved(data) {
                            // Update save status for specific library
                            console.log('Library saved:', data);
                        },
                        showNotification(message, type = 'success') {
                            // Удаляем существующие уведомления
                            const existingNotifications = document.querySelectorAll('.notification');
                            existingNotifications.forEach(notification => notification.remove());

                            // Создаем новое уведомление
                            const notification = document.createElement('div');
                            notification.className = `notification fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
                                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                            }`;
                            notification.textContent = message;

                            document.body.appendChild(notification);

                            // Анимация появления
                            setTimeout(() => {
                                notification.style.transform = 'translateX(0)';
                            }, 100);

                            // Автоматическое скрытие через 3 секунды
                            setTimeout(() => {
                                notification.style.transform = 'translateX(100%)';
                                setTimeout(() => {
                                    notification.remove();
                                }, 300);
                            }, 3000);
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
