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
                                                class="w-full max-w-56 h-100 aspect-[3/4] object-cover rounded-2xl shadow-2xl group-hover:shadow-3xl transition-all duration-500">
                                        @else
                                            <div
                                                class="w-full max-w-56 h-80 bg-slate-100 dark:bg-slate-700 flex items-center justify-center rounded-2xl shadow-2xl">
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
                                            <span class="font-medium text-lg">{{ $author->books_count }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2 text-xl">
                                            <i class="fas fa-feather text-gray-400"></i>
                                            <span class="text-white-700 text-lg">10</span>
                                        </div>
                                        <div class="flex items-center space-x-2 text-xl">
                                            <i class="fas fa-quote-left text-gray-400"></i>
                                            <span class="text-white-700 text-lg">20</span>
                                        </div>
                                        <div class="flex items-center space-x-2 text-xl">
                                            <i class="fas fa-info-circle text-gray-400"></i>
                                            <span class="text-white-700 text-lg">14</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Author Content -->
                            <div class="lg:col-span-2">
                                <div class="space-y-6">
                                    <h1 class="text-3xl font-black text-slate-900 dark:text-white leading-tight">
                                        {{ $author->full_name }} 
                                        <span class="text-sm py-2 px-4 dark:bg-gray-800/60 rounded-2xl">
                                            <i class="fas fa-star text-yellow-400"></i>
                                            <span class="text-slate-400 dark:text-white">8.9</span>
                                        </span>
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
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">

                    <!-- Key Facts -->
                    <div
                        class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-5 sticky top-24 max-h-screen overflow-y-auto">
                        <div class="mb-8">
                            <h3 class="text-s text-gray-700 dark:text-white mb-4">Середня оцінка творів автора</h3>
                            <div class="flex items-center justify-between mb-4">
                                <div class="star-rating-container" data-book-id="author-rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div class="star-wrapper cursor-pointer transition-transform duration-200 hover:scale-110" data-star="{{ $i }}">
                                            <div class="star-background">
                                                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </div>
                                            <div class="star-fill absolute top-0 left-0 w-full h-full overflow-hidden">
                                                <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                                <span class="text-2xl font-bold text-gray-900 dark:text-white text-center block rating-display">8.9</span>
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
                                    <span class="text-gray-600 dark:text-white">{{ $author->books_count }}</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Рецензій</span>
                                    <span class="text-gray-600 dark:text-white">89</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Цитат</span>
                                    <span class="text-gray-600 dark:text-white">193</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">Фактів</span>
                                    <span class="text-gray-400 dark:text-white">25</span>
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
                    <div class="grid grid-cols-2 lg:grid-cols-2 gap-6">
                        @foreach ($books as $book)
                            <div
                                class="group bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 overflow-hidden hover:shadow-2xl transition-all duration-300 hover:scale-[1.02]">
                                <a href="{{ route('books.show', $book) }}" class="block">
                                    <div class="flex gap-6 p-6">
                                        <!-- Book Cover -->
                                        <div class="flex-shrink-0">
                                            <div class="relative group">
                                                <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=200&h=300&fit=crop&crop=center' }}"
                                                    alt="{{ $book->title }}"
                                                    class="w-32 h-48 object-cover rounded-2xl shadow-lg group-hover:shadow-2xl transition-all duration-300 group-hover:scale-105">
                                            </div>
                                        </div>

                                        <!-- Book Details -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-col h-full justify-between">
                                                <!-- Title and Author -->
                                                <div class="mb-0">
                                                    <h3
                                                        class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300 mb-2">
                                                        {{ $book->title }}
                                                    </h3>
                                                    <div class="flex items-center mb-4">
                                                        <div class="star-rating-container" data-book-id="{{ $book->id }}">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <div class="star-wrapper cursor-pointer transition-transform duration-200 hover:scale-110" data-star="{{ $i }}">
                                                                    <div class="star-background">
                                                                        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                        </svg>
                                                                    </div>
                                                                    <div class="star-fill absolute top-0 left-0 w-full h-full overflow-hidden">
                                                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                        </svg>
                                                                    </div>
                                                                </div>
                                                            @endfor
                                                        </div>
                                                        <span class="ml-2 text-s text-slate-900 dark:text-white rating-display">{{ round(($book->rating ?? 0) * 2, 1) }}/10</span>
                                                    </div>
                                                </div>

                                                <!-- Add Button -->
                                                <div class="flex justify-between">
                                                    <div class="flex items-center space-x-4 mb-4">
                                                        <div
                                                            class="flex items-center space-x-2 text-slate-600 dark:text-slate-400">
                                                            <i class="fas fa-feather"></i>
                                                            <span
                                                                class="text-base font-semibold">{{ $book->reviews_count ?? 0 }}</span>
                                                        </div>
                                                        <div
                                                            class="flex items-center space-x-2 text-slate-600 dark:text-slate-400">
                                                            <i class="fas fa-paragraph"></i>
                                                            <span
                                                                class="text-base font-semibold">{{ $book->pages_count ?? 0 }}</span>
                                                        </div>
                                                    </div>
                                                    <button
                                                        class="bg-gradient-to-r from-brand-500 to-accent-500 text-white hover:from-orange-600 hover:to-red-600 text-white font-bold py-2 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                                                        <div class="flex items-center space-x-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                            </svg>
                                                            <span class="text-sm">Додати</span>
                                                        </div>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
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

            <!-- Author Quotes Section -->
            <section class="py-6">
                <div class="flex items-start justify-between mb-8">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white">
                        Цитати <small class="text-slate-600 dark:text-slate-400 text-lg font-semibold">({{ rand(15, 35) }})</small>
                    </h2>
                    <button class="bg-slate-800 hover:bg-slate-700 text-white font-medium px-4 py-2 rounded-xl transition-all duration-200">
                        Додати цитату
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Quote Card 1 -->
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 overflow-hidden p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">V</span>
                                </div>
                                <span class="text-white font-medium">Violetta</span>
                            </div>
                            <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/>
                            </svg>
                        </div>
                        
                        <p class="text-white text-base leading-relaxed mb-4">
                            "Світло книги здатне розбудити розум навіть там, де панує морок байдужості."
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button class="flex items-center space-x-2 text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <span class="text-sm">63</span>
                                </button>
                                <button class="text-slate-400 hover:text-blue-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                    </svg>
                                </button>
                                <button class="text-slate-400 hover:text-yellow-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                    </svg>
                                </button>
                            </div>
                            <button class="text-slate-400 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Quote Card 2 -->
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 overflow-hidden p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">T</span>
                                </div>
                                <span class="text-white font-medium">The Magic</span>
                            </div>
                            <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/>
                            </svg>
                        </div>
                        
                        <p class="text-white text-base leading-relaxed mb-4">
                            "Найстрашніше не тоді, коли забороняють читати, а тоді, коли люди самі відмовляються від думок."
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button class="flex items-center space-x-2 text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <span class="text-sm">44</span>
                                </button>
                                <button class="text-slate-400 hover:text-blue-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                    </svg>
                                </button>
                                <button class="text-slate-400 hover:text-yellow-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                    </svg>
                                </button>
                            </div>
                            <button class="text-slate-400 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Quote Card 3 -->
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 overflow-hidden p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">А</span>
                                </div>
                                <span class="text-white font-medium">Антоніо</span>
                            </div>
                            <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/>
                            </svg>
                        </div>
                        
                        <p class="text-white text-base leading-relaxed mb-4">
                            "Той, хто зберігає знання, береже не лише слова — він береже свободу."
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button class="flex items-center space-x-2 text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <span class="text-sm">23</span>
                                </button>
                                <button class="text-slate-400 hover:text-blue-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                    </svg>
                                </button>
                                <button class="text-slate-400 hover:text-yellow-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                    </svg>
                                </button>
                            </div>
                            <button class="text-slate-400 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Quote Card 4 -->
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 overflow-hidden p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-pink-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">М</span>
                                </div>
                                <span class="text-white font-medium">Максіма</span>
                            </div>
                            <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z"/>
                            </svg>
                        </div>
                        
                        <p class="text-white text-base leading-relaxed mb-4">
                            "Люди можуть втратити все, але поки вони здатні пам'ятати і передавати історії, вони залишаються людьми."
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button class="flex items-center space-x-2 text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <span class="text-sm">12</span>
                                </button>
                                <button class="text-slate-400 hover:text-blue-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                    </svg>
                                </button>
                                <button class="text-slate-400 hover:text-yellow-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                    </svg>
                                </button>
                            </div>
                            <button class="text-slate-400 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Show More Button -->
                <div class="flex justify-center mt-8">
                    <button class="flex items-center space-x-2 text-white font-medium px-6 py-3 rounded-xl bg-slate-700 hover:bg-slate-600 transition-all duration-200">
                        <span>Показати ще</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
            </section>

            <!-- Author Facts Section -->
            <section class="py-6 mt-16">
                <div class="flex items-start justify-between mb-8">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white">
                        Факти <small class="text-slate-600 dark:text-slate-400 text-lg font-semibold">({{ rand(12, 25) }})</small>
                    </h2>
                    <button class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium px-4 py-2 rounded-xl transition-all duration-200">
                        Додати факт
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fact Card 1 -->
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-white/20 dark:border-slate-700/30 overflow-hidden p-6 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-white font-medium">Violetta</span>
                            </div>
                        </div>
                        
                        <p class="text-white text-base leading-relaxed mb-4">
                            Письменник друкував текст у бібліотеці UCLA на платних друкарських машинках (вартість — 10 центів за пів години). У результаті витратив близько $9,80, щоб завершити рукопис.
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button class="flex items-center space-x-2 text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <span class="text-sm">12</span>
                                </button>
                                <button class="text-slate-400 hover:text-blue-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                    </svg>
                                </button>
                            </div>
                            <button class="text-slate-400 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Fact Card 2 -->
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-white/20 dark:border-slate-700/30 overflow-hidden p-6 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-white font-medium">The Magic</span>
                            </div>
                        </div>
                        
                        <p class="text-white text-base leading-relaxed mb-4">
                            Перша відома екранізація вийшла у 1966 році (режисер Франсуа Трюффо). Друга — у 2018 році від НВО.
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button class="flex items-center space-x-2 text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <span class="text-sm">33</span>
                                </button>
                                <button class="text-slate-400 hover:text-blue-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                    </svg>
                                </button>
                            </div>
                            <button class="text-slate-400 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Fact Card 3 -->
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-white/20 dark:border-slate-700/30 overflow-hidden p-6 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-white font-medium">Антоніо</span>
                            </div>
                        </div>
                        
                        <p class="text-white text-base leading-relaxed mb-4">
                            У 2004 році «451° за Фаренгейтом» отримав премію «Прометей» (Prometheus Hall of Fame Award).
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button class="flex items-center space-x-2 text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <span class="text-sm">23</span>
                                </button>
                                <button class="text-slate-400 hover:text-blue-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                    </svg>
                                </button>
                            </div>
                            <button class="text-slate-400 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Fact Card 4 -->
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-white/20 dark:border-slate-700/30 overflow-hidden p-6 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-white font-medium">Максіма</span>
                            </div>
                        </div>
                        
                        <p class="text-white text-base leading-relaxed mb-4">
                            Бредбері написав першу версію роману (новелу «Пожежний») у 1950 році. Пізніше він розширив її до повноцінної книги.
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <button class="flex items-center space-x-2 text-slate-400 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <span class="text-sm">11</span>
                                </button>
                                <button class="text-slate-400 hover:text-blue-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                    </svg>
                                </button>
                            </div>
                            <button class="text-slate-400 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Show More Button -->
                <div class="flex justify-center mt-8">
                    <button class="flex items-center space-x-2 text-white font-medium px-6 py-3 rounded-xl bg-slate-700 hover:bg-slate-600 transition-all duration-200">
                        <span>Показати ще</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                </div>
            </section>
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
            
            // Инициализируем интерактивные звезды
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
            
            .star-rating-container[data-book-id]:not([data-book-id="author-rating"]) .star-wrapper {
                width: 16px;
                height: 16px;
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
                if (container.getAttribute('data-book-id') === 'author-rating') {
                    initialRating = 8.9;
                } else {
                    initialRating = parseFloat(ratingDisplay.textContent.split('/')[0]);
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
                        showRatingNotification(container, rating);
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
            ratingDisplay.textContent = rating.toFixed(1);
            
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

        // Функция для показа уведомления об оценке
        function showRatingNotification(container, rating) {
            // Создаем временное уведомление
            const notification = document.createElement('div');
            notification.className = 'absolute top-0 left-0 right-0 bg-green-500 text-white text-center py-1 text-sm font-medium rounded-t-2xl transform -translate-y-full transition-all duration-300';
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
    </script>
@endpush
