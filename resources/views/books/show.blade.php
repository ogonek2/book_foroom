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
    <div class="min-h-screen">
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
                                        @if ($currentReadingStatus)
                                            @php
                                                $statusTexts = [
                                                    'read' => 'Прочитано',
                                                    'reading' => 'Читаю',
                                                    'want_to_read' => 'Буду читати',
                                                ];
                                                $statusColors = [
                                                    'read' =>
                                                        'from-green-500 to-green-600 hover:from-green-600 hover:to-green-700',
                                                    'reading' =>
                                                        'from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700',
                                                    'want_to_read' =>
                                                        'from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700',
                                                ];
                                            @endphp
                                            <button onclick="openReadingStatusModal()"
                                                class="w-full bg-gradient-to-r {{ $statusColors[$currentReadingStatus->status] }} text-white px-8 py-3 rounded-xl font-bold transition-all duration-300 transform shadow-lg hover:shadow-xl flex items-center justify-center">
                                                {{ $statusTexts[$currentReadingStatus->status] }}
                                            </button>
                                        @else
                                            <button onclick="openReadingStatusModal()"
                                                class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-3 rounded-xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform shadow-lg hover:shadow-xl">
                                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Додати
                                            </button>
                                        @endif

                                        @auth
                                            <button onclick="openAddToLibraryModal()"
                                                class="w-full bg-gradient-to-r from-orange-500 to-pink-500 text-white px-8 py-3 rounded-xl font-bold hover:from-orange-600 hover:to-pink-600 transition-all duration-300 transform shadow-lg hover:shadow-xl">
                                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                                Додати до добірки
                                            </button>
                                        @endauth
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
                                                    <span class="text-slate-400 dark:text-white">{{ $book->display_rating }}</span>
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
                                    @if($userReview)
                                        <div class="flex space-x-4">
                                            <button onclick="toggleEditReviewForm()"
                                                class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-8 py-4 rounded-2xl font-bold hover:from-green-600 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                                                <svg class="w-6 h-6 mr-3 inline" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Редагувати рецензію
                                            </button>
                                            <button onclick="deleteUserReview()"
                                                class="bg-gradient-to-r from-red-500 to-pink-600 text-white px-8 py-4 rounded-2xl font-bold hover:from-red-600 hover:to-pink-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                                                <svg class="w-6 h-6 mr-3 inline" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
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
                                        <div class="flex items-center space-x-1 mb-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-8 h-8 cursor-pointer" data-rating="{{ $i }}" 
                                                    fill="none" 
                                                    stroke="currentColor" 
                                                    stroke-width="1"
                                                    class="text-gray-300 dark:text-gray-600"
                                                    viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @endfor
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
                                        <div class="flex items-center space-x-3" id="ratingStarsGuest">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <button type="button"
                                                    class="star-rating w-12 h-12 text-slate-300 dark:text-slate-600 hover:text-yellow-400 transition-all duration-300 hover:scale-125"
                                                    data-rating="{{ $i }}">
                                                    <svg class="w-full h-full" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </button>
                                            @endfor
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
                            @if($userReview)
                                <div id="editReviewForm" class="hidden p-8 border-t border-slate-200/30 dark:border-slate-700/30">
                                    <form action="{{ route('books.reviews.update', ['book' => $book, 'review' => $userReview]) }}" method="POST" class="space-y-6">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-lg font-bold text-slate-700 dark:text-slate-300 mb-4">Рейтинг</label>
                                            <div class="flex space-x-2" id="editRatingStars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <button type="button" onclick="setEditRating({{ $i }})" 
                                                        class="star-edit text-3xl transition-colors duration-200 {{ $i <= $userReview->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}">
                                                        ★
                                                    </button>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="rating" id="editRatingInput" value="{{ $userReview->rating }}">
                                        </div>
                                        <div>
                                            <label class="block text-lg font-bold text-slate-700 dark:text-slate-300 mb-4">Рецензія</label>
                                            <textarea name="content" rows="6"
                                                class="w-full px-6 py-4 border border-slate-300 dark:border-slate-600 rounded-2xl focus:ring-4 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white transition-all duration-300 text-lg font-medium resize-none"
                                                placeholder="Поділіться своїми думками про книгу, персонажів, сюжет...">{{ $userReview->content }}</textarea>
                                        </div>
                                        <div class="flex justify-end space-x-4">
                                            <button type="button" onclick="toggleEditReviewForm()"
                                                class="px-8 py-4 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors font-bold text-lg">
                                                Скасувати
                                            </button>
                                            <button type="submit"
                                                class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-8 py-4 rounded-2xl font-bold hover:from-green-600 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                                                <svg class="w-6 h-6 mr-3 inline" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
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
                                    @guest
                                        <div class="mt-8">
                                            <button onclick="toggleReviewForm()"
                                                class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                                                Написати рецензію як гість
                                            </button>
                                        </div>
                                    @endguest
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div
                        class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-6 sticky top-24 max-h-screen overflow-y-auto">
                        <!-- Star Rating -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Оцінки</h3>
                            
                            <!-- Overall Rating Display -->
                            <div class="flex items-center space-x-2 mb-4">
                                <div class="flex items-center space-x-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $book->star_rating)
                                            <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $book->display_rating }}/10</span>
                            </div>
                            
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">{{ $book->reviews_count }} {{ Str::plural('оцінка', $book->reviews_count) }}</p>
                            
        @auth
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Ваша оцінка:</p>
                <form action="{{ route('books.rating.update', $book->id) }}" method="POST" id="ratingForm">
                    @csrf
                    <div class="flex items-center space-x-1" id="userRating">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-6 h-6 cursor-pointer star" data-rating="{{ $i }}" 
                                fill="{{ $i <= ($userRating ?? 0) ? 'currentColor' : 'none' }}" 
                                stroke="currentColor" 
                                stroke-width="1"
                                class="{{ $i <= ($userRating ?? 0) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="ratingValue" value="{{ $userRating ?? 0 }}">
                </form>
            </div>
        @endauth

                            <!-- Rating Breakdown -->
                            <div class="space-y-3" id="rating-breakdown">
                                @php
                                    $maxCount = max($ratingDistribution);
                                @endphp
                                @for ($rating = 5; $rating >= 1; $rating--)
                                    @php
                                        $count = $ratingDistribution[$rating] ?? 0;
                                        $percentage = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                                    @endphp
                                    <div class="flex items-center space-x-3">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 w-8">{{ $rating * 2 }}</span>
                                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                            <div class="bg-orange-500 h-3 rounded-full transition-all duration-500 progress-bar"
                                                data-value="{{ $count }}" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400 w-8 text-right">{{ $count }}</span>
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
                                    <span class="text-sm text-gray-900 dark:text-white">Альпіна паблішінг</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Сторінок</span>
                                    <span class="text-sm text-gray-900 dark:text-white">208</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Рік видання</span>
                                    <span class="text-sm text-gray-900 dark:text-white">2005</span>
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
                                    $totalReading = $readingStats['read'] + $readingStats['reading'] + $readingStats['want_to_read'];
                                    $maxReading = max($readingStats);
                                @endphp
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Прочитано</span>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $readingStats['read'] }}</span>
                                    </div>
                                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                        <div class="bg-green-500 h-3 rounded-full transition-all duration-500 progress-bar"
                                            data-value="{{ $readingStats['read'] }}" 
                                            style="width: {{ $maxReading > 0 ? ($readingStats['read'] / $maxReading) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Читаю</span>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $readingStats['reading'] }}</span>
                                    </div>
                                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                        <div class="bg-blue-500 h-3 rounded-full transition-all duration-500 progress-bar"
                                            data-value="{{ $readingStats['reading'] }}" 
                                            style="width: {{ $maxReading > 0 ? ($readingStats['reading'] / $maxReading) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Буду читати</span>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $readingStats['want_to_read'] }}</span>
                                    </div>
                                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                        <div class="bg-yellow-500 h-3 rounded-full transition-all duration-500 progress-bar"
                                            data-value="{{ $readingStats['want_to_read'] }}" 
                                            style="width: {{ $maxReading > 0 ? ($readingStats['want_to_read'] / $maxReading) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reading Status Modal -->
    <div id="readingStatusModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-gray dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0"
            id="modalContent">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Додати до списку</h3>
                    <button onclick="closeReadingStatusModal()"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <button onclick="selectReadingStatus('read')"
                        class="w-full text-left p-4 rounded-xl border-2 border-transparent hover:border-indigo-500 dark:hover:border-indigo-400 transition-all duration-300 bg-slate-50 dark:bg-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 group">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mr-4 group-hover:bg-green-200 dark:group-hover:bg-green-800/40 transition-colors">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4
                                    class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    Прочитано</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Книга прочитана повністю</p>
                            </div>
                        </div>
                    </button>

                    <button onclick="selectReadingStatus('reading')"
                        class="w-full text-left p-4 rounded-xl border-2 border-transparent hover:border-indigo-500 dark:hover:border-indigo-400 transition-all duration-300 bg-slate-50 dark:bg-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 group">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mr-4 group-hover:bg-blue-200 dark:group-hover:bg-blue-800/40 transition-colors">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <h4
                                    class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    Читаю</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Зараз читаю цю книгу</p>
                            </div>
                        </div>
                    </button>

                    <button onclick="selectReadingStatus('want-to-read')"
                        class="w-full text-left p-4 rounded-xl border-2 border-transparent hover:border-indigo-500 dark:hover:border-indigo-400 transition-all duration-300 bg-slate-50 dark:bg-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 group">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mr-4 group-hover:bg-purple-200 dark:group-hover:bg-purple-800/40 transition-colors">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4
                                    class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    Буду читати</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Планую прочитати цю книгу</p>
                            </div>
                        </div>
                    </button>

                    <!-- Divider -->
                    <div class="border-t border-slate-200 dark:border-slate-600 my-4"></div>

                    <!-- Add to Library Button -->
                    <button onclick="openAddToLibraryModal()"
                        class="w-full text-left p-4 rounded-xl border-2 border-transparent hover:border-orange-500 dark:hover:border-orange-400 transition-all duration-300 bg-slate-50 dark:bg-slate-700 hover:bg-orange-50 dark:hover:bg-orange-900/20 group">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center mr-4 group-hover:bg-orange-200 dark:group-hover:bg-orange-800/40 transition-colors">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div>
                                <h4
                                    class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                    Додати до добірки</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Створити або додати до існуючої
                                    добірки</p>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add to Library Modal -->
    <div id="addToLibraryModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-gray dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0"
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

                <!-- Tabs -->
                <div class="flex space-x-1 mb-6 bg-slate-100 dark:bg-slate-700 rounded-lg p-1">
                    <button onclick="switchLibraryTab('existing')" id="existingTab"
                        class="flex-1 py-2 px-4 text-sm font-medium rounded-md transition-all duration-200 bg-white dark:bg-slate-600 text-slate-900 dark:text-white shadow-sm">
                        Існуючі добірки
                    </button>
                    <button onclick="switchLibraryTab('create')" id="createTab"
                        class="flex-1 py-2 px-4 text-sm font-medium rounded-md transition-all duration-200 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                        Створити нову
                    </button>
                </div>

                <!-- Existing Libraries Tab -->
                <div id="existingLibrariesTab" class="tab-content">
                    @php
                        try {
                            $userLibraries = auth()->user()->libraries()->orderBy('name')->get();
                        } catch (\Exception $e) {
                            $userLibraries = collect();
                        }
                    @endphp

                    @if ($userLibraries->count() > 0)
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            @foreach ($userLibraries as $library)
                                <div class="flex items-center justify-between p-3 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                                    data-library-id="{{ $library->id }}">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-slate-900 dark:text-white">{{ $library->name }}</h4>
                                        @if ($library->description)
                                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                                                {{ Str::limit($library->description, 50) }}</p>
                                        @endif
                                        <div class="flex items-center space-x-2 mt-2">
                                            <span class="text-xs text-slate-500 dark:text-slate-400 library-count">
                                                {{ $library->books_count }}
                                                {{ Str::plural('книга', $library->books_count) }}
                                            </span>
                                            @if ($library->is_private)
                                                <span
                                                    class="text-xs bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2 py-1 rounded-full">
                                                    <i class="fas fa-lock mr-1"></i>Приватна
                                                </span>
                                            @else
                                                <span
                                                    class="text-xs bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-2 py-1 rounded-full">
                                                    <i class="fas fa-globe mr-1"></i>Публічна
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <button onclick="addBookToLibrary({{ $library->id }})"
                                        class="ml-3 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Додати
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-slate-400 dark:text-slate-500 text-4xl mb-3">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <p class="text-slate-600 dark:text-slate-400 mb-4">У вас поки немає добірок</p>
                            <button onclick="switchLibraryTab('create')"
                                class="text-orange-500 hover:text-orange-600 font-medium">
                                Створити першу добірку
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Create New Library Tab -->
                <div id="createLibraryTab" class="tab-content hidden">
                    <form id="createLibraryForm" action="{{ route('libraries.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Назва
                                    добірки</label>
                                <input type="text" name="name" required
                                    class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white transition-all duration-200"
                                    placeholder="Наприклад: Моя улюблена фантастика">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Опис
                                    (необов'язково)</label>
                                <textarea name="description" rows="3"
                                    class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white transition-all duration-200 resize-none"
                                    placeholder="Короткий опис вашої добірки"></textarea>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_private" value="1" id="isPrivate"
                                    class="w-4 h-4 text-orange-600 bg-slate-100 border-slate-300 rounded focus:ring-orange-500 dark:focus:ring-orange-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600">
                                <label for="isPrivate" class="ml-2 text-sm text-slate-700 dark:text-slate-300">Приватна
                                    добірка</label>
                            </div>

                            <div class="flex space-x-3 pt-4">
                                <button type="button" onclick="closeAddToLibraryModal()"
                                    class="flex-1 px-4 py-3 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium transition-colors">
                                    Скасувати
                                </button>
                                <button type="submit"
                                    class="flex-1 bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-lg font-medium transition-colors">
                                    Створити та додати
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleReviewForm() {
                const form = document.getElementById('reviewForm');
                form.classList.toggle('hidden');

                if (!form.classList.contains('hidden')) {
                    form.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
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
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Помилка при видаленні рецензії: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Помилка при видаленні рецензії');
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

            // Функции для модального окна статуса чтения
            function openReadingStatusModal() {
                const modal = document.getElementById('readingStatusModal');
                const modalContent = document.getElementById('modalContent');

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                // Анимация появления
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            function closeReadingStatusModal() {
                const modal = document.getElementById('readingStatusModal');
                const modalContent = document.getElementById('modalContent');

                // Анимация исчезновения
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 300);
            }

            async function selectReadingStatus(status) {
                try {
                    // Отправляем запрос на сервер
                    const response = await fetch(`/api/reading-status/book/{{ $book->id }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            status: status === 'want-to-read' ? 'want_to_read' : status
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Показываем уведомление об успехе
                        showStatusNotification(status, 'success');

                        // Обновляем отображение статуса на странице
                        updateStatusDisplay(status);
                    } else {
                        // Показываем уведомление об ошибке
                        showStatusNotification(status, 'error', data.message || 'Ошибка при сохранении статуса');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showStatusNotification(status, 'error', 'Ошибка при сохранении статуса');
                }

                // Закрываем модальное окно
                closeReadingStatusModal();
            }

            // Функции для модального окна добавления в доборку
            function openAddToLibraryModal() {
                const modal = document.getElementById('addToLibraryModal');
                const modalContent = document.getElementById('addToLibraryModalContent');

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                // Анимация появления
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);

                // Закрываем предыдущий модал
                closeReadingStatusModal();
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

            function switchLibraryTab(tab) {
                const existingTab = document.getElementById('existingTab');
                const createTab = document.getElementById('createTab');
                const existingContent = document.getElementById('existingLibrariesTab');
                const createContent = document.getElementById('createLibraryTab');

                if (tab === 'existing') {
                    existingTab.classList.add('bg-white', 'dark:bg-slate-600', 'text-slate-900', 'dark:text-white',
                    'shadow-sm');
                    existingTab.classList.remove('text-slate-600', 'dark:text-slate-400');
                    createTab.classList.remove('bg-white', 'dark:bg-slate-600', 'text-slate-900', 'dark:text-white',
                        'shadow-sm');
                    createTab.classList.add('text-slate-600', 'dark:text-slate-400');
                    existingContent.classList.remove('hidden');
                    createContent.classList.add('hidden');
                } else {
                    createTab.classList.add('bg-white', 'dark:bg-slate-600', 'text-slate-900', 'dark:text-white', 'shadow-sm');
                    createTab.classList.remove('text-slate-600', 'dark:text-slate-400');
                    existingTab.classList.remove('bg-white', 'dark:bg-slate-600', 'text-slate-900', 'dark:text-white',
                        'shadow-sm');
                    existingTab.classList.add('text-slate-600', 'dark:text-slate-400');
                    createContent.classList.remove('hidden');
                    existingContent.classList.add('hidden');
                }
            }

            async function addBookToLibrary(libraryId) {
                try {
                    console.log('Adding book to library:', libraryId, 'Book ID:', {{ $book->id }});

                    const response = await fetch(`/libraries/${libraryId}/add-book`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            book_id: {{ $book->id }}
                        })
                    });

                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);

                    // Проверяем, что ответ действительно JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        console.error('Non-JSON response:', text);
                        showLibraryNotification('error', 'Сервер повернув некоректну відповідь');
                        return;
                    }

                    const data = await response.json();

                    if (data.success) {
                        showLibraryNotification('success', 'Книга успішно додана до добірки!');

                        // Обновляем количество книг в поп-апе
                        updateLibraryCount(libraryId);

                        // Добавляем индикатор на страницу книги
                        addLibraryIndicator(libraryId, data.library_name);

                        closeAddToLibraryModal();
                    } else {
                        showLibraryNotification('error', data.message || 'Помилка при додаванні книги');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showLibraryNotification('error', 'Помилка при додаванні книги');
                }
            }

            function updateLibraryCount(libraryId) {
                // Находим элемент с количеством книг в поп-апе
                const libraryElement = document.querySelector(`[data-library-id="${libraryId}"]`);
                if (libraryElement) {
                    const countElement = libraryElement.querySelector('.library-count');
                    if (countElement) {
                        const currentCount = parseInt(countElement.textContent) || 0;
                        countElement.textContent = currentCount + 1;
                    }
                }
            }

            function addLibraryIndicator(libraryId, libraryName) {
                // Проверяем, есть ли уже индикаторы на странице
                let indicatorsContainer = document.querySelector('.library-indicators');

                if (indicatorsContainer) {
                    const badgesContainer = indicatorsContainer.querySelector('#library-badges');
                    if (badgesContainer) {
                        // Проверяем, нет ли уже такого бейджа
                        const existingBadge = badgesContainer.querySelector(`[data-library-id="${libraryId}"]`);
                        if (!existingBadge) {
                            const badge = document.createElement('span');
                            badge.className =
                                'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 border border-orange-200 dark:border-orange-700';
                            badge.setAttribute('data-library-id', libraryId);
                            badge.innerHTML = `
                    <i class="fas fa-folder-open mr-1"></i>
                    ${libraryName}
                `;
                            badgesContainer.appendChild(badge);
                        }
                    }
                }
            }

            function showLibraryNotification(type, message) {
                const isError = type === 'error';
                const bgColor = isError ? 'bg-red-500' : 'bg-green-500';
                const icon = isError ? 'M6 18L18 6M6 6l12 12' : 'M5 13l4 4L19 7';

                // Создаем уведомление
                const notification = document.createElement('div');
                notification.className =
                    `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-xl shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
                notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${icon}"/>
            </svg>
            <span>${message}</span>
        </div>
    `;

                document.body.appendChild(notification);

                // Анимация появления
                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                }, 100);

                // Удаляем уведомление через 3 секунды
                setTimeout(() => {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }

            function showStatusNotification(status, type = 'success', message = null) {
                const statusTexts = {
                    'read': 'Прочитано',
                    'reading': 'Читаю',
                    'want-to-read': 'Буду читати'
                };

                const isError = type === 'error';
                const bgColor = isError ? 'bg-red-500' : 'bg-green-500';
                const icon = isError ? 'M6 18L18 6M6 6l12 12' : 'M5 13l4 4L19 7';

                // Создаем уведомление
                const notification = document.createElement('div');
                notification.className =
                    `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-xl shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
                notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${icon}"/>
            </svg>
            ${message || `Книга додана до списку "${statusTexts[status]}"`}
        </div>
    `;

                document.body.appendChild(notification);

                // Анимация появления
                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                }, 10);

                // Удаляем через 3 секунды
                setTimeout(() => {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => {
                        if (document.body.contains(notification)) {
                            document.body.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            }

            function updateStatusDisplay(status) {
                // Обновляем кнопку добавления в библиотеку
                const addButton = document.querySelector('[onclick="openReadingStatusModal()"]');
                if (addButton) {
                    const statusTexts = {
                        'read': 'Прочитано',
                        'reading': 'Читаю',
                        'want-to-read': 'Буду читати',
                        'want_to_read': 'Буду читати'
                    };

                    const statusColors = {
                        'read': 'from-green-500 to-green-600 hover:from-green-600 hover:to-green-700',
                        'reading': 'from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700',
                        'want-to-read': 'from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700',
                        'want_to_read': 'from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700'
                    };

                    addButton.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            ${statusTexts[status]}
        `;

                    // Удаляем все цветовые классы
                    addButton.classList.remove('from-indigo-500', 'to-purple-600', 'hover:from-indigo-600',
                        'hover:to-purple-700');
                    addButton.classList.remove('from-green-500', 'to-green-600', 'hover:from-green-600', 'hover:to-green-700');
                    addButton.classList.remove('from-blue-500', 'to-blue-600', 'hover:from-blue-600', 'hover:to-blue-700');
                    addButton.classList.remove('from-yellow-500', 'to-yellow-600', 'hover:from-yellow-600',
                        'hover:to-yellow-700');

                    // Добавляем новые цветовые классы
                    const colorClasses = statusColors[status].split(' ');
                    addButton.classList.add(...colorClasses);
                }
            }

            function initializeReadingStatus(status) {
                // Инициализируем отображение статуса при загрузке страницы
                updateStatusDisplay(status);
            }

            // Функция для инициализации прогресс-баров
            function initializeProgressBars() {
                // Обрабатываем рейтинги
                const ratingBars = document.querySelectorAll('#rating-breakdown .progress-bar');
                if (ratingBars.length > 0) {
                    const ratingValues = Array.from(ratingBars).map(bar => parseInt(bar.dataset.value));
                    const maxRating = Math.max(...ratingValues);

                    ratingBars.forEach(bar => {
                        const value = parseInt(bar.dataset.value);
                        const percentage = (value / maxRating) * 100;
                        bar.style.width = percentage + '%';
                    });
                }

                // Обрабатываем статистику чтения
                const readingBars = document.querySelectorAll('#reading-stats .progress-bar');
                if (readingBars.length > 0) {
                    const readingValues = Array.from(readingBars).map(bar => parseInt(bar.dataset.value));
                    const maxReading = Math.max(...readingValues);

                    readingBars.forEach(bar => {
                        const value = parseInt(bar.dataset.value);
                        const percentage = (value / maxReading) * 100;
                        bar.style.width = percentage + '%';
                    });
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
                                showLibraryNotification('success', 'Добірка створена та книга додана!');

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
                                showLibraryNotification('error', data.message ||
                                    'Помилка при створенні добірки');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showLibraryNotification('error', 'Помилка при створенні добірки');
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
                                updateBookRatingOnServer(rating, container.getAttribute('data-book-id'));
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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ rating: rating / 2 }) // Конвертируем в 5-звездочную систему
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Обновляем отображение рейтинга книги
                        const bookRatingDisplay = document.querySelector('[data-book-id="book-display-rating"]').parentElement.querySelector('.rating-display');
                        if (bookRatingDisplay) {
                            bookRatingDisplay.textContent = data.book_rating + '/10';
                        }
                        
                        // Обновляем звезды отображения
                        const bookDisplayStars = document.querySelector('[data-book-id="book-display-rating"]').querySelectorAll('.star-wrapper');
                        updateStarRating(bookDisplayStars, data.book_rating, bookRatingDisplay, false);
                        
                        showRatingNotification('Оцінку оновлено!');
                    } else {
                        showRatingNotification(data.message || 'Помилка при оновленні оцінки', 'error');
                    }
                } catch (error) {
                    console.error('Error updating rating:', error);
                    showRatingNotification('Помилка при оновленні оцінки: ' + error.message, 'error');
                }
            }

            // Функция для показа уведомления об оценке
            function showRatingNotification(message, type = 'success') {
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
                        if (notification.parentElement) {
                            notification.parentElement.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
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

            // Функции для модального окна добавления в библиотеку
            function openAddToLibraryModal() {
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
            userRatingStars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    document.getElementById('ratingValue').value = rating;
                    document.getElementById('ratingForm').submit();
                });
            });

                // Звезды в форме рецензии
                const reviewStars = document.querySelectorAll('[data-rating]');
                reviewStars.forEach(star => {
                    star.addEventListener('click', function() {
                        const rating = parseInt(this.getAttribute('data-rating'));
                        
                        // Обновляем все звезды в форме
                        reviewStars.forEach((s, index) => {
                            if (index < rating) {
                                s.classList.remove('text-gray-300', 'dark:text-gray-600');
                                s.classList.add('text-yellow-400');
                                s.setAttribute('fill', 'currentColor');
                            } else {
                                s.classList.remove('text-yellow-400');
                                s.classList.add('text-gray-300', 'dark:text-gray-600');
                                s.setAttribute('fill', 'none');
                            }
                        });
                        
                        // Обновляем скрытое поле
                        document.getElementById('ratingInput').value = rating;
                    });
                });
            });

        // Обновление рейтинга пользователя
        async function updateUserRating(rating) {
            try {
                const formData = new FormData();
                formData.append('rating', rating);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                const response = await fetch(`{{ route('books.rating.update', $book->id) }}`, {
                    method: 'POST',
                    body: formData
                });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Обновляем звезды в сайдбаре
                        updateStarsDisplay('#userRating .star', rating);
                        
                        // Обновляем звезды в форме рецензии
                        updateStarsDisplay('[data-rating]', rating);
                        
                        // Обновляем скрытое поле в форме
                        document.getElementById('ratingInput').value = rating;
                        
                        showNotification('Оцінку оновлено!');
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
                        star.classList.remove('text-gray-300', 'dark:text-gray-600');
                        star.classList.add('text-yellow-400');
                        star.setAttribute('fill', 'currentColor');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300', 'dark:text-gray-600');
                        star.setAttribute('fill', 'none');
                    }
                });
            }

            // Показать уведомление
            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
                    type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                }`;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                }, 100);
                
                setTimeout(() => {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.parentElement.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            }
        </script>

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
                            <input type="hidden" name="book_id" value="{{ $book->id }}">

                            <div class="mb-6">
                                <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-3">Оберіть
                                    добірку</label>
                                <select name="library_id" id="librarySelect" required
                                    class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-slate-900 dark:text-white">
                                    <option value="">-- Оберіть добірку --</option>
                                    @php
                                        try {
                                            $userLibraries = auth()->user()->libraries()->orderBy('name')->get();
                                        } catch (\Exception $e) {
                                            $userLibraries = collect();
                                        }
                                    @endphp
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
                                <a href="{{ route('profile.collections', auth()->user()->username) }}"
                                    class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-sm font-medium">
                                    Створити першу добірку →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endauth

    @endpush
@endsection
