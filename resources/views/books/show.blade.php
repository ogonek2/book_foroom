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
</style>
@endpush

@section('main')
<div class="min-h-screen">
    <div class="mx-auto">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-3 text-sm text-slate-500 dark:text-slate-400 font-medium">
                <li><a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">Головна</a></li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 mx-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <a href="{{ route('books.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200">Книги</a>
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 mx-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
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
                            <div class="flex-shrink-0">
                                <div class="relative group">
                                    <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=450&fit=crop&crop=center' }}" 
                                         alt="{{ $book->title }}" 
                                         class="object-cover rounded-2xl shadow-2xl group-hover:shadow-3xl transition-all duration-500 group-hover:scale-105" style="width: 224px; height: 360px;">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                </div>
                                <!-- Add Button -->
                                <div class="mt-4 text-center">
                                    <button onclick="openReadingStatusModal()" class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-3 rounded-xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform shadow-lg hover:shadow-xl">
                                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Додати
                                    </button>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h1 class="text-3xl font-black text-slate-900 dark:text-white mb-2 leading-tight">{{ $book->title }}</h1>
                                            <span class="text-sm py-2 px-4 dark:bg-gray-800/60 rounded-2xl">
                                                <i class="fas fa-star text-yellow-400"></i>
                                                <span class="text-slate-400 dark:text-white">8.9</span>
                                            </span>
                                        </div>
                                        <p class="text-lg text-slate-600 dark:text-slate-400 font-bold mb-2">{{ $book->author }}</p>
                                    </div>
                                </div>

                                @if($book->description)
                                    <div class="mb-8 relative">
                                        <div id="description-text" class="text-slate-700 dark:text-slate-300 leading-relaxed text-sm font-medium" style="max-height: 280px; overflow: hidden;">
                                            {{ $book->description }}
                                        </div>
                                        <div id="description-gradient" class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-slate-50 via-slate-50/80 to-transparent dark:from-slate-900 dark:via-slate-800/80 dark:to-transparent pointer-events-none" style="top: 200px;"></div>
                                        <div id="description-toggle-container" class="text-left mt-4 hidden">
                                            <button onclick="toggleDescription()" class=" text-white rounded-lg font-bold  transition-all duration-300 transform hover:scale-105 flex items-center justify-start">
                                                <span id="description-toggle-text">Розгорнути</span>
                                                <svg class="w-5 h-5 ml-2 transition-transform duration-300" id="description-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
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
                        <div class="bg-gray dark:bg-gray-700 rounded-2xl p-4 flex items-center justify-between transition-all duration-300 cursor-pointer hover:shadow-lg price-card-hover">
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
                                <button class="bg-orange-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-orange-600 transition-colors duration-200 flex items-center space-x-2">
                                    <span>На сайт</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Book E Entry -->
                        <div class="bg-gray dark:bg-gray-700 rounded-2xl p-4 flex items-center justify-between transition-all duration-300 cursor-pointer hover:shadow-lg price-card-hover">
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
                                <button class="bg-orange-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-orange-600 transition-colors duration-200 flex items-center space-x-2">
                                    <span>На сайт</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Буква Entry -->
                        <div class="bg-gray dark:bg-gray-700 rounded-2xl p-4 flex items-center justify-between transition-all duration-300 cursor-pointer hover:shadow-lg price-card-hover">
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
                                <button class="bg-orange-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-orange-600 transition-colors duration-200 flex items-center space-x-2">
                                    <span>На сайт</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
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
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30">
                    <div class="p-8 border-b border-slate-200/30 dark:border-slate-700/30">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-3xl font-black text-slate-900 dark:text-white">Рецензії</h2>
                                <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">Поділіться своєю думкою про книгу</p>
                            </div>
                            @auth
                                <button onclick="toggleReviewForm()" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                                    <svg class="w-6 h-6 mr-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Написати рецензію
                                </button>
                            @else
                                <button onclick="toggleReviewForm()" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                                    <svg class="w-6 h-6 mr-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Написати рецензію (гість)
                                </button>
                            @endauth
                        </div>
                    </div>

                    <!-- Review Form for All Users -->
                    <div id="reviewForm" class="hidden p-8 border-b border-slate-200/30 dark:border-slate-700/30 bg-slate-50/50 dark:bg-slate-800/50">
                        @auth
                            <!-- Form for authenticated users -->
                            <form action="{{ route('books.reviews.store', $book) }}" method="POST" class="space-y-6">
                                @csrf
                                <div>
                                    <label class="block text-lg font-bold text-slate-700 dark:text-slate-300 mb-4">Оцінка</label>
                                    <div class="flex items-center space-x-3" id="ratingStars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" class="star-rating w-12 h-12 text-slate-300 dark:text-slate-600 hover:text-yellow-400 transition-all duration-300 hover:scale-125" data-rating="{{ $i }}">
                                                <svg class="w-full h-full" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </button>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" id="ratingInput" value="">
                                </div>
                                <div>
                                    <label class="block text-lg font-bold text-slate-700 dark:text-slate-300 mb-4">Рецензія</label>
                                    <textarea name="content" rows="6" 
                                              class="w-full px-6 py-4 border border-slate-300 dark:border-slate-600 rounded-2xl focus:ring-4 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white transition-all duration-300 text-lg font-medium resize-none"
                                              placeholder="Поділіться своїми думками про книгу, персонажів, сюжет..."></textarea>
                                </div>
                                <div class="flex justify-end space-x-4">
                                    <button type="button" onclick="toggleReviewForm()" class="px-8 py-4 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors font-bold text-lg">
                                        Скасувати
                                    </button>
                                    <button type="submit" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                                        <svg class="w-6 h-6 mr-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v8"/>
                                        </svg>
                                        Опублікувати
                                    </button>
                                </div>
                            </form>
                        @else
                            <!-- Form for guests -->
                            <form action="{{ route('books.reviews.guest-store', $book) }}" method="POST" class="space-y-6">
                                @csrf
                                <div>
                                    <label class="block text-lg font-bold text-slate-700 dark:text-slate-300 mb-4">Оцінка</label>
                                <div class="flex items-center space-x-3" id="ratingStarsGuest">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" class="star-rating w-12 h-12 text-slate-300 dark:text-slate-600 hover:text-yellow-400 transition-all duration-300 hover:scale-125" data-rating="{{ $i }}">
                                            <svg class="w-full h-full" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="ratingInputGuest" value="">
                            </div>
                            <div>
                                <label class="block text-lg font-bold text-slate-700 dark:text-slate-300 mb-4">Рецензия</label>
                                <textarea name="content" rows="6" 
                                          class="w-full px-6 py-4 border border-slate-300 dark:border-slate-600 rounded-2xl focus:ring-4 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white transition-all duration-300 text-lg font-medium resize-none"
                                          placeholder="Поделитесь своими мыслями о книге, персонажах, сюжете..."></textarea>
                            </div>
                            <div class="flex justify-end space-x-4">
                                <button type="button" onclick="toggleReviewForm()" class="px-8 py-4 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors font-bold text-lg">
                                    Отмена
                                </button>
                                <button type="submit" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                                    <svg class="w-6 h-6 mr-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v8"/>
                                    </svg>
                                    Опублікувати як гість
                                </button>
                            </div>
                            </form>
                        @endauth
                    </div>

                    <!-- Reviews List -->
                    <div class="p-8">
                        @if($reviews->count() > 0)
                            <div class="space-y-8">
                                @foreach($reviews as $review)
                                    @if(is_null($review->parent_id))
                                        @include('books.partials.review', ['review' => $review, 'book' => $book])
                                    @endif
                                @endforeach
                            </div>
                            <div class="mt-8">
                                {{ $reviews->links() }}
                            </div>
                        @else
                            <div class="text-center py-20">
                                <div class="w-32 h-32 mx-auto mb-8 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                </div>
                                <h3 class="text-3xl font-black text-slate-900 dark:text-white mb-4">Поки немає рецензій</h3>
                                <p class="text-slate-500 dark:text-slate-400 text-xl font-medium">Станьте першим, хто поділиться своєю думкою про цю книгу</p>
                                @guest
                                    <div class="mt-8">
                                        <button onclick="toggleReviewForm()" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-4 rounded-2xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
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
                <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-6 sticky top-24 max-h-screen overflow-y-auto">
                    <!-- Star Rating -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Оцінки</h3>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-1">
                                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-6 h-6 {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">8/10</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">93 оцінок</p>
                        
                        <!-- Rating Breakdown -->
                        <div class="space-y-3" id="rating-breakdown">
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8">10</span>
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-orange-500 h-3 rounded-full transition-all duration-500 progress-bar" data-value="23"></div>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8 text-right">23</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8">9</span>
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-orange-500 h-3 rounded-full transition-all duration-500 progress-bar" data-value="45"></div>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8 text-right">45</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8">8</span>
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-orange-500 h-3 rounded-full transition-all duration-500 progress-bar" data-value="34"></div>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8 text-right">34</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8">7</span>
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-orange-500 h-3 rounded-full transition-all duration-500 progress-bar" data-value="12"></div>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8 text-right">12</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8">6</span>
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-orange-500 h-3 rounded-full transition-all duration-500 progress-bar" data-value="16"></div>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8 text-right">16</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8">5</span>
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-orange-500 h-3 rounded-full transition-all duration-500 progress-bar" data-value="15"></div>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8 text-right">15</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8">4</span>
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-orange-500 h-3 rounded-full transition-all duration-500 progress-bar" data-value="7"></div>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8 text-right">7</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8">3</span>
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-orange-500 h-3 rounded-full transition-all duration-500 progress-bar" data-value="4"></div>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8 text-right">4</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8">2</span>
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-orange-500 h-3 rounded-full transition-all duration-500 progress-bar" data-value="8"></div>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8 text-right">8</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8">1</span>
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-orange-500 h-3 rounded-full transition-all duration-500 progress-bar" data-value="3"></div>
                                        </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400 w-8 text-right">3</span>
                                    </div>
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
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Прочитано</span>
                                    <span class="text-sm text-gray-900 dark:text-white">23</span>
                                </div>
                                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-green-500 h-3 rounded-full transition-all duration-500 progress-bar" data-value="23"></div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Читаю</span>
                                    <span class="text-sm text-gray-900 dark:text-white">45</span>
                                </div>
                                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-green-500 h-3 rounded-full transition-all duration-500 progress-bar" data-value="45"></div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Буду читати</span>
                                    <span class="text-sm text-gray-900 dark:text-white">34</span>
                                </div>
                                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="bg-green-500 h-3 rounded-full transition-all duration-500 progress-bar" data-value="34"></div>
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
<div id="readingStatusModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-gray dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Додати до списку</h3>
                <button onclick="closeReadingStatusModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-3">
                <button onclick="selectReadingStatus('read')" class="w-full text-left p-4 rounded-xl border-2 border-transparent hover:border-indigo-500 dark:hover:border-indigo-400 transition-all duration-300 bg-slate-50 dark:bg-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 group">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mr-4 group-hover:bg-green-200 dark:group-hover:bg-green-800/40 transition-colors">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Прочитано</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Книга прочитана повністю</p>
                        </div>
                    </div>
                </button>
                
                <button onclick="selectReadingStatus('reading')" class="w-full text-left p-4 rounded-xl border-2 border-transparent hover:border-indigo-500 dark:hover:border-indigo-400 transition-all duration-300 bg-slate-50 dark:bg-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 group">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mr-4 group-hover:bg-blue-200 dark:group-hover:bg-blue-800/40 transition-colors">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Читаю</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Зараз читаю цю книгу</p>
                        </div>
                    </div>
                </button>
                
                <button onclick="selectReadingStatus('want-to-read')" class="w-full text-left p-4 rounded-xl border-2 border-transparent hover:border-indigo-500 dark:hover:border-indigo-400 transition-all duration-300 bg-slate-50 dark:bg-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 group">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mr-4 group-hover:bg-purple-200 dark:group-hover:bg-purple-800/40 transition-colors">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Буду читати</h4>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Планую прочитати цю книгу</p>
                        </div>
                    </div>
                </button>
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
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
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

function selectReadingStatus(status) {
    // Здесь можно добавить логику отправки на сервер
    console.log('Selected reading status:', status);
    
    // Показываем уведомление
    showStatusNotification(status);
    
    // Закрываем модальное окно
    closeReadingStatusModal();
}

function showStatusNotification(status) {
    const statusTexts = {
        'read': 'Прочитано',
        'reading': 'Читаю',
        'want-to-read': 'Буду читати'
    };
    
    // Создаем уведомление
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50 transform translate-x-full transition-transform duration-300';
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Книга додана до списку "${statusTexts[status]}"
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
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
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
    
    // Обработчик для закрытия модального окна по клику на фон
    document.getElementById('readingStatusModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeReadingStatusModal();
        }
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
                            s.classList.add('text-slate-300', 'dark:text-slate-600');
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

// Comments functionality
function toggleComments(reviewId) {
    const commentsContainer = document.getElementById(`commentsContainer${reviewId}`);
    const commentsToggle = document.getElementById(`commentsToggle${reviewId}`);
    const arrow = commentsToggle.querySelector('svg');
    
    if (commentsContainer.classList.contains('hidden')) {
        commentsContainer.classList.remove('hidden');
        arrow.style.transform = 'rotate(180deg)';
    } else {
        commentsContainer.classList.add('hidden');
        arrow.style.transform = 'rotate(0deg)';
    }
}

function submitComment(event, reviewId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const content = formData.get('content');
    
    if (!content.trim()) {
        alert('Будь ласка, введіть текст коментаря');
        return;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Відправляємо...';
    submitBtn.disabled = true;
    
    // Send AJAX request
    fetch(`/books/{{ $book->id }}/reviews`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            content: content,
            parent_id: reviewId,
            rating: null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear the form
            form.reset();
            
            // Add the comment immediately to the UI
            addCommentToUI(reviewId, data.reply);
            
            // Update comments count
            updateCommentsCount(reviewId);
            
            // Show success message
            showNotification('Коментар успішно додано!', 'success');
        } else {
            throw new Error(data.message || 'Помилка при додаванні коментаря');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Помилка при додаванні коментаря: ' + error.message, 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

function addCommentToUI(reviewId, commentData) {
    const commentsContent = document.getElementById(`commentsContent${reviewId}`);
    if (!commentsContent) return;
    
    // Create comment HTML
    const commentHTML = createCommentHTML(commentData);
    
    // Add comment to container
    commentsContent.insertAdjacentHTML('beforeend', commentHTML);
    
    // Show comments container if it was hidden
    const commentsContainer = document.getElementById(`commentsContainer${reviewId}`);
    if (commentsContainer.classList.contains('hidden')) {
        commentsContainer.classList.remove('hidden');
        const arrow = document.querySelector(`#commentsToggle${reviewId} svg`);
        if (arrow) arrow.style.transform = 'rotate(180deg)';
    }
}

function createCommentHTML(commentData) {
    const isGuest = commentData.is_guest;
    const authorName = commentData.author_name || 'Анонімний користувач';
    const createdAt = new Date(commentData.created_at).toLocaleString('uk-UA', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    return `
        <div class="bg-slate-100 dark:bg-slate-800 rounded-lg p-3">
            <div class="flex items-start space-x-2">
                <div class="flex-shrink-0">
                    ${isGuest ? 
                        `<div class="w-6 h-6 bg-gray-300 dark:bg-slate-600 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>` :
                        `<div class="w-6 h-6 rounded-full overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=100&h=100&fit=crop&crop=face" 
                                 alt="${authorName}" 
                                 class="w-full h-full object-cover">
                        </div>`
                    }
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="text-xs font-medium text-gray-900 dark:text-white">${authorName}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">${createdAt}</span>
                    </div>
                    <div class="text-xs text-gray-900 dark:text-white leading-relaxed">${commentData.content}</div>
                </div>
            </div>
        </div>
    `;
}

function updateCommentsCount(reviewId) {
    const commentsContent = document.getElementById(`commentsContent${reviewId}`);
    if (commentsContent) {
        const count = commentsContent.children.length;
        const toggleText = document.getElementById(`commentsToggleText${reviewId}`);
        if (toggleText) {
            const word = count == 1 ? 'коментар' : (count < 5 ? 'коментарі' : 'коментарів');
            toggleText.textContent = `${count} ${word}`;
        }
    }
}
</script>
@endpush
@endsection