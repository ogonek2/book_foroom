@extends('users.public.main')

@section('profile-content')
    <div class="flex-1">
        <!-- Rating Statistics -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Статистика оцінок</h2>
            
            @if ($stats['total_rated_books'] > 0)
                <div class="flex items-center justify-center">
                    <!-- Vertical Rating Bars -->
                    <div class="flex items-end space-x-1">
                        @for ($i = 1; $i <= 10; $i++)
                            @php
                                $percentage = $stats['total_rated_books'] > 0 ? ($ratingStats[$i] / $stats['total_rated_books']) * 100 : 0;
                                
                                // Определяем цвет в зависимости от оценки
                                $barColor = match(true) {
                                    $i >= 9 => 'from-green-500 to-emerald-500', // 9-10: зеленый
                                    $i >= 7 => 'from-blue-500 to-cyan-500',     // 7-8: синий
                                    $i >= 5 => 'from-yellow-500 to-orange-500', // 5-6: желтый
                                    $i >= 3 => 'from-orange-500 to-red-500',    // 3-4: оранжевый
                                    default => 'from-red-500 to-pink-500'       // 1-2: красный
                                };
                            @endphp
                            <div class="flex flex-col items-center space-y-1">
                                <!-- Vertical Progress Bar -->
                                <div class="relative w-6 h-32 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                                    <div class="absolute bottom-0 w-full bg-gradient-to-t {{ $barColor }} rounded-full transition-all duration-300"
                                         style="height: {{ $percentage }}%">
                                    </div>
                                </div>
                                
                                <!-- Rating Label -->
                                <div class="text-xs font-medium text-gray-900 dark:text-white">{{ $i }}</div>
                                
                                <!-- Count Label -->
                                <div class="text-xs text-gray-600 dark:text-gray-400">{{ $ratingStats[$i] }}</div>
                            </div>
                        @endfor
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Усього оцінено: {{ $stats['total_rated_books'] }} книг</span>
                    @if($stats['average_rating'])
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-4">Середня оцінка: {{ number_format($stats['average_rating'], 1) }} / 10</span>
                    @endif
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-600 dark:text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Поки немає оцінок</h3>
                    <p class="text-gray-600 dark:text-gray-400">Користувач ще не оцінив жодної книги</p>
                </div>
            @endif
        </div>

        <!-- Recent Books Section -->
        <div class="mb-8">
            <div class="flex flex-col mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Останні прочитані книги</h2>
                <a href="{{ route('users.public.library', $user->username) }}"
                    class="text-purple-600 dark:text-purple-300 hover:text-purple-700 mt-2 dark:hover:text-purple-200 text-sm font-medium">
                    Переглянути всі →
                </a>
            </div>

            @if ($recentReadBooks->count() > 0)
                <div id="recent-books-app" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($recentReadBooks as $readingStatus)
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
                        <book-card
                            :book='@json($bookData)'
                            :user-libraries='@json($userLibraries ?? [])'
                            :is-authenticated="@json(auth()->check())"
                        ></book-card>
                    @endforeach
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
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Ще немає прочитаних книг</h3>
                    <p class="text-gray-600 dark:text-gray-400">Користувач ще не додав книги до своєї бібліотеки</p>
                </div>
            @endif
        </div>

        <!-- Recent Reviews Section -->
        <div>
            <div class="flex flex-col mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Останні рецензії</h2>
                <a href="{{ route('users.public.reviews', $user->username) }}"
                    class="text-purple-600 dark:text-purple-300 hover:text-purple-700 mt-2 dark:hover:text-purple-200 text-sm font-medium">
                    Переглянути всі →
                </a>
            </div>

            @if ($recentReviews->count() > 0)
                <div class="space-y-6">
                    @foreach ($recentReviews as $review)
                        <div class="bg-white/5 rounded-xl p-6 hover:bg-white/10 transition-all">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <a href="{{ route('books.show', $review->book->slug) }}"
                                        class="text-xl font-semibold text-gray-900 dark:text-white hover:text-purple-600 dark:hover:text-purple-200 transition-colors">
                                        {{ $review->book->title }}
                                    </a>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">{{ $review->book->author }}</p>
                                </div>

                                @if ($review->rating)
                                    <div class="flex items-center ml-4">
                                        <div class="flex space-x-1">
                                            <i class="fa-solid fa-star text-yellow-500"></i>
                                        </div>
                                        <span class="ml-2 text-gray-900 dark:text-white font-medium">{{ $review->rating }}/10</span>
                                    </div>
                                @endif
                            </div>

                            @if($review->contains_spoiler)
                                <!-- Spoiler Content -->
                                <div class="relative mb-4">
                                    <div class="relative overflow-hidden rounded-lg py-8 px-4">
                                        <div class="text-gray-700 dark:text-gray-300 leading-relaxed break-words blur-sm filter"
                                             style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                                            {{ strip_tags(Str::limit($review->content, 250)) }}
                                        </div>
                                        <!-- Semi-transparent overlay -->
                                        <div class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 backdrop-blur-sm flex items-center justify-center rounded-lg">
                                            <div class="text-center px-4 py-6">
                                                <p class="text-gray-900 dark:text-white text-base font-bold mb-4 drop-shadow-sm">Рецензія містить спойлер</p>
                                                <a href="{{ route('books.show', $review->book->slug) }}/reviews/{{ $review->id }}"
                                                   class="inline-block bg-indigo-600 dark:bg-indigo-500 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors shadow-md">
                                                    Читати
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">
                                    {{ strip_tags(Str::limit($review->content, 250)) }}
                                        <br><a href="{{ route('books.show', $review->book->slug) }}/reviews/{{ $review->id }}" 
                                               class="text-orange-500 hover:text-orange-600 font-medium">
                                            До рецензії →
                                        </a>
                                </div>
                            @endif

                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                            </path>
                                        </svg>
                                        <span>{{ $review->likes_count ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                            </path>
                                        </svg>
                                        <span>{{ $review->replies_count ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="text-gray-500 dark:text-gray-500">
                                    {{ $review->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-600 dark:text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Ще немає рецензій</h3>
                    <p class="text-gray-600 dark:text-gray-400">Користувач ще не написав жодної рецензії</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Инициализация Vue для карточек книг
                if (document.getElementById('recent-books-app')) {
                    new Vue({
                        el: '#recent-books-app',
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
