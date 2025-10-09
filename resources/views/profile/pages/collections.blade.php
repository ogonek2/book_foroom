@extends('profile.private.main')

@section('profile-content')
<div id="app">
        <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Мої добірки</h2>
        @if (auth()->check() && auth()->user()->id === $user->id)
            <a href="{{ route('libraries.create') }}"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg font-medium hover:from-purple-600 hover:to-pink-600 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Створити добірку
            </a>
        @endif
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if ($libraries->count() > 0)
            <!-- Libraries Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                    @foreach ($libraries as $library)
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/30 p-6 hover:shadow-2xl transition-all duration-300 cursor-pointer group"
                         onclick="openLibrary({{ $library->id }})">
                        <!-- Header with library info -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2 line-clamp-2">{{ $library->name }}</h3>
                                <div class="flex items-center space-x-4 text-sm text-slate-500 dark:text-slate-400">
                                    <span class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $library->books_count }} книг</span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        @if ($library->is_private)
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Приватна</span>
                                        @else
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Публічна</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Menu button (only for owner) -->
                            @if (auth()->check() && auth()->user()->id === $user->id)
                                <div class="relative">
                                    <button @click.stop="toggleLibraryMenu({{ $library->id }})" 
                                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>
                                    
                                    <!-- Dropdown Menu -->
                                    <div v-if="activeMenuId === {{ $library->id }}" 
                                         @click.stop
                                         class="absolute right-0 top-8 bg-white dark:bg-slate-700 rounded-lg shadow-lg border border-slate-200 dark:border-slate-600 py-2 z-10 min-w-[160px]">
                                        <button @click="editLibrary({{ $library->id }})"
                                                class="w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600 flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <span>Редагувати</span>
                                        </button>
                                        <button @click="deleteLibrary({{ $library->id }})"
                                                class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span>Видалити</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Books preview -->
                        <div class="mb-4">
                            <div class="flex space-x-2">
                                @php
                                    $previewBooks = $library->books()->limit(3)->get();
                                @endphp
                                
                                @for($i = 0; $i < 3; $i++)
                                    @if($i < $previewBooks->count())
                                        <div class="flex-shrink-0">
                                            <img src="{{ $previewBooks[$i]->cover_image ?? 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=200&h=300&fit=crop&crop=center' }}"
                                                 alt="{{ $previewBooks[$i]->title }}"
                                                 class="w-12 h-16 object-cover rounded-lg shadow-md">
                                    </div>
                                    @elseif($i == 2 && $library->books_count > 3)
                                        <div class="w-12 h-16 bg-gradient-to-br from-orange-400 to-red-500 rounded-lg flex items-center justify-center backdrop-blur-sm">
                                            <span class="text-white font-bold text-xs">+{{ $library->books_count - 2 }}</span>
                                        </div>
                                    @else
                                        <div class="w-12 h-16 bg-gray-300 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                            </svg>
                                            </div>
                                        @endif
                                @endfor
                            </div>
                        </div>

                        <!-- Footer with stats -->
                        <div class="flex items-center justify-between text-sm text-slate-500 dark:text-slate-400">
                            <span>{{ $library->created_at->format('d.m.Y') }}</span>
                            <div class="flex items-center space-x-3">
                                <span class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $library->likes()->count() }}</span>
                                </span>
                                <span class="text-slate-400">•</span>
                                <span class="text-slate-400">Переглянути →</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="text-slate-400 dark:text-slate-500 text-6xl mb-4">
                    <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
        </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Добірки не знайдено</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Створіть першу добірку для збереження улюблених книг</p>
                @if (auth()->check() && auth()->user()->id === $user->id)
                    <a href="{{ route('libraries.create') }}"
                            class="inline-flex items-center bg-gradient-to-r from-purple-500 to-pink-500 text-white px-6 py-3 rounded-xl font-bold hover:from-purple-600 hover:to-pink-600 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Створити добірку
                    </a>
                @endif
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Vue приложение для страницы профиля
            document.addEventListener('DOMContentLoaded', function() {
                const profileApp = new Vue({
                    el: '#app',
                    data: {
                        activeMenuId: null,
                        @auth
                        user: {
                            username: '{{ auth()->user()->username }}'
                        }
                        @endauth
                    },
                    methods: {
                        openLibrary(libraryId) {
                            window.location.href = `/libraries/${libraryId}`;
                        },
                        toggleLibraryMenu(libraryId) {
                            this.activeMenuId = this.activeMenuId === libraryId ? null : libraryId;
                        },
                        editLibrary(libraryId) {
                            window.location.href = `/libraries/${libraryId}/edit`;
                        },
                        async deleteLibrary(libraryId) {
                            if (confirm('Ви впевнені, що хочете видалити цю добірку? Цю дію неможливо скасувати.')) {
                                try {
                                    const response = await axios.delete(`/libraries/${libraryId}`);
                                    if (response.status === 200) {
                                        this.showNotification('Добірку успішно видалено!', 'success');
                                        setTimeout(() => {
                                            location.reload();
                                        }, 1000);
                                    }
                                } catch (error) {
                                    console.error('Error deleting library:', error);
                                    this.showNotification('Помилка при видаленні добірки', 'error');
                                }
                            }
                            this.activeMenuId = null;
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

                // Закрываем меню при клике вне его
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.relative')) {
                        profileApp.activeMenuId = null;
                    }
                });
            });

            // Функция для создания библиотеки (оставляем для совместимости)
            function openCreateLibraryModal() {
                window.location.href = '/libraries/create';
            }
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