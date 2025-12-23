@extends('layouts.app')

@section('title', 'Редагувати добірку - ' . $library->name)

@section('main')
    <div id="app">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <ol class="flex items-center space-x-2 text-sm">
                    <li><a href="{{ route('libraries.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-orange-500">Добірки</a></li>
                    <li><span class="text-slate-400 dark:text-slate-500">/</span></li>
                    <li><a href="{{ route('libraries.show.slug', ['username' => $library->user->username, 'library' => $library->slug]) }}" class="text-slate-600 dark:text-slate-400 hover:text-orange-500">{{ $library->name }}</a></li>
                    <li><span class="text-slate-400 dark:text-slate-500">/</span></li>
                    <li class="text-slate-900 dark:text-white font-medium">Редагувати</li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-black text-slate-900 dark:text-white mb-2">Редагувати добірку</h1>
                <p class="text-lg text-slate-600 dark:text-slate-400">Змініть назву, опис та видимість вашої добірки</p>
            </div>

            <!-- Edit Form -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 p-8">
                <form action="{{ route('libraries.update', $library) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Library Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Назва добірки
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $library->name) }}"
                               class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white transition-all duration-200"
                               placeholder="Введіть назву добірки"
                               required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Library Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Опис добірки
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white transition-all duration-200 resize-none"
                                  placeholder="Опишіть вашу добірку...">{{ old('description', $library->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Visibility -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                            Видимість
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="radio" 
                                       name="is_private" 
                                       value="0"
                                       {{ old('is_private', $library->is_private ? '1' : '0') == '0' ? 'checked' : '' }}
                                       class="w-4 h-4 text-orange-600 bg-slate-100 border-slate-300 focus:ring-orange-500 dark:focus:ring-orange-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-medium text-slate-900 dark:text-white">Публічна</span>
                                    </div>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">Добірка буде видна всім користувачам</p>
                                </div>
                            </label>
                            
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="radio" 
                                       name="is_private" 
                                       value="1"
                                       {{ old('is_private', $library->is_private ? '1' : '0') == '1' ? 'checked' : '' }}
                                       class="w-4 h-4 text-orange-600 bg-slate-100 border-slate-300 focus:ring-orange-500 dark:focus:ring-orange-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-medium text-slate-900 dark:text-white">Приватна</span>
                                    </div>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">Добірка буде видна тільки вам</p>
                                </div>
                            </label>
                        </div>
                        @error('is_private')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-700">
                        <a href="{{ route('libraries.show.slug', ['username' => $library->user->username, 'library' => $library->slug]) }}" 
                           class="px-6 py-3 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-medium transition-colors">
                            Скасувати
                        </a>
                        
                        <div class="flex space-x-3">
                            <button type="button" 
                                    onclick="deleteLibrary()"
                                    class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-medium transition-all duration-300">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Видалити
                            </button>
                            
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-bold hover:from-orange-600 hover:to-red-600 transition-all duration-300 transform hover:scale-105">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Зберегти зміни
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Books Management Section -->
            <div class="mt-8 bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Книги в добірці</h2>
                        <p class="text-slate-600 dark:text-slate-400">Управління книгами в цій добірці</p>
                    </div>
                    <a href="{{ route('books.index') }}" 
                       class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-indigo-700 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Додати книги
                    </a>
                </div>

                @php
                    $books = $library->books()->with(['author', 'categories'])->paginate(12);
                @endphp

                @if($books->count() > 0)
                    <!-- Books Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($books as $book)
                            <div class="bg-slate-50 dark:bg-slate-700 rounded-xl p-4 hover:shadow-lg transition-all duration-300">
                                <div class="aspect-[3/4] mb-3">
                                    <img src="{{ $book->cover_image }}" 
                                         alt="{{ $book->title }}"
                                         class="w-full h-full object-cover rounded-lg shadow-md">
                                </div>
                                
                                <div class="space-y-2">
                                    <h3 class="font-semibold text-slate-900 dark:text-white line-clamp-2">
                                        <a href="{{ route('books.show', $book->slug) }}" 
                                           class="hover:text-orange-500 transition-colors">
                                            {{ $book->title }}
                                        </a>
                                    </h3>
                                    
                                    <p class="text-sm text-slate-600 dark:text-slate-400">
                                        {{ $book->author->first_name ?? $book->author ?? 'Не указан' }}
                                    </p>
                                    
                                    <button @click="removeBook('{{ $book->slug }}')"
                                            class="w-full px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Видалити
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($books->hasPages())
                        <div class="mt-6">
                            {{ $books->links('vendor.pagination.custom') }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="text-slate-400 dark:text-slate-500 text-6xl mb-4">
                            <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Добірка порожня</h3>
                        <p class="text-slate-600 dark:text-slate-400 mb-6">Додайте книги до вашої добірки</p>
                        <a href="{{ route('books.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-bold hover:from-orange-600 hover:to-red-600 transition-all duration-300">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Додати книги
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Vue приложение для редактирования добірки
            document.addEventListener('DOMContentLoaded', function() {
                const editApp = new Vue({
                    el: '#app',
                    data: {
                        @auth
                        user: {
                            username: '{{ auth()->user()->username }}'
                        }
                        @endauth
                    },
                    methods: {
                        async removeBook(bookSlug) {
                            const confirmed = await confirm('Ви впевнені, що хочете видалити цю книгу з добірки?', 'Підтвердження', 'warning');
                            if (confirmed) {
                                try {
                                    const response = await fetch(`/libraries/{{ $library->id }}/books/${bookSlug}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json'
                                        }
                                    });
                                    
                                    const data = await response.json();
                                    
                                    if (data.success) {
                                        this.showNotification('Книгу успішно видалено з добірки!', 'success');
                                        setTimeout(() => {
                                            location.reload();
                                        }, 1000);
                                    } else {
                                        this.showNotification(data.message || 'Помилка при видаленні книги', 'error');
                                    }
                                } catch (error) {
                                    console.error('Error removing book:', error);
                                    this.showNotification('Помилка при видаленні книги', 'error');
                                }
                            }
                        },
                        async deleteLibrary() {
                            const confirmed = await confirm('Ви впевнені, що хочете видалити цю добірку? Цю дію неможливо скасувати.', 'Підтвердження', 'warning');
                            if (confirmed) {
                                try {
                                    const response = await fetch(`/libraries/{{ $library->id }}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json'
                                        }
                                    });
                                    
                                    if (response.ok) {
                                        this.showNotification('Добірку успішно видалено!', 'success');
                                        setTimeout(() => {
                                            window.location.href = '{{ route("libraries.index") }}';
                                        }, 1000);
                                    } else {
                                        this.showNotification('Помилка при видаленні добірки', 'error');
                                    }
                                } catch (error) {
                                    console.error('Error deleting library:', error);
                                    this.showNotification('Помилка при видаленні добірки', 'error');
                                }
                            }
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

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>