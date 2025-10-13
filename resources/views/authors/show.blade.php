@extends('layouts.app')

@section('title', $author->full_name . ' - Книжковий форум')

@section('main')
    <div
        class="min-h-screen from-slate-50 via-blue-50 to-indigo-100 dark:from-slate-900 dark:via-slate-800 dark:to-indigo-900">
        <div class="">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <ol class="flex items-center space-x-3 text-sm text-slate-500 dark:text-slate-400 font-medium">
                    <li><a href="{{ route('home') }}"
                            class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">Головна</a>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <a href="{{ route('authors.index') }}"
                            class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">Автори</a>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-slate-900 dark:text-white font-bold">{{ $author->full_name }}</span>
                    </li>
                </ol>
            </nav>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3 space-y-8">
                    <!-- Author Details Card -->
                    <div class="relative">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Sticky Author Photo -->
                            <div class="lg:col-span-1">
                                <div class="sticky top-24">
                                    <div class="relative group">
                                        @if ($author->photo_url)
                                            <img src="{{ $author->photo_url }}" alt="{{ $author->full_name }}"
                                                class="w-full h-100 aspect-[3/4] object-cover rounded-2xl shadow-2xl group-hover:shadow-3xl transition-all duration-500">
                                        @else
                                            <div
                                                class="w-full h-100 bg-slate-100 dark:bg-slate-700 flex items-center justify-center rounded-2xl shadow-2xl">
                                                <svg class="w-24 h-24 text-slate-400 dark:text-slate-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                                        </div>
                                    </div>
                                    <div
                                        class="flex justify-center mt-4 w-full p-4 bg-slate-50 dark:bg-slate-700/50 rounded-2xl space-x-4">
                                        <div class="flex items-center space-x-2 text-xl" style="color: #F97316;">
                                            <i class="fas fa-book"></i>
                                            <span class="font-medium text-lg">{{ $stats['total_books'] }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2 text-xl">
                                            <i class="fas fa-feather text-gray-400"></i>
                                            <span class="text-white-700 text-lg">{{ $stats['total_reviews'] }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2 text-xl">
                                            <i class="fas fa-quote-left text-gray-400"></i>
                                            <span class="text-white-700 text-lg">{{ $stats['total_quotes'] }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2 text-xl">
                                            <i class="fas fa-info-circle text-gray-400"></i>
                                            <span class="text-white-700 text-lg">{{ $stats['total_facts'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Author Content -->
                            <div class="lg:col-span-2">
                                <div class="space-y-6">
                                    <h1 class="text-3xl font-black text-slate-900 dark:text-white leading-tight">
                                        {{ $author->full_name }}
                                    </h1>

                                    <div class="relative">
                                        <div id="biography-text"
                                            class="prose prose-lg max-w-none text-slate-700 dark:text-slate-300 leading-relaxed transition-all duration-300"
                                            style="max-height: 280px; overflow: hidden;">
                                            {!! nl2br(e($author->biography)) !!}
                                        </div>
                                        <div id="biography-gradient"
                                            class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-slate-50 dark:from-slate-900 to-transparent pointer-events-none transition-opacity duration-300"
                                            style="top: 200px;"></div>
                                        <div id="biography-toggle-container" class="flex justify-left mt-4 hidden">
                                            <button id="toggle-biography" onclick="toggleBiography()"
                                                class="inline-flex items-center py-2 text-orange-600 dark:text-orange-400 font-medium hover:text-orange-700 dark:hover:text-orange-300 transition-colors duration-200">
                                                <span id="biography-button-text">Розгорнути</span>
                                                <svg id="biography-arrow"
                                                    class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Author's Books -->
                    <section class="py-12">
                        <div class="flex items-start justify-between mb-8">
                            <h2 class="text-2xl font-black text-slate-900 dark:text-white">
                                Книги <small
                                    class="text-slate-600 dark:text-slate-400 text-lg font-semibold">({{ $books->total() }})</small>
                            </h2>
                        </div>

                        @if ($books->count() > 0)
                            <div class="grid grid-cols-2 lg:grid-cols-2 gap-6" id="author-books-section">
                                @foreach ($books as $book)
                                    <book-card
                                        :book="{{ json_encode([
                                            'id' => $book->id,
                                            'title' => $book->title,
                                            'slug' => $book->slug,
                                            'cover_image' => $book->cover_image_display ?? null,
                                            'publication_year' => $book->publication_year ?? 'N/A',
                                            'categories' => $book->categories->map(function ($cat) {
                                                return ['id' => $cat->id, 'name' => $cat->name];
                                            }),
                                            'rating' => $book->display_rating ?? 0,
                                            'reviews_count' => $book->reviews_count ?? 0,
                                        ]) }}"
                                        :is-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
                                        :user="{{ json_encode(auth()->user()) }}"
                                        :user-libraries="{{ json_encode($userLibraries) }}" card-width="100%"
                                        :show-stats="true"></book-card>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-12">
                                {{ $books->links() }}
                            </div>
                        @else
                            <!-- No Books Found -->
                            <div class="text-center py-12">
                                <div class="max-w-md mx-auto">
                                    <div
                                        class="mx-auto w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-12 h-12 text-slate-400 dark:text-slate-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">
                                        Книги не знайдені
                                    </h3>
                                    <p class="text-slate-600 dark:text-slate-400 mb-6">
                                        У цього автора поки немає книг у каталозі
                                    </p>
                                    <a href="{{ route('authors.index') }}"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg font-medium transition-colors duration-200">
                                        Повернутися до авторів
                                    </a>
                                </div>
                            </div>
                        @endif
                    </section>

                    <!-- Author Reviews Section -->
                    <section id="author-reviews-section">

                        @if ($reviews->count() > 0)
                            <reviews-list book-slug="{{ $reviews->first()->book->slug ?? '' }}"
                                :current-user-id="{{ auth()->id() ?? 'null' }}" 
                                :user-review="null"
                                :hide-header="true"
                                :hide-add-button="true"
                                :reviews="{{ json_encode(
                                    $reviews->map(function ($review) {
                                            return [
                                                'id' => $review->id,
                                                'content' => $review->content,
                                                'rating' => $review->rating,
                                                'contains_spoiler' => $review->contains_spoiler ?? false,
                                                'user' => $review->user
                                                    ? [
                                                        'id' => $review->user->id,
                                                        'name' => $review->user->name,
                                                        'username' => $review->user->username ?? null,
                                                        'avatar_display' => $review->user->avatar_display ?? null,
                                                    ]
                                                    : null,
                                                'book' => [
                                                    'id' => $review->book->id,
                                                    'title' => $review->book->title,
                                                    'slug' => $review->book->slug,
                                                ],
                                                'likes_count' => $review->likes_count ?? 0,
                                                'replies_count' => $review->replies_count ?? 0,
                                                'is_liked' => false,
                                                'created_at' => $review->created_at->toIso8601String(),
                                            ];
                                        })->values(),
                                ) }}"></reviews-list>
                        @else
                            <div class="text-center py-12">
                                <div class="max-w-md mx-auto">
                                    <div
                                        class="mx-auto w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-12 h-12 text-slate-400 dark:text-slate-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">
                                        Рецензій не знайдено
                                    </h3>
                                    <p class="text-slate-600 dark:text-slate-400 mb-6">
                                        У цього автора поки немає рецензій на книги
                                    </p>
                                </div>
                            </div>
                        @endif
                    </section>

                    <!-- Author Quotes Section -->
                    <section id="author-quotes-section">
                        @if ($quotes->count() > 0)
                            <quotes-list book-slug="{{ $quotes->first()->book->slug ?? '' }}"
                                :current-user-id="{{ auth()->id() ?? 'null' }}"
                                :hide-header="true"
                                :quotes="{{ json_encode(
                                    $quotes->map(function ($quote) {
                                            return [
                                                'id' => $quote->id,
                                                'content' => $quote->content,
                                                'book' => [
                                                    'id' => $quote->book->id,
                                                    'title' => $quote->book->title,
                                                    'slug' => $quote->book->slug,
                                                ],
                                                'user' => $quote->user
                                                    ? [
                                                        'id' => $quote->user->id,
                                                        'name' => $quote->user->name,
                                                        'username' => $quote->user->username,
                                                    ]
                                                    : null,
                                                'likes_count' => $quote->likes_count ?? 0,
                                                'created_at' => $quote->created_at,
                                            ];
                                        })->values(),
                                ) }}"></quotes-list>
                        @else
                            <div class="text-center py-12">
                                <div class="max-w-md mx-auto">
                                    <div
                                        class="mx-auto w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-12 h-12 text-slate-400 dark:text-slate-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">
                                        Цитат не знайдено
                                    </h3>
                                    <p class="text-slate-600 dark:text-slate-400 mb-6">
                                        У цього автора поки немає цитат
                                    </p>
                                </div>
                            </div>
                        @endif
                    </section>

                    <!-- Author Facts Section -->
                    <section id="author-facts-section">
                        @if ($facts->count() > 0)
                            <facts-list book-slug="{{ $facts->first()->book->slug ?? '' }}"
                                :current-user-id="{{ auth()->id() ?? 'null' }}"
                                :is-moderator="{{ auth()->check() && auth()->user()->hasRole('admin') ? 'true' : 'false' }}"
                                :hide-header="true"
                                :facts="{{ json_encode(
                                    $facts->map(function ($fact) {
                                            return [
                                                'id' => $fact->id,
                                                'content' => $fact->content,
                                                'book' => [
                                                    'id' => $fact->book->id,
                                                    'title' => $fact->book->title,
                                                    'slug' => $fact->book->slug,
                                                ],
                                                'user' => $fact->user
                                                    ? [
                                                        'id' => $fact->user->id,
                                                        'name' => $fact->user->name,
                                                        'username' => $fact->user->username,
                                                    ]
                                                    : null,
                                                'likes_count' => $fact->likes_count ?? 0,
                                                'is_public' => $fact->is_public,
                                                'created_at' => $fact->created_at,
                                            ];
                                        })->values(),
                                ) }}"></facts-list>
                        @else
                            <div class="text-center py-12">
                                <div class="max-w-md mx-auto">
                                    <div
                                        class="mx-auto w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-12 h-12 text-slate-400 dark:text-slate-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">
                                        Фактів не знайдено
                                    </h3>
                                    <p class="text-slate-600 dark:text-slate-400 mb-6">
                                        У цього автора поки немає фактів
                                    </p>
                                </div>
                            </div>
                        @endif
                    </section>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">

                    <!-- Key Facts -->
                    <div
                        class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-5 sticky top-24 max-h-screen overflow-y-auto">
                        <div class="mb-8">
                            <h3 class="text-s text-gray-700 dark:text-white mb-4">Середня оцінка творів автора</h3>
                            <div class="flex items-center gap-2 mb-4">
                                <i class="fas fa-star text-yellow-400"></i>
                                <span
                                    class="text-2xl font-bold text-gray-900 dark:text-white text-center block rating-display">{{ number_format($stats['average_rating'], 1) }}</span>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-s font-bold text-gray-900 dark:text-white mb-4">Ключові факти</h3>
                            <div class="space-y-4">
                                @if ($author->birth_date)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Родився</span>
                                        <span
                                            class="text-gray-900 dark:text-white">{{ $author->birth_date->format('d.m.Y') }}</span>
                                    </div>
                                @endif

                                @if ($author->nationality)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Країна</span>
                                        <span class="text-gray-900 dark:text-white">{{ $author->nationality }}</span>
                                    </div>
                                @endif

                                <div style="background-color: #ffffff15; height: 1px;"></div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Книг</span>
                                    <span class="text-gray-600 dark:text-white">{{ $stats['total_books'] }}</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Рецензій</span>
                                    <span class="text-gray-600 dark:text-white">{{ $stats['total_reviews'] }}</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Цитат</span>
                                    <span class="text-gray-600 dark:text-white">{{ $stats['total_quotes'] }}</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Фактів</span>
                                    <span class="text-gray-400 dark:text-white">{{ $stats['total_facts'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function checkBiographyHeight() {
            const biographyText = document.getElementById('biography-text');
            const toggleContainer = document.getElementById('biography-toggle-container');
            const gradient = document.getElementById('biography-gradient');

            const maxHeight = 280; // Максимальная высота для короткой версии
            const fullHeight = biographyText.scrollHeight;

            if (fullHeight > maxHeight) {
                // Текст превышает высоту - показываем кнопку
                biographyText.style.maxHeight = maxHeight + 'px';
                toggleContainer.classList.remove('hidden');
                gradient.style.opacity = '1';
                // Позиционируем градиент внизу видимой области
                gradient.style.top = (maxHeight - 80) + 'px'; // 80px - высота градиента
            } else {
                // Текст помещается - скрываем кнопку
                biographyText.style.maxHeight = 'none';
                toggleContainer.classList.add('hidden');
                gradient.style.opacity = '0';
            }
        }

        function toggleBiography() {
            const biographyText = document.getElementById('biography-text');
            const buttonText = document.getElementById('biography-button-text');
            const arrow = document.getElementById('biography-arrow');
            const gradient = document.getElementById('biography-gradient');

            if (biographyText.style.maxHeight === '280px' || biographyText.style.maxHeight === '') {
                // Разворачиваем
                biographyText.style.maxHeight = 'none';
                buttonText.textContent = 'Згорнути';
                arrow.style.transform = 'rotate(180deg)';
                gradient.style.opacity = '0';
            } else {
                // Сворачиваем
                biographyText.style.maxHeight = '280px';
                buttonText.textContent = 'Розгорнути';
                arrow.style.transform = 'rotate(0deg)';
                gradient.style.opacity = '1';
                // Позиционируем градиент внизу видимой области
                gradient.style.top = '200px'; // 280px - 80px (высота градиента)
            }
        }

        // Проверяем высоту при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(checkBiographyHeight, 100);
        });

        // Функция для показа уведомления об оценке
        function showRatingNotification(container, rating) {
            // Создаем временное уведомление
            const notification = document.createElement('div');
            notification.className =
                'absolute top-0 left-0 right-0 bg-green-500 text-white text-center py-1 text-sm font-medium rounded-t-2xl transform -translate-y-full transition-all duration-300';
            notification.textContent = `Оценка ${rating}/10 сохранена!`;

            // Добавляем уведомление к контейнеру
            container.parentElement.parentElement.style.position = 'relative';
            container.parentElement.parentElement.appendChild(notification);

            // Анимация появления
            setTimeout(() => {
                notification.style.transform = 'translateY(0)';
            }, 10);

            // Убираем уведомление через 2 секунды
            setTimeout(() => {
                notification.style.transform = 'translateY(-100%)';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.parentElement.removeChild(notification);
                    }
                }, 300);
            }, 2000);
        }

        // Vue initialization for components
        function initializeVueApp() {
            if (window.Vue) {
                // Initialize Vue for books section
                const booksSection = document.querySelector('#author-books-section');
                if (booksSection && !booksSection.__vue__) {
                    new Vue({
                        el: '#author-books-section'
                    });
                }

                // Initialize Vue for quotes section
                const quotesSection = document.querySelector('#author-quotes-section');
                if (quotesSection && !quotesSection.__vue__) {
                    new Vue({
                        el: '#author-quotes-section'
                    });
                }

                // Initialize Vue for facts section
                const factsSection = document.querySelector('#author-facts-section');
                if (factsSection && !factsSection.__vue__) {
                    new Vue({
                        el: '#author-facts-section'
                    });
                }

                // Initialize Vue for reviews section
                const reviewsSection = document.querySelector('#author-reviews-section');
                if (reviewsSection && !reviewsSection.__vue__) {
                    new Vue({
                        el: '#author-reviews-section'
                    });
                }
            } else {
                // Retry after a short delay
                setTimeout(initializeVueApp, 100);
            }
        }

        // Initialize Vue when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            initializeVueApp();
        });
    </script>
@endpush
