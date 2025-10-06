@extends('layouts.app')

@section('title', 'Каталог книг')

@section('main')
    <div class="max-w-7xl mx-auto">
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
                            <div
                                class="group bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 overflow-hidden hover:shadow-xl transition-all duration-300 hover:scale-105">
                                <div class="p-4">
                                    <div class="flex space-x-4">
                                        <div class="flex-shrink-0">
                                            <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=200&h=300&fit=crop&crop=center' }}"
                                                alt="{{ $book->title }}"
                                                class="aspect-[3/4] object-cover rounded-lg shadow-md group-hover:shadow-lg transition-all duration-300"
                                                style="width: 120px; height: 170px;">
                                        </div>
                                        <div class="flex w-full flex-col h-auto justify-between">
                                            <div>
                                                <h3
                                                    class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors mb-1">
                                                    <a
                                                        href="{{ route('books.show', $book->slug) }}">{{ $book->title }}</a>
                                                </h3>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-2">
                                                    {{ $book->author }}</p>
                                                <div class="flex items-center mb-2">
                                                    <div class="flex items-center">
                                                        @for ($i = 1; $i <= 10; $i++)
                                                            <svg class="w-3 h-3 {{ $i <= $book->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path
                                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                            </svg>
                                                        @endfor
                                                    </div>
                                                    <span
                                                        class="ml-2 text-sm text-gray-600 dark:text-gray-400 font-medium">{{ number_format($book->rating, 1) }}/10</span>
                                                    <span
                                                        class="ml-1 text-sm text-gray-500 dark:text-gray-500">({{ $book->readingStatuses()->whereNotNull('rating')->count() }})</span>
                                                </div>
                                            </div>
                                            <div class="flex justify-end w-full">
                                                <div class="flex justify-between w-full align-center">
                                                    <div class="flex items-center space-x-4 ">
                                                        <div
                                                            class="flex items-center space-x-2 text-slate-600 dark:text-slate-400">
                                                            <i class="fas fa-feather"></i>
                                                            <span
                                                                class="text-base font-semibold">{{ $book->reviews()->whereNull('parent_id')->count() }}</span>
                                                        </div>
                                                        <div
                                                            class="flex items-center space-x-2 text-slate-600 dark:text-slate-400">
                                                            <i class="fas fa-paragraph"></i>
                                                            <span
                                                                class="text-base font-semibold">{{ $book->pages ?? 0 }}</span>
                                                        </div>
                                                    </div>
                                                    @auth
                                                        <button onclick="openAddToLibraryModal('{{ $book->slug }}')"
                                                            class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 font-bold py-2 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                                                            <div class="flex items-center space-x-2">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                                </svg>
                                                                <span class="text-sm">Додати</span>
                                                            </div>
                                                        </button>
                                                    @else
                                                        <a href="{{ route('login') }}"
                                                            class="bg-gradient-to-r from-gray-500 to-gray-600 text-white hover:from-gray-600 hover:to-gray-700 font-bold py-2 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                                                            <div class="flex items-center space-x-2">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                                </svg>
                                                                <span class="text-sm">Додати</span>
                                                            </div>
                                                        </a>
                                                    @endauth
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

    <!-- Modal для добавления книги в библиотеку -->
    @auth
        <div id="addToLibraryModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0"
                id="addToLibraryModalContent">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Додати до добірки</h3>
                        <button onclick="closeAddToLibraryModal()"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="" method="POST" id="addToLibraryForm">
                        @csrf
                        <input type="hidden" name="book_slug" id="bookSlug" value="">

                        <div class="mb-6">
                            <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-3">Оберіть
                                добірку</label>
                            <select name="library_id" id="librarySelect" required
                                class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-slate-900 dark:text-white">
                                <option value="">-- Оберіть добірку --</option>
                                @foreach ($userLibraries as $library)
                                    <option value="{{ $library->id }}"
                                        data-url="{{ route('libraries.addBook', $library) }}">{{ $library->name }}
                                        @if ($library->is_private)
                                            (Приватна)
                                        @else
                                            (Публічна)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex space-x-3">
                            <button type="submit"
                                class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-6 rounded-xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                                Додати
                            </button>
                            <button type="button" onclick="closeAddToLibraryModal()"
                                class="flex-1 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 py-3 px-6 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-all duration-300">
                                Скасувати
                            </button>
                        </div>
                    </form>

                    @if ($userLibraries->count() === 0)
                        <div class="mt-4 p-4 bg-slate-100 dark:bg-slate-700 rounded-xl">
                            <p class="text-slate-600 dark:text-slate-400 text-sm mb-3">У вас ще немає добірок</p>
                            <a href="{{ auth()->user() ? route('profile.collections', auth()->user()->username) : '#' }}"
                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-sm font-medium">
                                Створити першу добірку →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endauth

    @push('scripts')
        <script>
            let currentBookSlug = null;

            function openAddToLibraryModal(bookSlug) {
                currentBookSlug = bookSlug;
                document.getElementById('bookSlug').value = bookSlug;
                
                const modal = document.getElementById('addToLibraryModal');
                const modalContent = document.getElementById('addToLibraryModalContent');

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                // Анимация появления
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            function closeAddToLibraryModal() {
                const modal = document.getElementById('addToLibraryModal');
                const modalContent = document.getElementById('addToLibraryModalContent');

                // Анимация исчезновения
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 300);
            }

            // Обработчик для закрытия модального окна по клику на фон
            document.addEventListener('DOMContentLoaded', function() {
                const addToLibraryModal = document.getElementById('addToLibraryModal');
                if (addToLibraryModal) {
                    addToLibraryModal.addEventListener('click', function(e) {
                        if (e.target === addToLibraryModal) {
                            closeAddToLibraryModal();
                        }
                    });
                }

                // Обработчик формы добавления в библиотеку
                const addToLibraryForm = document.getElementById('addToLibraryForm');
                if (addToLibraryForm) {
                    addToLibraryForm.addEventListener('submit', async function(e) {
                        e.preventDefault();

                        const librarySelect = document.getElementById('librarySelect');
                        const selectedOption = librarySelect.options[librarySelect.selectedIndex];
                        
                        if (!selectedOption.value) {
                            alert('Оберіть добірку');
                            return;
                        }

                        const url = selectedOption.getAttribute('data-url');
                        const formData = new FormData();
                        formData.append('book_slug', currentBookSlug);
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                body: formData
                            });

                            if (response.ok) {
                                const data = await response.json();
                                if (data.success) {
                                    showNotification('Книга успішно додана до добірки!', 'success');
                                    closeAddToLibraryModal();
                                } else {
                                    showNotification(data.message || 'Помилка при додаванні книги', 'error');
                                }
                            } else {
                                showNotification('Помилка при додаванні книги', 'error');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            showNotification('Помилка при додаванні книги', 'error');
                        }
                    });
                }
            });

            // Функция для показа уведомлений
            function showNotification(message, type = 'success') {
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
        </script>
    @endpush
@endsection
