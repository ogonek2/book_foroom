@extends('layouts.app')

@section('title', $book->title . ' - Книжковий форум')

@push('styles')
    <style>
        .price-card-hover:hover {
            background: linear-gradient(to right, #F97316, #EC4899) !important;
        }

        .price-card-hover:hover button {
            background: white !important;
            color: #1f2937 !important;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush

@section('main')
    <div id="app" class="min-h-screen">
        <div class="mx-auto">
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
                        <a href="{{ route('books.index') }}"
                            class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">Книги</a>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-slate-900 dark:text-white font-bold">{{ $book->title }}</span>
                    </li>
                </ol>
            </nav>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3 space-y-8">
                    <!-- Book Details Card -->
                    <div class="overflow-hidden">
                        <div>
                            <div class="flex flex-col md:flex-row gap-8">
                                <div class="flex-shrink-0" style="width: 100%; max-width: 264px;">
                                    <div class="relative group">
                                        <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=450&fit=crop&crop=center' }}"
                                            alt="{{ $book->title }}"
                                            class="object-cover rounded-2xl shadow-2xl group-hover:shadow-3xl transition-all duration-500 group-hover:scale-105">
                                    </div>
                                    <!-- Add Button -->
                                    <div class="mt-4 text-center space-y-3">
                                        <add-to-library-button :book='@json($book)'
                                            :user-libraries='@json($userLibraries)'
                                            :is-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
                                            :initial-status="{{ $currentReadingStatus ? "'{$currentReadingStatus->status}'" : 'null' }}"
                                            @notification="handleNotification"></add-to-library-button>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            @auth
                                                @if ($bookLibraries->count() > 0)
                                                    <div class="mb-4">
                                                        <div class="flex items-center space-x-2 mb-2">
                                                            <i class="fas fa-folder text-orange-500"></i>
                                                            <span
                                                                class="text-sm font-medium text-slate-600 dark:text-slate-400">У
                                                                ваших добірках:</span>
                                                        </div>
                                                        <div class="flex flex-wrap gap-2">
                                                            @foreach ($bookLibraries as $library)
                                                                <span
                                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-800 border border-orange-200 dark:border-orange-700">
                                                                    <i class="fas fa-folder-open mr-1"></i>
                                                                    {{ $library->name }}
                                                                    @if ($library->is_private)
                                                                        <i class="fas fa-lock ml-1 text-xs"></i>
                                                                    @endif
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @endauth
                                            <div class="flex items-center justify-between">
                                                <h1
                                                    class="text-3xl font-black text-slate-900 dark:text-white mb-2 leading-tight">
                                                    {{ $book->title }}</h1>
                                                <span class="text-sm py-2 px-4 dark:bg-gray-800/60 rounded-2xl">
                                                    <i class="fas fa-star text-yellow-400"></i>
                                                    <span
                                                        class="text-slate-400 dark:text-white">{{ $book->display_rating }}</span>
                                                </span>
                                            </div>
                                            <p class="text-lg text-slate-600 dark:text-slate-400 font-bold mb-2">
                                                {{ $book->author }}</p>
                                        </div>
                                    </div>

                                    @if ($book->description)
                                        <div class="mb-8 relative">
                                            <div id="description-text"
                                                class="text-slate-700 dark:text-slate-300 leading-relaxed text-sm font-medium"
                                                style="max-height: 280px; overflow: hidden;">
                                                {{ $book->description }}
                                            </div>
                                            <div id="description-gradient"
                                                class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-slate-50 via-slate-50/80 to-transparent dark:from-slate-900 dark:via-slate-800/80 dark:to-transparent pointer-events-none"
                                                style="top: 200px;"></div>
                                            <div id="description-toggle-container" class="text-left mt-4 hidden">
                                                <button onclick="toggleDescription()"
                                                    class=" text-white rounded-lg font-bold  transition-all duration-300 transform hover:scale-105 flex items-center justify-start">
                                                    <span id="description-toggle-text">Розгорнути</span>
                                                    <svg class="w-5 h-5 ml-2 transition-transform duration-300"
                                                        id="description-arrow" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price Comparison Section -->
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl p-8">
                        <h2 class="text-2xl font-bold text-white mb-6">Порівняйте ціни</h2>

                        <div class="space-y-4">
                            <!-- Yakaboo Entry -->
                            <div
                                class="bg-gray dark:bg-gray-700 rounded-2xl p-4 flex items-center justify-between transition-all duration-300 cursor-pointer hover:shadow-lg price-card-hover">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                                        <span class="text-orange-500 font-bold text-xl">Y</span>
                                    </div>
                                    <div>
                                        <h3 class="text-white font-bold text-lg">Yakaboo</h3>
                                        <p class="text-gray-300 text-sm">Онлайн книжковий магазин</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="text-white font-bold text-xl">250 грн</span>
                                    <button
                                        class="bg-orange-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-orange-600 transition-colors duration-200 flex items-center space-x-2">
                                        <span>На сайт</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Book E Entry -->
                            <div
                                class="bg-gray dark:bg-gray-700 rounded-2xl p-4 flex items-center justify-between transition-all duration-300 cursor-pointer hover:shadow-lg price-card-hover">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                                        <span class="text-red-500 font-bold text-xl">E</span>
                                    </div>
                                    <div>
                                        <h3 class="text-white font-bold text-lg">Book E</h3>
                                        <p class="text-gray-300 text-sm">Електронні книги</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="text-white font-bold text-xl">245 грн</span>
                                    <button
                                        class="bg-orange-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-orange-600 transition-colors duration-200 flex items-center space-x-2">
                                        <span>На сайт</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Буква Entry -->
                            <div
                                class="bg-gray dark:bg-gray-700 rounded-2xl p-4 flex items-center justify-between transition-all duration-300 cursor-pointer hover:shadow-lg price-card-hover">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                                        <span class="text-orange-500 font-bold text-xl">Б</span>
                                    </div>
                                    <div>
                                        <h3 class="text-white font-bold text-lg">Буква</h3>
                                        <p class="text-gray-300 text-sm">Книжковий магазин</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="text-white font-bold text-xl">265 грн</span>
                                    <button
                                        class="bg-orange-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-orange-600 transition-colors duration-200 flex items-center space-x-2">
                                        <span>На сайт</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Show More Button -->
                        <div class="text-center mt-6">
                            <button class="text-white hover:text-gray-300 transition-colors duration-200 font-medium">
                                Показати більше пропозицій
                            </button>
                        </div>
                    </div>

                    <!-- Reviews Section -->
                    <div
                        class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30">
                        <div class="p-8 border-b border-slate-200/30 dark:border-slate-700/30">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-3xl font-black text-slate-900 dark:text-white">Рецензії</h2>
                                    <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">Поділіться своєю думкою
                                        про книгу</p>
                                </div>
                                @auth
                                    @if ($userReview)
                                        <div class="flex space-x-4">
                                            <button onclick="toggleEditReviewForm()" class="text-green-600 dark:text-white"
                                                style="text-decoration: underline;">
                                                Редагувати рецензію
                                            </button>
                                            <button onclick="deleteUserReview()" class="text-red-500 dark:text-purple-300">
                                                <i class="fas fa-trash mr-1"></i>
                                                Видалити рецензію
                                            </button>
                                        </div>
                                    @else
                                        <button onclick="toggleReviewForm()"
                                            class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                                            <svg class="w-6 h-6 mr-3 inline" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Написати рецензію
                                        </button>
                                    @endif
                                @else
                                    <button onclick="toggleReviewForm()"
                                        class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                                        <svg class="w-6 h-6 mr-3 inline" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Написати рецензію (гість)
                                    </button>
                                @endauth
                            </div>
                        </div>

                        <!-- Review Form for All Users -->
                        <div id="reviewForm"
                            class="hidden p-8 border-b border-slate-200/30 dark:border-slate-700/30 bg-slate-50/50 dark:bg-slate-800/50">
                            @auth
                                <!-- Form for authenticated users -->
                                <form action="{{ route('books.reviews.store', $book) }}" method="POST" class="space-y-6">
                                    @csrf
                                    <div>
                                        <label
                                            class="block text-lg font-bold text-slate-700 dark:text-slate-300 mb-4">Оцінка</label>
                                        <div class="flex items-center space-x-1 mb-2" id="reviewFormStars">
                                            @for ($i = 1; $i <= 10; $i++)
                                                <svg class="w-6 h-6 cursor-pointer transition-all duration-200 hover:scale-110 review-form-star {{ $i <= ($userRating ?? 0) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600 hover:text-yellow-300' }}"
                                                    data-rating="{{ $i }}"
                                                    fill="{{ $i <= ($userRating ?? 0) ? 'currentColor' : 'none' }}"
                                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                                </svg>
                                            @endfor
                                            <span class="ml-3 text-sm text-gray-600 dark:text-gray-400 font-medium"
                                                id="reviewRatingText">
                                                @if ($userRating)
                                                    {{ $userRating }}/10
                                                @else
                                                    Оберіть оцінку
                                                @endif
                                            </span>
                                        </div>
                                        <input type="hidden" name="rating" id="ratingInput" value="">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-lg font-bold text-slate-700 dark:text-slate-300 mb-4">Рецензія</label>
                                        <textarea name="content" rows="6"
                                            class="w-full px-6 py-4 border border-slate-300 dark:border-slate-600 rounded-2xl focus:ring-4 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white transition-all duration-300 text-lg font-medium resize-none"
                                            placeholder="Поділіться своїми думками про книгу, персонажів, сюжет..."></textarea>
                                    </div>
                                    <div class="flex justify-end space-x-4">
                                        <button type="button" onclick="toggleReviewForm()"
                                            class="px-8 py-4 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors font-bold text-lg">
                                            Скасувати
                                        </button>
                                        <button type="submit"
                                            class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                                            <svg class="w-6 h-6 mr-3 inline" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v8" />
                                            </svg>
                                            Опублікувати
                                        </button>
                                    </div>
                                </form>
                            @else
                                <!-- Form for guests -->
                                <form action="{{ route('books.reviews.guest-store', $book) }}" method="POST"
                                    class="space-y-6">
                                    @csrf
                                    <div>
                                        <label
                                            class="block text-lg font-bold text-slate-700 dark:text-slate-300 mb-4">Оцінка</label>
                                        <div class="flex items-center space-x-1" id="ratingStarsGuest">
                                            @for ($i = 1; $i <= 10; $i++)
                                                <button type="button"
                                                    class="star-rating w-6 h-6 text-slate-300 dark:text-slate-600 hover:text-yellow-400 transition-all duration-200 hover:scale-110 flex items-center justify-center"
                                                    data-rating="{{ $i }}">
                                                    <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                                    </svg>
                                                </button>
                                            @endfor
                                            <span class="ml-3 text-sm text-gray-600 dark:text-gray-400 font-medium"
                                                id="guestRatingText">
                                                Оберіть оцінку
                                            </span>
                                        </div>
                                        <input type="hidden" name="rating" id="ratingInputGuest" value="">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-lg font-bold text-slate-700 dark:text-slate-300 mb-4">Рецензия</label>
                                        <textarea name="content" rows="6"
                                            class="w-full px-6 py-4 border border-slate-300 dark:border-slate-600 rounded-2xl focus:ring-4 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white transition-all duration-300 text-lg font-medium resize-none"
                                            placeholder="Поделитесь своими мыслями о книге, персонажах, сюжете..."></textarea>
                                    </div>
                                    <div class="flex justify-end space-x-4">
                                        <button type="button" onclick="toggleReviewForm()"
                                            class="px-8 py-4 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors font-bold text-lg">
                                            Отмена
                                        </button>
                                        <button type="submit"
                                            class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                                            <svg class="w-6 h-6 mr-3 inline" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v8" />
                                            </svg>
                                            Опублікувати як гість
                                        </button>
                                    </div>
                                </form>
                            @endauth
                        </div>

                        <!-- Edit Review Form for Authenticated Users -->
                        @auth
                            @if ($userReview)
                                <div id="editReviewForm"
                                    class="hidden p-8 border-t border-slate-200/30 dark:border-slate-700/30">
                                    <form
                                        action="{{ route('books.reviews.update', ['book' => $book, 'review' => $userReview]) }}"
                                        method="POST" class="space-y-6">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label
                                                class="block text-lg font-bold text-slate-700 dark:text-slate-300 mb-4">Рейтинг</label>
                                            <div class="flex space-x-2" id="editRatingStars">
                                                @for ($i = 1; $i <= 10; $i++)
                                                    <button type="button" onclick="setEditRating({{ $i }})"
                                                        class="star-edit text-3xl transition-colors duration-200 {{ $i <= $userReview->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}">
                                                        ★
                                                    </button>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="rating" id="editRatingInput"
                                                value="{{ $userReview->rating }}">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-lg font-bold text-slate-700 dark:text-slate-300 mb-2">Рецензія</label>
                                            <textarea name="content" rows="6"
                                                class="w-full px-6 py-4 border border-slate-300 dark:border-slate-600 rounded-2xl focus:ring-4 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white transition-all duration-300 text-lg font-medium resize-none"
                                                placeholder="Поділіться своїми думками про книгу, персонажів, сюжет...">{{ $userReview->content }}</textarea>
                                        </div>
                                        <div class="flex justify-end space-x-4">
                                            <button type="button" onclick="toggleEditReviewForm()"
                                                class="px-8 py-4 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors text-lg">
                                                Скасувати
                                            </button>
                                            <button type="submit"
                                                class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-8 py-4 rounded-2xl">
                                                Оновити рецензію
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @endauth

                        <!-- Reviews List -->
                        <div class="p-8">
                            @if ($reviews->count() > 0)
                                <div class="space-y-8">
                                    @foreach ($reviews as $review)
                                        @if (is_null($review->parent_id))
                                            @include('books.partials.review', [
                                                'review' => $review,
                                                'book' => $book,
                                            ])
                                        @endif
                                    @endforeach
                                </div>
                                <div class="mt-8">
                                    {{ $reviews->links() }}
                                </div>
                            @else
                                <div class="text-center py-20">
                                    <div
                                        class="w-32 h-32 mx-auto mb-8 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-3xl font-black text-slate-900 dark:text-white mb-4">Поки немає рецензій
                                    </h3>
                                    <p class="text-slate-500 dark:text-slate-400 text-xl font-medium">Станьте першим, хто
                                        поділиться своєю думкою про цю книгу</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div
                        class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-6">
                        <!-- Star Rating -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Оцінки</h3>

                            <!-- Overall Rating Display -->
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-star text-yellow-400"></i>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white average-rating">
                                    {{ number_format($book->rating, 1) }}/10
                                </span>
                            </div>

                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Оцінок:
                                {{ $book->readingStatuses()->whereNotNull('rating')->count() }}</p>

                            @auth
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Ваша оцінка:</p>
                                    <form action="{{ route('books.rating.update', $book->slug) }}" method="POST"
                                        id="ratingForm">
                                        @csrf
                                        <div class="flex items-center space-x-1" id="userRating">
                                            @for ($i = 1; $i <= 10; $i++)
                                                <svg class="w-6 h-6 cursor-pointer star transition-all duration-200 hover:scale-110 {{ $i <= ($userRating ?? 0) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600 hover:text-yellow-300' }}"
                                                    data-rating="{{ $i }}"
                                                    fill="{{ $i <= ($userRating ?? 0) ? 'currentColor' : 'none' }}"
                                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                                </svg>
                                            @endfor
                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400 font-medium"
                                                id="ratingText">
                                                @if ($userRating)
                                                    {{ $userRating }}/10
                                                @endif
                                            </span>
                                        </div>
                                        <input type="hidden" name="rating" id="ratingValue"
                                            value="{{ $userRating ?? 0 }}">
                                    </form>
                                </div>
                            @endauth

                            <!-- Rating Breakdown -->
                            <div class="space-y-1" id="rating-breakdown">
                                @php
                                    $maxCount = max($ratingDistribution);
                                @endphp
                                @for ($rating = 10; $rating >= 1; $rating--)
                                    @php
                                        $count = $ratingDistribution[$rating] ?? 0;
                                        $percentage = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                                    @endphp
                                    <div class="flex items-center space-x-3">
                                        <span class="text-gray-600 dark:text-gray-400 w-8"
                                            style="font-size: 12px;">{{ $rating }}</span>
                                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full"
                                            style="height: 4px;">
                                            <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-full transition-all duration-500 progress-bar"
                                                data-value="{{ $count }}"
                                                style="width: {{ $percentage }}%; height: 4px;">
                                            </div>
                                        </div>
                                        <span class="text-gray-600 dark:text-gray-400 w-8 text-right"
                                            style="font-size: 12px;">{{ $count }}</span>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Characteristics Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Характеристики</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Вид-во</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $book->publisher }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Сторінок</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $book->pages }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Рік видання</span>
                                    <span
                                        class="text-sm text-gray-900 dark:text-white">{{ $book->publication_year }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">ISBN</span>
                                    <span class="text-sm text-gray-900 dark:text-white">978-5-699-93667-0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Section -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Статистика</h3>
                            <div class="space-y-4" id="reading-stats">
                                @php
                                    $totalReading =
                                        $readingStats['read'] +
                                        $readingStats['reading'] +
                                        $readingStats['want_to_read'];
                                    $maxReading = max($readingStats);
                                @endphp
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Прочитано</span>
                                        <span
                                            class="text-sm text-gray-900 dark:text-white">{{ $readingStats['read'] }}</span>
                                    </div>
                                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                        <div class="bg-green-500 h-3 rounded-full transition-all duration-500 progress-bar"
                                            data-value="{{ $readingStats['read'] }}"
                                            style="width: {{ $maxReading > 0 ? ($readingStats['read'] / $maxReading) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Читаю</span>
                                        <span
                                            class="text-sm text-gray-900 dark:text-white">{{ $readingStats['reading'] }}</span>
                                    </div>
                                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                        <div class="bg-blue-500 h-3 rounded-full transition-all duration-500 progress-bar"
                                            data-value="{{ $readingStats['reading'] }}"
                                            style="width: {{ $maxReading > 0 ? ($readingStats['reading'] / $maxReading) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Буду
                                            читати</span>
                                        <span
                                            class="text-sm text-gray-900 dark:text-white">{{ $readingStats['want_to_read'] }}</span>
                                    </div>
                                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                        <div class="bg-yellow-500 h-3 rounded-full transition-all duration-500 progress-bar"
                                            data-value="{{ $readingStats['want_to_read'] }}"
                                            style="width: {{ $maxReading > 0 ? ($readingStats['want_to_read'] / $maxReading) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
        <script>
            // Vue приложение для страницы книги
            document.addEventListener('DOMContentLoaded', function() {
                const bookShowApp = new Vue({
                        el: '#app',
                        data: {
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

            function toggleReviewForm() {
                const form = document.getElementById('reviewForm');
                form.classList.toggle('hidden');

                if (!form.classList.contains('hidden')) {
                    // Инициализируем звезды при открытии формы
                    setTimeout(() => {
                        initializeReviewFormStars();
                    }, 100);
                    
                    form.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }

            // Функция для инициализации звезд в форме рецензии
            function initializeReviewFormStars() {
                // Для авторизованных пользователей
                const reviewStars = document.querySelectorAll('#reviewFormStars .review-form-star');
                if (reviewStars.length > 0) {
                    const currentRating = parseInt(document.getElementById('ratingInput').value) || 0;
                    reviewStars.forEach((s, index) => {
                        if (index < currentRating) {
                            s.classList.add('text-yellow-400');
                            s.classList.remove('text-gray-300', 'dark:text-gray-600');
                            s.setAttribute('fill', 'currentColor');
                        } else {
                            s.classList.remove('text-yellow-400');
                            s.classList.add('text-gray-300', 'dark:text-gray-600');
                            s.setAttribute('fill', 'none');
                        }
                    });
                }

                // Для гостей
                const guestStars = document.querySelectorAll('#ratingStarsGuest .star-rating');
                if (guestStars.length > 0) {
                    const currentRating = parseInt(document.getElementById('ratingInputGuest').value) || 0;
                    guestStars.forEach((s, index) => {
                        if (index < currentRating) {
                            s.classList.add('text-yellow-400');
                            s.classList.remove('text-slate-300', 'dark:text-slate-600');
                        } else {
                            s.classList.remove('text-yellow-400');
                            s.classList.add('text-slate-300', 'dark:text-slate-600');
                        }
                    });
                }
            }

            function toggleEditReviewForm() {
                const form = document.getElementById('editReviewForm');
                form.classList.toggle('hidden');

                if (!form.classList.contains('hidden')) {
                    form.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }

            function setEditRating(rating) {
                document.getElementById('editRatingInput').value = rating;

                const stars = document.querySelectorAll('.star-edit');
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.remove('text-gray-300', 'dark:text-gray-600');
                        star.classList.add('text-yellow-400');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300', 'dark:text-gray-600');
                    }
                });
            }

            function deleteUserReview() {
                if (confirm('Ви впевнені, що хочете видалити свою рецензію? Цю дію неможливо скасувати.')) {
                    const reviewId = {{ $userReview->id ?? 'null' }};
                    if (reviewId) {
                        fetch(`/books/{{ $book->slug }}/reviews/${reviewId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json', // Важно! Это указывает серверу, что мы ожидаем JSON
                                },
                            })
                            .then(response => {
                                // Проверяем, что ответ успешный
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    showNotification('Рецензію успішно видалено!', 'success');
                                    // Перезагружаем страницу после небольшой задержки
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    showNotification('Помилка при видаленні рецензії: ' + data.message, 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showNotification('Помилка при видаленні рецензії', 'error');
                            });
                    }
                }
            }

            // Функция для переключения описания книги
            function toggleDescription() {
                const descriptionText = document.getElementById('description-text');
                const gradient = document.getElementById('description-gradient');
                const toggleContainer = document.getElementById('description-toggle-container');
                const toggleText = document.getElementById('description-toggle-text');
                const arrow = document.getElementById('description-arrow');

                if (descriptionText.style.maxHeight === '280px') {
                    // Разворачиваем
                    descriptionText.style.maxHeight = descriptionText.scrollHeight + 'px';
                    gradient.style.display = 'none';
                    toggleText.textContent = 'Згорнути';
                    arrow.style.transform = 'rotate(180deg)';
                } else {
                    // Сворачиваем
                    descriptionText.style.maxHeight = '280px';
                    gradient.style.display = 'block';
                    gradient.style.top = '200px';
                    toggleText.textContent = 'Розгорнути';
                    arrow.style.transform = 'rotate(0deg)';
                }
            }

            // Функция для проверки высоты описания
            function checkDescriptionHeight() {
                const descriptionText = document.getElementById('description-text');
                const gradient = document.getElementById('description-gradient');
                const toggleContainer = document.getElementById('description-toggle-container');

                if (descriptionText && gradient && toggleContainer) {
                    const maxHeight = 280;
                    const actualHeight = descriptionText.scrollHeight;

                    if (actualHeight > maxHeight) {
                        // Текст превышает максимальную высоту - показываем кнопку и градиент
                        toggleContainer.classList.remove('hidden');
                        gradient.style.display = 'block';
                        gradient.style.top = (maxHeight - 80) + 'px';
                    } else {
                        // Текст помещается - скрываем кнопку и градиент
                        toggleContainer.classList.add('hidden');
                        gradient.style.display = 'none';
                        descriptionText.style.maxHeight = 'none';
                    }
                }
            }

            // Функция для обновления прогресс-баров (можно вызывать извне)
            function updateProgressBars(containerId, values) {
                const container = document.getElementById(containerId);
                if (!container) return;

                const bars = container.querySelectorAll('.progress-bar');
                const maxValue = Math.max(...values);

                bars.forEach((bar, index) => {
                    if (values[index] !== undefined) {
                        const percentage = (values[index] / maxValue) * 100;
                        bar.style.width = percentage + '%';
                        bar.dataset.value = values[index];
                    }
                });
            }

            // Star rating functionality for all users
            document.addEventListener('DOMContentLoaded', function() {
                // Проверяем высоту описания при загрузке страницы
                setTimeout(checkDescriptionHeight, 100);

                // Инициализируем прогресс-бары
                initializeProgressBars();

                // Инициализируем звезды формы рецензии
                initializeReviewFormStars();

                // Инициализируем статус чтения
                @if ($currentReadingStatus)
                    initializeReadingStatus('{{ $currentReadingStatus->status }}');
                @endif

                // Обработчик для закрытия модального окна по клику на фон
                document.getElementById('readingStatusModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeReadingStatusModal();
                    }
                });

                // Обработчик для закрытия модального окна добавления в доборку по клику на фон
                document.getElementById('addToLibraryModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeAddToLibraryModal();
                    }
                });

                // Обработчик для формы создания новой доборки
                document.getElementById('createLibraryForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const data = {
                        name: formData.get('name'),
                        description: formData.get('description'),
                        is_private: formData.get('is_private') === '1',
                        book_id: {{ $book->id }}
                    };

                    fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify(data)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('Добірка створена та книга додана!', 'success');

                                // Добавляем индикатор на страницу книги
                                if (data.library) {
                                    addLibraryIndicator(data.library.id, data.library.name);
                                }

                                closeAddToLibraryModal();
                                // Перезагружаем страницу чтобы обновить список добірок
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                showNotification(data.message ||
                                    'Помилка при створенні добірки');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('Помилка при створенні добірки', 'error');
                        });
                });

                const stars = document.querySelectorAll('.star-rating');
                const ratingInputs = document.querySelectorAll('input[name="rating"]');

                if (stars.length > 0 && ratingInputs.length > 0) {
                    stars.forEach(star => {
                        star.addEventListener('click', function() {
                            const rating = this.dataset.rating;
                            const form = this.closest('form');
                            const formRatingInput = form.querySelector('input[name="rating"]');

                            if (formRatingInput) {
                                formRatingInput.value = rating;
                            }

                            // Update all stars in this form
                            const formStars = form.querySelectorAll('.star-rating');
                            formStars.forEach((s, index) => {
                                if (index < rating) {
                                    s.classList.remove('text-slate-300', 'dark:text-slate-600');
                                    s.classList.add('text-yellow-400');
                                } else {
                                    s.classList.remove('text-yellow-400');
                                    s.classList.add('text-slate-300', 'dark:text-slate-600');
                                }
                            });
                        });

                        star.addEventListener('mouseenter', function() {
                            const rating = this.dataset.rating;
                            const form = this.closest('form');
                            const formStars = form.querySelectorAll('.star-rating');

                            formStars.forEach((s, index) => {
                                if (index < rating) {
                                    s.classList.add('text-yellow-400');
                                } else {
                                    s.classList.remove('text-yellow-400');
                                }
                            });
                        });
                    });

                    // Handle mouse leave for each form
                    document.querySelectorAll('#ratingStars, #ratingStarsGuest').forEach(ratingContainer => {
                        ratingContainer.addEventListener('mouseleave', function() {
                            const form = this.closest('form');
                            const formRatingInput = form.querySelector('input[name="rating"]');
                            const formStars = form.querySelectorAll('.star-rating');

                            if (formRatingInput) {
                                const currentRating = formRatingInput.value;
                                formStars.forEach((s, index) => {
                                    if (index < currentRating) {
                                        s.classList.add('text-yellow-400');
                                    } else {
                                        s.classList.remove('text-yellow-400');
                                        s.classList.add('text-slate-300',
                                            'dark:text-slate-600');
                                    }
                                });
                            }
                        });
                    });
                }
            });

            // Review text expand/collapse functionality
            function toggleReviewText(reviewId) {
                const textElement = document.getElementById(`review-text-${reviewId}`);
                const gradientElement = document.getElementById(`review-gradient-${reviewId}`);
                const toggleContainer = document.getElementById(`review-toggle-container-${reviewId}`);
                const toggleText = document.getElementById(`review-toggle-text-${reviewId}`);
                const arrow = document.getElementById(`review-arrow-${reviewId}`);

                if (textElement.style.maxHeight === '120px' || textElement.style.maxHeight === '') {
                    // Expand
                    textElement.style.maxHeight = textElement.scrollHeight + 'px';
                    gradientElement.style.display = 'none';
                    toggleText.textContent = 'Згорнути';
                    arrow.style.transform = 'rotate(180deg)';
                } else {
                    // Collapse
                    textElement.style.maxHeight = '120px';
                    gradientElement.style.display = 'block';
                    toggleText.textContent = 'Розгорнути';
                    arrow.style.transform = 'rotate(0deg)';
                }
            }

            // Check if review text needs expand button
            function checkReviewTextHeight() {
                document.querySelectorAll('[id^="review-text-"]').forEach(textElement => {
                    const reviewId = textElement.id.replace('review-text-', '');
                    const gradientElement = document.getElementById(`review-gradient-${reviewId}`);
                    const toggleContainer = document.getElementById(`review-toggle-container-${reviewId}`);

                    if (textElement.scrollHeight > 120) {
                        toggleContainer.classList.remove('hidden');
                        gradientElement.style.display = 'block';
                    } else {
                        toggleContainer.classList.add('hidden');
                        gradientElement.style.display = 'none';
                        textElement.style.maxHeight = 'none';
                    }
                });
            }

            // Initialize review text heights on page load
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(checkReviewTextHeight, 100);
            });

            // Инициализируем интерактивные звезды
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(checkReviewTextHeight, 100);
                initializeStarRatings();
            });

            // Добавляем CSS стили для звезд
            const style = document.createElement('style');
            style.textContent = `
                .star-rating-container {
                    display: flex;
                    gap: 4px;
                    align-items: center;
                }
                
                .star-wrapper {
                    position: relative;
                    width: 32px;
                    height: 32px;
                    display: inline-block;
                }
                
                .star-rating-container[data-book-id]:not([data-book-id="book-display-rating"]):not([data-book-id="review-form-rating"]) .star-wrapper {
                    width: 20px;
                    height: 20px;
                }
                
                .star-rating-container[data-book-id="review-form-rating"] .star-wrapper {
                    width: 48px;
                    height: 48px;
                }
                
                .star-background {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 1;
                }
                
                .star-fill {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 0%;
                    height: 100%;
                    z-index: 2;
                    transition: width 0.2s ease;
                    overflow: hidden;
                }
                
                .star-fill svg {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                }
                
                .star-wrapper:hover .star-fill {
                    transition: none;
                }
            `;
            document.head.appendChild(style);

            // Функция для инициализации интерактивных звезд
            function initializeStarRatings() {
                const starContainers = document.querySelectorAll('.star-rating-container');

                starContainers.forEach(container => {
                    const starWrappers = container.querySelectorAll('.star-wrapper');
                    const ratingDisplay = container.parentElement.querySelector('.rating-display');

                    // Инициализируем начальный рейтинг
                    let initialRating;
                    if (container.getAttribute('data-book-id') === 'book-display-rating') {
                        initialRating = {{ $book->display_rating }};
                    } else if (container.getAttribute('data-book-id') === 'review-form-rating') {
                        initialRating = {{ ($userRating ?? 0) * 2 }};
                    } else {
                        initialRating = {{ ($userRating ?? 0) * 2 }};
                    }

                    updateStarRating(starWrappers, initialRating, ratingDisplay, false);

                    starWrappers.forEach((starWrapper, index) => {
                        starWrapper.addEventListener('click', (e) => {
                            const rect = starWrapper.getBoundingClientRect();
                            const clickX = e.clientX - rect.left;
                            const starWidth = rect.width;

                            let rating;
                            if (clickX < starWidth / 2) {
                                // Левая половина - половинка звезды
                                rating = index * 2 + 1;
                            } else {
                                // Правая половина - полная звезда
                                rating = (index + 1) * 2;
                            }

                            updateStarRating(starWrappers, rating, ratingDisplay);

                            // Обновляем скрытое поле в форме рецензии
                            if (container.getAttribute('data-book-id') === 'review-form-rating') {
                                document.getElementById('ratingInput').value = rating / 2;
                            }

                            // Отправляем на сервер для интерактивного рейтинга
                            if (container.getAttribute('data-book-id') !== 'book-display-rating' &&
                                container.getAttribute('data-book-id') !== 'review-form-rating') {
                                updateBookRatingOnServer(rating, container.getAttribute(
                                    'data-book-id'));
                            }
                        });

                        starWrapper.addEventListener('mousemove', (e) => {
                            const rect = starWrapper.getBoundingClientRect();
                            const mouseX = e.clientX - rect.left;
                            const starWidth = rect.width;

                            let hoverRating;
                            if (mouseX < starWidth / 2) {
                                hoverRating = index * 2 + 1;
                            } else {
                                hoverRating = (index + 1) * 2;
                            }

                            highlightStars(starWrappers, hoverRating);
                        });
                    });

                    container.addEventListener('mouseleave', () => {
                        const currentRating = parseFloat(ratingDisplay.textContent.split('/')[0]);
                        updateStarRating(starWrappers, currentRating, ratingDisplay, false);
                    });
                });
            }

            // Функция для обновления визуального состояния звезд
            function updateStarRating(starWrappers, rating, ratingDisplay, animate = true) {
                starWrappers.forEach((wrapper, index) => {
                    const starNumber = index + 1;
                    const starFill = wrapper.querySelector('.star-fill');

                    if (animate) {
                        starFill.style.transition = 'width 0.2s ease';
                    } else {
                        starFill.style.transition = 'none';
                    }

                    // Полная звезда (2, 4, 6, 8, 10 баллов)
                    if (starNumber * 2 <= rating) {
                        starFill.style.width = '100%';
                        starFill.style.clipPath = 'none';
                    }
                    // Половинка звезды (1, 3, 5, 7, 9 баллов) - заполняем половину большой звезды
                    else if (starNumber * 2 - 1 <= rating) {
                        starFill.style.width = '100%';
                        starFill.style.clipPath = 'inset(0 50% 0 0)'; // Обрезаем правую половину
                    }
                    // Пустая звезда (0 баллов)
                    else {
                        starFill.style.width = '0%';
                        starFill.style.clipPath = 'none';
                    }
                });

                // Обновляем отображение рейтинга
                if (ratingDisplay) {
                    ratingDisplay.textContent = rating.toFixed(1) + '/10';

                    // Добавляем небольшую анимацию для отображения рейтинга
                    if (animate) {
                        ratingDisplay.style.transform = 'scale(1.1)';
                        ratingDisplay.style.color = '#fbbf24';
                        setTimeout(() => {
                            ratingDisplay.style.transform = 'scale(1)';
                            ratingDisplay.style.color = '';
                        }, 200);
                    }
                }
            }

            // Функция для подсветки звезд при наведении
            function highlightStars(starWrappers, rating) {
                starWrappers.forEach((wrapper, index) => {
                    const starNumber = index + 1;
                    const starFill = wrapper.querySelector('.star-fill');

                    starFill.style.transition = 'none';

                    // Полная звезда (2, 4, 6, 8, 10 баллов)
                    if (starNumber * 2 <= rating) {
                        starFill.style.width = '100%';
                        starFill.style.clipPath = 'none';
                    }
                    // Половинка звезды (1, 3, 5, 7, 9 баллов) - заполняем половину большой звезды
                    else if (starNumber * 2 - 1 <= rating) {
                        starFill.style.width = '100%';
                        starFill.style.clipPath = 'inset(0 50% 0 0)'; // Обрезаем правую половину
                    }
                    // Пустая звезда (0 баллов)
                    else {
                        starFill.style.width = '0%';
                        starFill.style.clipPath = 'none';
                    }
                });
            }

            // Функция для обновления рейтинга на сервере
            async function updateBookRatingOnServer(rating, bookId) {
                try {
                    const response = await fetch(`/books/${bookId}/rating`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            rating: rating / 2
                        }) // Конвертируем в 5-звездочную систему
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        // Обновляем отображение рейтинга книги
                        const bookRatingDisplay = document.querySelector('[data-book-id="book-display-rating"]')
                            .parentElement.querySelector('.rating-display');
                        if (bookRatingDisplay) {
                            bookRatingDisplay.textContent = data.book_rating + '/10';
                        }

                        // Обновляем звезды отображения
                        const bookDisplayStars = document.querySelector('[data-book-id="book-display-rating"]')
                            .querySelectorAll('.star-wrapper');
                        updateStarRating(bookDisplayStars, data.book_rating, bookRatingDisplay, false);

                        showNotification('Оцінку оновлено!', 'success');
                    } else {
                        showNotification(data.message || 'Помилка при оновленні оцінки', 'error');
                    }
                } catch (error) {
                    console.error('Error updating rating:', error);
                    showNotification('Помилка при оновленні оцінки: ' + error.message, 'error');
                }
            }


            // Notification function
            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
                    type === 'success' ? 'bg-green-500 text-white' : 
                    type === 'error' ? 'bg-red-500 text-white' : 
                    'bg-blue-500 text-white'
                }`;
                notification.textContent = message;

                document.body.appendChild(notification);

                // Animate in
                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                }, 100);

                // Remove after 3 seconds
                setTimeout(() => {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }

            // Like functionality
            function toggleLike(reviewId) {
                @auth
                fetch(`/books/{{ $book->id }}/reviews/${reviewId}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update like count
                            const countElement = document.getElementById(`likes-count-${reviewId}`);
                            if (countElement) {
                                countElement.textContent = data.likes_count;
                            }

                            // Update button state
                            const button = document.querySelector(`[onclick="toggleLike(${reviewId})"]`);
                            if (data.is_liked) {
                                button.classList.add('text-red-500', 'bg-red-50', 'dark:bg-red-900/20');
                                button.classList.remove('text-slate-600', 'dark:text-slate-400', 'hover:text-red-500',
                                    'hover:bg-slate-100', 'dark:hover:bg-slate-700');
                                const svg = button.querySelector('svg');
                                svg.setAttribute('fill', 'currentColor');
                            } else {
                                button.classList.remove('text-red-500', 'bg-red-50', 'dark:bg-red-900/20');
                                button.classList.add('text-slate-600', 'dark:text-slate-400', 'hover:text-red-500',
                                    'hover:bg-slate-100', 'dark:hover:bg-slate-700');
                                const svg = button.querySelector('svg');
                                svg.setAttribute('fill', 'none');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            @else
                alert('Будь ласка, увійдіть в систему, щоб ставити лайки');
            @endauth
            }


            // Обработчик для закрытия модального окна по клику на фон
            document.addEventListener('DOMContentLoaded', function() {
                const addToLibraryModal = document.getElementById('addToLibraryModal');
                if (addToLibraryModal) {
                    addToLibraryModal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeAddToLibraryModal();
                        }
                    });
                }

                // Обновляем action формы при выборе библиотеки
                const librarySelect = document.getElementById('librarySelect');
                const addToLibraryForm = document.getElementById('addToLibraryForm');

                if (librarySelect && addToLibraryForm) {
                    librarySelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        if (selectedOption.value) {
                            const url = selectedOption.getAttribute('data-url');
                            addToLibraryForm.action = url;
                        } else {
                            addToLibraryForm.action = '';
                        }
                    });
                }
            });

            // Система рейтингов
            document.addEventListener('DOMContentLoaded', function() {
                // Звезды для оценки книги в сайдбаре
                const userRatingStars = document.querySelectorAll('#userRating .star');

                // Удаляем старые обработчики, если они есть
                userRatingStars.forEach(star => {
                    star.removeEventListener('click', star.clickHandler);
                });

                userRatingStars.forEach(star => {
                    const clickHandler = function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const rating = parseInt(this.getAttribute('data-rating'));
                        document.getElementById('ratingValue').value = rating;

                        // Обновляем визуальное отображение звезд
                        userRatingStars.forEach((s, index) => {
                            if (index < rating) {
                                s.classList.remove('text-gray-300', 'dark:text-gray-600',
                                    'hover:text-yellow-300');
                                s.classList.add('text-yellow-400');
                                s.setAttribute('fill', 'currentColor');
                            } else {
                                s.classList.remove('text-yellow-400');
                                s.classList.add('text-gray-300', 'dark:text-gray-600',
                                    'hover:text-yellow-300');
                                s.setAttribute('fill', 'none');
                            }
                        });

                        // Обновляем текст рейтинга
                        const ratingText = document.getElementById('ratingText');
                        if (ratingText) {
                            ratingText.textContent = `${rating}/10`;
                        }

                        // Отправляем AJAX запрос
                        updateUserRating(rating);
                    };

                    star.clickHandler = clickHandler;
                    star.addEventListener('click', clickHandler);
                });

                // Звезды в форме рецензии для авторизованных пользователей
                const reviewStars = document.querySelectorAll('#reviewFormStars .review-form-star');
                reviewStars.forEach(star => {
                    star.addEventListener('click', function() {
                        const rating = parseInt(this.getAttribute('data-rating'));

                        // Обновляем все звезды в форме
                        reviewStars.forEach((s, index) => {
                            if (index < rating) {
                                s.classList.remove('text-gray-300', 'dark:text-gray-600',
                                    'hover:text-yellow-300');
                                s.classList.add('text-yellow-400');
                                s.setAttribute('fill', 'currentColor');
                            } else {
                                s.classList.remove('text-yellow-400');
                                s.classList.add('text-gray-300', 'dark:text-gray-600',
                                    'hover:text-yellow-300');
                                s.setAttribute('fill', 'none');
                            }
                        });

                        // Обновляем скрытое поле
                        document.getElementById('ratingInput').value = rating;

                        // Обновляем текст рейтинга
                        const reviewRatingText = document.getElementById('reviewRatingText');
                        if (reviewRatingText) {
                            reviewRatingText.textContent = `${rating}/10`;
                        }
                    });

                    // Добавляем hover эффект
                    star.addEventListener('mouseenter', function() {
                        const rating = parseInt(this.getAttribute('data-rating'));
                        reviewStars.forEach((s, index) => {
                            if (index < rating) {
                                s.classList.add('text-yellow-400');
                                s.classList.remove('text-gray-300', 'dark:text-gray-600');
                            }
                        });
                    });
                });

                // Сбрасываем hover эффект при уходе мыши
                document.getElementById('reviewFormStars').addEventListener('mouseleave', function() {
                    const currentRating = parseInt(document.getElementById('ratingInput').value) || 0;
                    reviewStars.forEach((s, index) => {
                        if (index < currentRating) {
                            s.classList.add('text-yellow-400');
                            s.classList.remove('text-gray-300', 'dark:text-gray-600');
                            s.setAttribute('fill', 'currentColor');
                        } else {
                            s.classList.remove('text-yellow-400');
                            s.classList.add('text-gray-300', 'dark:text-gray-600');
                            s.setAttribute('fill', 'none');
                        }
                    });
                });

                // Проверка формы рецензии при отправке
                const reviewForm = document.querySelector('form[action*="reviews.store"]');
                if (reviewForm) {
                    reviewForm.addEventListener('submit', function(e) {
                        const ratingInput = document.getElementById('ratingInput');
                        const contentInput = this.querySelector('textarea[name="content"]');

                        // Проверяем, есть ли контент
                        if (!contentInput.value.trim()) {
                            e.preventDefault();
                            showNotification('Будь ласка, напишіть рецензію', 'error');
                            return;
                        }

                        // Проверяем, есть ли рейтинг (либо в форме, либо в BookReadingStatus)
                        if (!ratingInput.value) {
                            e.preventDefault();
                            showNotification('Спочатку поставте оцінку книзі, а потім напишіть рецензію',
                                'error');
                            return;
                        }
                    });
                }

                // Звезды в форме рецензии для гостей
                const guestStars = document.querySelectorAll('#ratingStarsGuest .star-rating');
                guestStars.forEach(star => {
                    star.addEventListener('click', function() {
                        const rating = parseInt(this.getAttribute('data-rating'));

                        // Обновляем все звезды в форме
                        guestStars.forEach((s, index) => {
                            if (index < rating) {
                                s.classList.remove('text-slate-300', 'dark:text-slate-600');
                                s.classList.add('text-yellow-400');
                            } else {
                                s.classList.remove('text-yellow-400');
                                s.classList.add('text-slate-300', 'dark:text-slate-600');
                            }
                        });

                        // Обновляем скрытое поле
                        document.getElementById('ratingInputGuest').value = rating;

                        // Обновляем текст рейтинга
                        const guestRatingText = document.getElementById('guestRatingText');
                        if (guestRatingText) {
                            guestRatingText.textContent = `${rating}/10`;
                        }
                    });

                    // Добавляем hover эффект для гостей
                    star.addEventListener('mouseenter', function() {
                        const rating = parseInt(this.getAttribute('data-rating'));
                        guestStars.forEach((s, index) => {
                            if (index < rating) {
                                s.classList.add('text-yellow-400');
                                s.classList.remove('text-slate-300', 'dark:text-slate-600');
                            }
                        });
                    });
                });

                // Сбрасываем hover эффект при уходе мыши для гостей
                document.getElementById('ratingStarsGuest').addEventListener('mouseleave', function() {
                    const currentRating = parseInt(document.getElementById('ratingInputGuest').value) || 0;
                    guestStars.forEach((s, index) => {
                        if (index < currentRating) {
                            s.classList.add('text-yellow-400');
                            s.classList.remove('text-slate-300', 'dark:text-slate-600');
                        } else {
                            s.classList.remove('text-yellow-400');
                            s.classList.add('text-slate-300', 'dark:text-slate-600');
                        }
                    });
                });
            });

            // Обновление рейтинга пользователя
            async function updateUserRating(rating) {
                try {
                    const formData = new FormData();
                    formData.append('rating', rating);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                    const response = await fetch(`{{ route('books.rating.update', $book->slug) }}`, {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Обновляем средний рейтинг книги
                        const averageRatingElement = document.querySelector('.average-rating');
                        if (averageRatingElement && data.average_rating) {
                            averageRatingElement.textContent = parseFloat(data.average_rating).toFixed(1) + '/10';
                        }

                        // Обновляем звезды в сайдбаре
                        updateStarsDisplay('#userRating .star', rating);

                        // Обновляем текст рейтинга в сайдбаре
                        const ratingText = document.getElementById('ratingText');
                        if (ratingText) {
                            ratingText.textContent = `${rating}/10`;
                        }

                        // Обновляем скрытое поле в форме
                        const ratingInput = document.getElementById('ratingInput');
                        if (ratingInput) {
                            ratingInput.value = rating;
                        }

                        // Показываем уведомление
                        showNotification('Оцінку оновлено!', 'success');
                    } else {
                        showNotification('Помилка при оновленні оцінки', 'error');
                    }
                } catch (error) {
                    console.error('Error updating rating:', error);
                    showNotification('Помилка при оновленні оцінки', 'error');
                }
            }

            // Обновление отображения звезд
            function updateStarsDisplay(selector, rating) {
                const stars = document.querySelectorAll(selector);
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.remove('text-gray-300', 'dark:text-gray-600', 'hover:text-yellow-300');
                        star.classList.add('text-yellow-400');
                        star.setAttribute('fill', 'currentColor');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300', 'dark:text-gray-600', 'hover:text-yellow-300');
                        star.setAttribute('fill', 'none');
                    }
                });
            }
        </script>
    @endpush
    
@endsection
