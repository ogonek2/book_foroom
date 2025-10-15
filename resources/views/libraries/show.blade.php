@extends('layouts.app')

@section('title', $library->name . ' - Добірка')

@section('main')
    <div id="app" class="min-h-screen from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
        <div class="max-w-4xl mx-auto py-2">
            <!-- Breadcrumb -->
            <nav class="mb-2">
                <ol class="flex items-center space-x-2 text-sm">
                    <li><a href="{{ route('libraries.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-orange-500">Добірки</a></li>
                    <li><span class="text-slate-400 dark:text-slate-500">/</span></li>
                    <li class="text-slate-900 dark:text-white font-medium">{{ $library->name }}</li>
                </ol>
            </nav>

            <!-- Library Header -->
            <div class="py-8 mb-8">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <!-- Library Title -->
                        <h1 class="text-4xl font-black text-slate-900 dark:text-white mb-4">{{ $library->name }}</h1>
                        
                        <!-- Author Info -->
                        <div class="flex items-center space-x-4 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                                @if($library->user->avatar)
                                    <img src="{{ $library->user->avatar }}" alt="{{ $library->user->name }}" class="w-12 h-12 rounded-full">
                                @else
                                    <span class="text-white font-bold text-lg">{{ $library->user->name[0] }}</span>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('profile.show', $library->user->username) }}">
                                    <p class="text-lg font-medium text-slate-900 dark:text-white">{{ $library->user->name }}</p>
                                </a>
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $library->created_at->format('d.m.Y') }}</p>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($library->description)
                            <p class="text-lg text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">{{ $library->description }}</p>
                        @endif

                        <!-- Stats -->
                        <div class="flex items-center space-x-6">
                            <div class="flex items-center space-x-2 text-slate-600 dark:text-slate-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $library->likes()->count() }} лайків</span>
                            </div>
                            <div class="flex items-center space-x-2 text-slate-600 dark:text-slate-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $library->books_count }} книг</span>
                            </div>
                            @if($library->is_private)
                                <div class="flex items-center space-x-2 text-slate-600 dark:text-slate-400">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Приватна</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- Action Buttons -->
            <div class="flex items-center gap-4">
                @auth
                    <button @click="toggleLike" 
                            :class="isLiked ? 'text-red-500 hover:text-red-600' : 'bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600'"
                            class="flex items-center space-x-2 rounded-xl font-medium transition-all duration-300">
                        <i class="fas fa-heart"></i>
                        <span>{{ $library->likes()->count() }}</span>
                    </button>

                    <button @click="toggleSave" 
                            :class="isSaved ? 'text-blue-500 hover:text-blue-600' : 'bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600'"
                            class="flex items-center space-x-2 rounded-xl font-medium transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                        </svg>
                        <span>{{ $isSaved ? 'Збережено' : 'Зберегти' }}</span>
                    </button>
                @endauth

                <button @click="shareLibrary" 
                        class="flex items-center space-x-2 rounded-xl font-medium transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                    </svg>
                </button>
            </div>

            <!-- Books Section -->
            <div class="py-4">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Книги в добірці</h2>
                    <span class="text-slate-600 dark:text-slate-400">{{ $books->total() }} книг</span>
                </div>

                @if($books->count() > 0)
                    <!-- Books Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">
                        @foreach($books as $book)
                            <book-card
                                :book='@json($book)'
                                :user-libraries='@json([])'
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
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="text-slate-400 dark:text-slate-500 text-6xl mb-4">
                            <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Добірка порожня</h3>
                        <p class="text-slate-600 dark:text-slate-400">В цій добірці ще немає книг</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Vue приложение для страницы добірки
            document.addEventListener('DOMContentLoaded', function() {
                const libraryShowApp = new Vue({
                    el: '#app',
                    data: {
                        isLiked: {{ $library->likes()->where('user_id', auth()->id())->exists() ? 'true' : 'false' }},
                        isSaved: {{ $isSaved ? 'true' : 'false' }},
                        likesCount: {{ $library->likes()->count() }},
                        libraryId: {{ $library->id }},
                        @auth
                        user: {
                            username: '{{ auth()->user()->username }}'
                        }
                        @endauth
                    },
                    methods: {
                        async toggleLike() {
                            if (!{{ auth()->check() ? 'true' : 'false' }}) {
                                this.showNotification('Необхідна авторизація', 'error');
                                return;
                            }

                            try {
                                const response = await axios.post(`/libraries/${this.libraryId}/like`);
                                if (response.data.success) {
                                    this.isLiked = response.data.is_liked;
                                    this.likesCount = response.data.likes_count;
                                    this.showNotification(response.data.message, 'success');
                                }
                            } catch (error) {
                                console.error('Error toggling like:', error);
                                this.showNotification('Помилка при збереженні лайка', 'error');
                            }
                        },
                        async toggleSave() {
                            if (!{{ auth()->check() ? 'true' : 'false' }}) {
                                this.showNotification('Необхідна авторизація', 'error');
                                return;
                            }

                            try {
                                const response = await fetch(`/libraries/${this.libraryId}/save`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    }
                                });
                                
                                const data = await response.json();
                                
                                if (data.success) {
                                    this.isSaved = data.is_saved;
                                    this.showNotification(data.message, 'success');
                                } else {
                                    this.showNotification(data.message || 'Помилка при збереженні добірки', 'error');
                                }
                            } catch (error) {
                                console.error('Error toggling save:', error);
                                this.showNotification('Помилка при збереженні добірки', 'error');
                            }
                        },
                        shareLibrary() {
                            if (navigator.share) {
                                navigator.share({
                                    title: '{{ $library->name }}',
                                    text: '{{ $library->description ?: "Добірка від " . $library->user->name }}',
                                    url: window.location.href
                                }).catch(error => {
                                    console.log('Error sharing:', error);
                                    this.fallbackShare();
                                });
                            } else {
                                this.fallbackShare();
                            }
                        },
                        fallbackShare() {
                            navigator.clipboard.writeText(window.location.href).then(() => {
                                this.showNotification('Посилання скопійовано в буфер обміну', 'success');
                            }).catch(() => {
                                alert(`Посилання на добірку: ${window.location.href}`);
                            });
                        },
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