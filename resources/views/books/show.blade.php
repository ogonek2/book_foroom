@extends('layouts.app')

@section('title', $book->title . ' - Книжковий форум')

@section('main')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-slate-900 dark:via-slate-800 dark:to-indigo-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 overflow-hidden">
                    <div class="p-8">
                        <div class="flex flex-col md:flex-row gap-8">
                            <div class="flex-shrink-0">
                                <div class="relative group">
                                    <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=450&fit=crop&crop=center' }}" 
                                         alt="{{ $book->title }}" 
                                         class="w-56 h-80 object-cover rounded-2xl shadow-2xl group-hover:shadow-3xl transition-all duration-500 group-hover:scale-105">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-6">
                                    <div class="flex-1">
                                        <h1 class="text-5xl font-black text-slate-900 dark:text-white mb-4 leading-tight">{{ $book->title }}</h1>
                                        <p class="text-2xl text-slate-600 dark:text-slate-400 font-bold mb-6">{{ $book->author }}</p>
                                        <div class="flex items-center space-x-6 mb-8">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-8 h-8 {{ $i <= $book->rating ? 'text-yellow-400' : 'text-slate-300 dark:text-slate-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-4xl font-black text-slate-900 dark:text-white">{{ $book->rating }}</span>
                                            <span class="text-slate-600 dark:text-slate-400 text-xl font-semibold">({{ $book->reviews_count }} відгуків)</span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-6 py-3 rounded-2xl text-lg font-bold shadow-lg" style="background-color: {{ $book->category->color }}20; color: {{ $book->category->color }}; border: 2px solid {{ $book->category->color }}30;">
                                            {{ $book->category->name }}
                                        </span>
                                    </div>
                                </div>

                                @if($book->description)
                                    <div class="mb-8">
                                        <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-4">Опис</h3>
                                        <p class="text-slate-700 dark:text-slate-300 leading-relaxed text-lg font-medium">{{ $book->description }}</p>
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @if($book->isbn)
                                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-5 border border-slate-200 dark:border-slate-600 hover:shadow-lg transition-all duration-300">
                                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2 font-medium">ISBN</p>
                                            <p class="font-bold text-slate-900 dark:text-white text-lg">{{ $book->isbn }}</p>
                                        </div>
                                    @endif
                                    @if($book->publication_year)
                                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-5 border border-slate-200 dark:border-slate-600 hover:shadow-lg transition-all duration-300">
                                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2 font-medium">Рік видання</p>
                                            <p class="font-bold text-slate-900 dark:text-white text-lg">{{ $book->publication_year }}</p>
                                        </div>
                                    @endif
                                    @if($book->publisher)
                                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-5 border border-slate-200 dark:border-slate-600 hover:shadow-lg transition-all duration-300">
                                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2 font-medium">Видавництво</p>
                                            <p class="font-bold text-slate-900 dark:text-white text-lg">{{ $book->publisher }}</p>
                                        </div>
                                    @endif
                                    @if($book->pages)
                                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-2xl p-5 border border-slate-200 dark:border-slate-600 hover:shadow-lg transition-all duration-300">
                                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2 font-medium">Сторінок</p>
                                            <p class="font-bold text-slate-900 dark:text-white text-lg">{{ $book->pages }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
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
            <div class="lg:col-span-1 space-y-8">
                <!-- Related Books -->
                @if($relatedBooks->count() > 0)
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 p-8">
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-6">Схожі книги</h3>
                        <div class="space-y-5">
                            @foreach($relatedBooks as $relatedBook)
                                <a href="{{ route('books.show', $relatedBook->slug) }}" class="group flex space-x-4 p-4 rounded-2xl hover:bg-slate-100/50 dark:hover:bg-slate-700/50 transition-all duration-300 hover:scale-105">
                                    <img src="{{ $relatedBook->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=100&h=150&fit=crop&crop=center' }}" 
                                         alt="{{ $relatedBook->title }}" 
                                         class="w-16 h-20 object-cover rounded-xl shadow-lg group-hover:shadow-2xl transition-all duration-300 group-hover:scale-105">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300 line-clamp-2">
                                            {{ $relatedBook->title }}
                                        </h4>
                                        <p class="text-sm text-slate-600 dark:text-slate-400 font-semibold">{{ $relatedBook->author }}</p>
                                        <div class="flex items-center mt-2">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= $relatedBook->rating ? 'text-yellow-400' : 'text-slate-300 dark:text-slate-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="ml-2 text-sm font-bold text-slate-900 dark:text-white">{{ $relatedBook->rating }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Quick Stats -->
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 p-8">
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-6">Статистика</h3>
                    <div class="space-y-5">
                        <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-2xl">
                            <span class="text-slate-600 dark:text-slate-400 font-semibold">Середня оцінка</span>
                            <span class="font-black text-slate-900 dark:text-white text-xl">{{ $book->rating }}</span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-2xl">
                            <span class="text-slate-600 dark:text-slate-400 font-semibold">Всього рецензій</span>
                            <span class="font-black text-slate-900 dark:text-white text-xl">{{ $book->reviews_count }}</span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-2xl">
                            <span class="text-slate-600 dark:text-slate-400 font-semibold">Категорія</span>
                            <span class="font-black text-slate-900 dark:text-white text-xl">{{ $book->category->name }}</span>
                        </div>
                    </div>
                </div>
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

// Star rating functionality for all users
document.addEventListener('DOMContentLoaded', function() {
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
</script>
@endpush
@endsection