@extends('users.public.main')

@section('title', $user->name . ' - Бібліотека')

@section('profile-content')
    <div class="flex-1">
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Бібліотека користувача</h2>

            <!-- Library Stats -->
            @php
                $readingStats = [
                    'read_count' => $user->readingStatuses()->where('status', 'read')->count(),
                    'reading_count' => $user->readingStatuses()->where('status', 'reading')->count(),
                    'want_to_read_count' => $user->readingStatuses()->where('status', 'want_to_read')->count(),
                    'average_rating' => $user
                        ->readingStatuses()
                        ->where('status', 'read')
                        ->whereNotNull('rating')
                        ->avg('rating'),
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $readingStats['read_count'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">Прочитано</div>
                </div>
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $readingStats['reading_count'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">Читає</div>
                </div>
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $readingStats['want_to_read_count'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">Планує</div>
                </div>
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $readingStats['average_rating'] ? number_format($readingStats['average_rating'], 1) : '0.0' }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">Середня оцінка</div>
                </div>
            </div>

            <!-- Books Grid -->
            @php
                $allBooks = $user->readingStatuses()->with('book')->orderBy('created_at', 'desc')->paginate(20);
            @endphp

            @if ($allBooks->count() > 0)
                <div id="user-library-app" class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-6">
                    @foreach ($allBooks as $readingStatus)
                        @php
                            $bookData = [
                                'id' => $readingStatus->book->id,
                                'slug' => $readingStatus->book->slug,
                                'title' => $readingStatus->book->title,
                                'author' => $readingStatus->book->author->first_name ?? $readingStatus->book->author ?? 'Автор невідомий',
                                'cover_image' => $readingStatus->book->cover_image,
                                'rating' => $readingStatus->rating ?? $readingStatus->book->rating ?? 0,
                                'reviews_count' => $readingStatus->book->reviews_count ?? 0,
                                'pages' => $readingStatus->book->pages ?? 0
                            ];
                        @endphp
                        <user-library-book-card
                            :book='@json($bookData)'
                            reading-status="{{ $readingStatus->status }}"
                        ></user-library-book-card>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $allBooks->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-600 dark:text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Бібліотека порожня</h3>
                    <p class="text-gray-600 dark:text-gray-400">Користувач ще не додав книги до своєї бібліотеки</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Инициализация Vue для библиотеки пользователя
                if (document.getElementById('user-library-app')) {
                    new Vue({
                        el: '#user-library-app',
                        data: {
                            @auth
                            user: {
                                username: '{{ auth()->user()->username }}'
                            }
                            @endauth
                        },
                        methods: {
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
                }
            });
        </script>
    @endpush
@endsection
