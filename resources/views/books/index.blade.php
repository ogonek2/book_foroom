@extends('layouts.app')

@section('title', 'Каталог книг')

@section('main')
    <div id="app" class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Каталог книг</h1>
            <p class="text-gray-600 dark:text-gray-400">Знайдіть цікаві книги для читання</p>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Filters -->
                <div
                    class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Фільтри</h3>
                    <form method="GET" action="{{ route('books.index') }}" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Пошук</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Назва, автор..."
                                class="w-full px-4 py-3 border-0 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-gray-600 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Категорія</label>
                            <select name="category"
                                class="w-full px-4 py-3 border-0 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-gray-600 transition-all duration-200">
                                <option value="">Всі категорії</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->slug }}"
                                        {{ request('category') === $category->slug ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Сортування</label>
                            <select name="sort"
                                class="w-full px-4 py-3 border-0 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-gray-600 transition-all duration-200">
                                <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>За рейтингом
                                </option>
                                <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>За назвою
                                </option>
                                <option value="author" {{ request('sort') === 'author' ? 'selected' : '' }}>За автором
                                </option>
                                <option value="year" {{ request('sort') === 'year' ? 'selected' : '' }}>За роком</option>
                                <option value="reviews" {{ request('sort') === 'reviews' ? 'selected' : '' }}>За відгуками
                                </option>
                            </select>
                        </div>
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-4 py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-105">
                            Застосувати фільтри
                        </button>
                    </form>
                </div>
            </div>

            <!-- Books Grid -->
            <div class="lg:col-span-3">
                @if ($books->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">
                        @foreach ($books as $book)
                            <book-card 
                                :book='@json($book)'
                                :user-libraries='@json($userLibraries)'
                                :is-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
                                @notification="handleNotification"
                            ></book-card>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $books->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div
                            class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Книги не знайдені</h3>
                        <p class="text-gray-500 dark:text-gray-400">Спробуйте змінити параметри пошуку</p>
                    </div>
                @endif
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            // Vue приложение для страницы книг
            document.addEventListener('DOMContentLoaded', function() {
                // Создаем новое Vue приложение для этой страницы
                const booksApp = new Vue({
                    el: '#app',
                    data: {
                        // Данные для уведомлений
                        @auth
                        user: {
                            username: '{{ auth()->user()->username }}'
                        }
                        @endauth
                    },
                    methods: {
                        handleNotification(notification) {
                            this.showNotification(notification.message, notification.type);
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
