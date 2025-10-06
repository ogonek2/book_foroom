@extends('layouts.app')

@section('title', 'Головна - Книжковий форум')

@section('main')
        <!-- Hero Banner -->
        <section class="relative mb-8 md:mb-12 overflow-hidden">
            <!-- Simple Background -->
            <div class="absolute inset-0 z-0">
                <div class="absolute top-2 left-2 md:top-4 md:left-4 w-8 h-8 md:w-12 md:h-12 bg-gradient-to-r from-orange-500/20 to-pink-500/20 rounded-full"></div>
                <div class="absolute top-4 right-4 md:top-8 md:right-8 w-6 h-6 md:w-8 md:h-8 bg-gradient-to-r from-pink-500/20 to-orange-500/20 rounded-full"></div>
                <div class="absolute bottom-2 left-1/4 md:bottom-4 w-4 h-4 md:w-6 md:h-6 bg-gradient-to-r from-orange-400/20 to-pink-400/20 rounded-full"></div>
            </div>
            
            <!-- Main Banner Content -->
            <div class="relative z-10 bg-light-bg dark:bg-dark-bg/30 backdrop-blur-xl border border-light-border dark:border-dark-border/30 rounded-xl md:rounded-2xl p-4 md:p-6 lg:p-8 shadow-lg">
                <div class="text-center max-w-4xl mx-auto">
                    <!-- Badge -->
                    <div class="inline-flex items-center bg-gradient-to-r from-brand-500/20 to-accent-500/20 backdrop-blur-sm border border-brand-500/30 rounded-full px-3 py-1.5 md:px-4 md:py-2 mb-3 md:mb-4">
                        <svg class="w-3 h-3 md:w-4 md:h-4 text-brand-400 mr-1.5 md:mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="text-brand-500 text-xs md:text-sm font-medium">Найкраща платформа для книголюбів</span>
                    </div>
                    
                    <!-- Main Heading -->
                    <h1 class="text-2xl sm:text-3xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-3 md:mb-4">
                        <span class="block text-light-text-primary dark:text-dark-text-primary">
                            Відкрийте світ
                        </span>
                        <span class="block bg-gradient-to-r from-brand-500 via-accent-500 to-brand-600 bg-clip-text text-transparent mt-1">
                            нескінченних історій
                        </span>
                    </h1>
                    
                    <!-- Subtitle -->
                    <p class="text-sm sm:text-base md:text-lg text-light-text-secondary dark:text-dark-text-secondary mb-4 md:mb-6 max-w-2xl mx-auto leading-relaxed px-2">
                        Приєднуйтесь до найбільшої спільноти читачів України. 
                        <span class="text-brand-500 dark:text-brand-300 font-medium">Діліться думками, відкривайте нові автори та знаходьте натхнення.</span>
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-center mb-6 md:mb-8 px-2">
                        <a href="{{ route('books.index') }}" class="bg-gradient-to-r from-brand-500 to-accent-500 text-white px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold text-base md:text-lg hover:from-brand-600 hover:to-accent-600 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Переглянути книги
                            </span>
                        </a>
                        <a href="{{ route('forum.index') }}" class="bg-light-text-primary/50 backdrop-blur-sm border border-light-border/50 text-white px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold text-base md:text-lg hover:bg-light-text-secondary/50 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Приєднатися до обговорення
                            </span>
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-3 md:gap-4">
                        <div class="bg-light-bg dark:bg-dark-bg-secondary/30 backdrop-blur-sm border border-light-border dark:border-dark-border/30 rounded-lg md:rounded-xl p-3 md:p-4 text-center hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary/40 transition-all duration-300 shadow-sm">
                            <div class="text-xl md:text-2xl font-bold text-brand-500 dark:text-brand-400 mb-1">{{ number_format($stats['users']) }}</div>
                            <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs md:text-sm">Активних читачів</div>
                        </div>
                        <div class="bg-light-bg dark:bg-dark-bg-secondary/30 backdrop-blur-sm border border-light-border dark:border-dark-border/30 rounded-lg md:rounded-xl p-3 md:p-4 text-center hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary/40 transition-all duration-300 shadow-sm">
                            <div class="text-xl md:text-2xl font-bold text-accent-500 dark:text-accent-400 mb-1">{{ number_format($stats['books']) }}</div>
                            <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs md:text-sm">Книг у каталозі</div>
                        </div>
                        <div class="bg-light-bg dark:bg-dark-bg-secondary/30 backdrop-blur-sm border border-light-border dark:border-dark-border/30 rounded-lg md:rounded-xl p-3 md:p-4 text-center hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary/40 transition-all duration-300 shadow-sm">
                            <div class="text-xl md:text-2xl font-bold text-brand-500 dark:text-brand-400 mb-1">{{ number_format($stats['reviews']) }}</div>
                            <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs md:text-sm">Рецензій та відгуків</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 1. Книги якими зацікавились -->
        <section class="mb-12 md:mb-16">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 md:mb-8 gap-3">
                <h2 class="text-2xl md:text-3xl font-bold text-light-text-primary dark:text-dark-text-primary">Книги якими зацікавились</h2>
                <a href="{{ route('books.index') }}" class="text-brand-500 dark:text-brand-400 hover:text-brand-600 dark:hover:text-brand-300 text-sm font-medium flex items-center gap-2 transition-colors self-start sm:self-auto">
                    Переглянути всі
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="relative">
                <div class="flex space-x-4 md:space-x-6 overflow-x-auto pb-4 scrollbar-hide snap-x snap-mandatory">
                    @forelse($featuredBooks as $book)
                        <div class="flex-shrink-0 w-36 sm:w-40 md:w-44 snap-start">
                            <a href="{{ route('books.show', $book->slug) }}" class="block bg-light-bg dark:bg-dark-bg-secondary rounded-lg overflow-hidden hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-colors duration-200 group shadow-sm hover:shadow-md">
                                <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=160&h=224&fit=crop&crop=center' }}" 
                                     alt="{{ $book->title }}" 
                                     class="w-full h-52 sm:h-56 md:h-60 object-cover">
                                <div class="p-3 md:p-4 text-left">
                                    <h3 class="text-light-text-primary dark:text-dark-text-primary font-semibold text-sm md:text-base line-clamp-1 mb-1">{{ $book->title }}</h3>
                                    <p class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs md:text-sm">{{ $book->publication_year }} • {{ $book->category->name ?? 'Без категорії' }}</p>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="flex-shrink-0 w-full text-center py-8">
                            <p class="text-light-text-tertiary dark:text-dark-text-tertiary">Поки що немає рекомендуваних книг</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- 2. Рекомендовані книги -->
        <section class="mb-12 md:mb-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 items-center gap-8 lg:gap-20">
                <!-- Content Side -->
                <div class="lg:col-span-1 space-y-4 md:space-y-6">
                    <div>
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-light-text-primary dark:text-white mb-3 md:mb-4">Рекомендовані книги</h2>
                        <p class="text-light-text-secondary dark:text-gray-300 text-base md:text-lg leading-relaxed">
                            Відкрийте для себе найкращі твори світової літератури, 
                            обрані нашими експертами та спільнотою читачів.
                        </p>
                        </div>
                    
                    <div class="space-y-4 md:space-y-4">
                        <a href="{{ route('books.index') }}" class="inline-flex items-center justify-center w-full bg-gradient-to-r from-brand-500 to-accent-500 text-white px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold hover:from-brand-600 hover:to-accent-600 transition-all duration-200 shadow-lg hover:shadow-xl text-base md:text-lg">
                            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Переглянути всі книги
                        </a>
                        
                        <a href="{{ route('forum.index') }}" class="inline-flex items-center justify-center w-full bg-light-bg-secondary dark:bg-gray-700 text-light-text-primary dark:text-white px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold hover:bg-light-bg-tertiary dark:hover:bg-gray-600 transition-all duration-200 border border-light-border dark:border-gray-600 text-base md:text-lg">
                            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Приєднатися до обговорення
                        </a>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-6 text-xs md:text-sm text-light-text-tertiary dark:text-gray-400">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 md:w-4 md:h-4 mr-2 text-brand-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            <span>4.8/5 середня оцінка</span>
                            </div>
                        <div class="flex items-center">
                            <svg class="w-3 h-3 md:w-4 md:h-4 mr-2 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span>1,200+ читачів</span>
                        </div>
                    </div>
                </div>

                <!-- Slider Side -->
                <div class="lg:col-span-2">
                    <div class="relative">
                        <!-- Slider Container -->
                        <div class="overflow-hidden rounded-xl md:rounded-2xl">
                            <div class="flex transition-transform duration-500 ease-in-out" id="bookSlider">
                                @forelse($recommendedBooks as $index => $book)
                                    <div class="w-full flex-shrink-0">
                                        <div class="bg-light-bg dark:bg-gray-800 rounded-xl md:rounded-2xl overflow-hidden border border-light-border dark:border-gray-700">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                                                <div class="relative">
                                                    <div class="relative h-64 sm:h-72 md:h-80 lg:h-96 bg-light-bg-secondary/20 dark:bg-gray-900/20">
                                                        <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=1200&h=1200&fit=crop&crop=center' }}" 
                                                             alt="{{ $book->title }} (background)" 
                                                             class="absolute inset-0 w-full h-full object-cover blur-lg scale-110 opacity-70">
                                                        <div class="absolute inset-0 flex items-center justify-center p-3 md:p-4">
                                                            <div class="relative w-28 h-40 sm:w-32 sm:h-44 md:w-36 md:h-52 lg:w-44 lg:h-64">
                                                                <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=176&h=256&fit=crop&crop=center' }}" 
                                                                     alt="{{ $book->title }}" 
                                                                     class="w-full h-full object-cover rounded-lg shadow-2xl ring-1 ring-white/10">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="absolute top-3 left-3 md:top-4 md:left-4 bg-brand-500 rounded-full px-2 py-1 md:px-3 md:py-1">
                                                        <span class="text-xs font-medium text-white">{{ $index === 0 ? 'Рекомендовано' : ($index === 1 ? 'Популярно' : 'Класика') }}</span>
                                                    </div>
                                                </div>
                                                <div class="p-4 md:p-6 lg:p-8 flex flex-col justify-center">
                                                    <div class="mb-3 md:mb-4">
                                                        <span class="text-brand-400 dark:text-brand-400 text-xs md:text-sm font-medium">{{ $book->category->name ?? 'Без категорії' }}</span>
                                                        <h3 class="text-xl sm:text-2xl md:text-3xl font-bold text-light-text-primary dark:text-white mt-1 md:mt-2 mb-2 md:mb-3">{{ $book->title }}</h3>
                                                        <p class="text-light-text-secondary dark:text-gray-300 text-sm md:text-base lg:text-lg mb-3 md:mb-4">{{ $book->author }}</p>
                                                    </div>
                                                    <p class="text-light-text-tertiary dark:text-gray-400 mb-4 md:mb-6 leading-relaxed text-sm md:text-base">
                                                        {{ Str::limit($book->description, 120) }}
                                                    </p>
                                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                                        <div class="flex items-center space-x-3 md:space-x-4">
                                                            <div class="flex items-center">
                                                                <svg class="w-3 h-3 md:w-4 md:h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                                <span class="text-light-text-primary dark:text-white font-semibold text-sm md:text-base">{{ number_format($book->rating, 1) }}</span>
                                                            </div>
                                                            <span class="text-light-text-tertiary dark:text-dark-text-tertiary text-xs md:text-sm">{{ $book->publication_year }}</span>
                                                        </div>
                                                        <a href="{{ route('books.show', $book->slug) }}" class="bg-brand-500 hover:bg-brand-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg text-xs md:text-sm font-medium transition-colors text-center">
                                                            Читати далі
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="w-full flex-shrink-0">
                                        <div class="bg-light-bg dark:bg-gray-800 rounded-xl md:rounded-2xl overflow-hidden border border-light-border dark:border-gray-700 p-6 md:p-8 text-center">
                                            <p class="text-light-text-tertiary dark:text-gray-400">Поки що немає рекомендуваних книг</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        
                        <!-- Navigation Arrows -->
                        <button onclick="previousSlide()" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-light-bg-secondary/80 dark:bg-gray-800/80 backdrop-blur-sm hover:bg-light-bg-tertiary/80 dark:hover:bg-gray-700/80 text-light-text-primary dark:text-white p-3 rounded-full transition-all duration-200 shadow-lg border border-light-border dark:border-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button onclick="nextSlide()" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-light-bg-secondary/80 dark:bg-gray-800/80 backdrop-blur-sm hover:bg-light-bg-tertiary/80 dark:hover:bg-gray-700/80 text-light-text-primary dark:text-white p-3 rounded-full transition-all duration-200 shadow-lg border border-light-border dark:border-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        
                        <!-- Dots Indicator -->
                        <div class="flex justify-center mt-6 space-x-2">
                            @if(count($recommendedBooks) > 0)
                                @for($i = 1; $i <= count($recommendedBooks); $i++)
                                    <button onclick="currentSlide({{ $i }})" class="w-3 h-3 rounded-full {{ $i === 1 ? 'bg-brand-500 dark:bg-brand-500' : 'bg-light-text-tertiary dark:bg-gray-600 hover:bg-light-text-secondary dark:hover:bg-gray-500' }} transition-all duration-200"></button>
                                @endfor
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
        let slideIndex = 1;
        const totalSlides = {{ count($recommendedBooks) > 0 ? count($recommendedBooks) : 1 }};

        function showSlide(n) {
            const slider = document.getElementById('bookSlider');
            const dots = document.querySelectorAll('.flex.justify-center button');
            
            if (n > totalSlides) slideIndex = 1;
            if (n < 1) slideIndex = totalSlides;
            
            slider.style.transform = `translateX(-${(slideIndex - 1) * 100}%)`;
            
            dots.forEach((dot, index) => {
                if (index === slideIndex - 1) {
                    dot.className = 'w-3 h-3 rounded-full bg-brand-500 dark:bg-brand-500 transition-all duration-200';
                } else {
                    dot.className = 'w-3 h-3 rounded-full bg-light-text-tertiary dark:bg-gray-600 hover:bg-light-text-secondary dark:hover:bg-gray-500 transition-all duration-200';
                }
            });
        }

        function nextSlide() {
            slideIndex++;
            showSlide(slideIndex);
        }

        function previousSlide() {
            slideIndex--;
            showSlide(slideIndex);
        }

        function currentSlide(n) {
            slideIndex = n;
            showSlide(slideIndex);
        }

        // Auto-slide every 5 seconds
        setInterval(nextSlide, 5000);
        </script>

        <!-- 3. Рецензії -->
        <section class="mb-10 md:mb-12">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 md:mb-6 gap-3">
                <h2 class="text-2xl md:text-3xl font-bold text-light-text-primary dark:text-dark-text-primary">Рецензії</h2>
                <a href="{{ route('books.index') }}" class="text-brand-500 dark:text-brand-400 hover:text-brand-600 dark:hover:text-brand-300 text-sm font-medium flex items-center gap-2 transition-colors self-start sm:self-auto">
                    Переглянути всі
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                </a>
            </div>
            <div class="relative">
                <div class="flex space-x-4 md:space-x-6 overflow-x-auto pb-4 scrollbar-hide snap-x snap-mandatory">
                    @forelse($recentReviews as $review)
                    <!-- Review Card -->
                    <div class="flex-shrink-0 w-80 sm:w-96 md:w-[28rem] snap-start">
                        <div class="bg-light-bg dark:bg-dark-bg-secondary rounded-lg p-4 md:p-5 hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-colors duration-200 h-full shadow-sm hover:shadow-md">
                            <div class="flex items-start space-x-4">
                                <!-- Left side: Avatar + Author name and Review content -->
                                <div class="flex-1 min-w-0">
                                    <!-- Author info -->
                                    <div class="flex items-center space-x-3 mb-3">
                                        <!-- Avatar with first letter -->
                                        @if($review->user)
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-brand-500 to-accent-500 flex items-center justify-center flex-shrink-0">
                                                <span class="text-white font-semibold text-sm">{{ strtoupper(substr($review->user->name, 0, 1)) }}</span>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center flex-shrink-0">
                                                <span class="text-gray-300 font-semibold text-sm">Г</span>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <span class="text-light-text-primary dark:text-dark-text-primary font-medium text-sm">{{ $review->getAuthorName() }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Review content -->
                                    <p class="text-light-text-secondary dark:text-dark-text-secondary text-sm leading-relaxed line-clamp-4">
                                        {{ Str::limit($review->content, 200) }}
                                    </p>
                                </div>
                                
                                <!-- Right side: Book cover, title and button -->
                                <div class="flex-shrink-0 flex flex-col items-center space-y-3">
                                    <!-- Book cover -->
                                    <div class="w-20 h-28 md:w-24 md:h-32 rounded-lg overflow-hidden">
                                        <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=160&h=224&fit=crop" 
                                             alt="{{ $review->book->title }}" 
                                             class="w-full h-full object-cover">
                                    </div>
                                    
                                    <!-- Book title -->
                                    <h4 class="text-brand-500 dark:text-brand-400 font-medium text-sm text-center truncate w-full" title="{{ $review->book->title }}">
                                        {{ $review->book->title }}
                                    </h4>
                                    
                                    <!-- Read more button -->
                                    <a href="{{ route('books.show', $review->book->slug) }}#review-{{ $review->id }}" 
                                       class="inline-block bg-brand-500 hover:bg-brand-600 text-white text-xs font-medium px-4 py-2 rounded transition-colors duration-200">
                                        Читати далі
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <!-- No Reviews -->
                    <div class="flex-shrink-0 w-80 sm:w-96 md:w-[28rem] snap-start">
                        <div class="bg-dark-bg-secondary rounded-lg p-6 md:p-8 text-center h-full flex items-center justify-center">
                            <div>
                                <svg class="w-10 h-10 md:w-12 md:h-12 text-gray-400 mx-auto mb-3 md:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <p class="text-gray-400 text-xs md:text-sm">Поки що немає рецензій</p>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
                <!-- Scroll Arrow -->
                <button class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-light-bg dark:bg-dark-bg-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary text-light-text-secondary dark:text-dark-text-primary p-2 md:p-3 rounded-full transition-all duration-200 shadow-lg">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                </button>
            </div>
        </section>

        <!-- 4. Цитати -->
        <section class="mb-10 md:mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-light-text-primary dark:text-dark-text-primary mb-4 md:mb-6">Цитати</h2>
            <div class="relative">
                <div class="flex space-x-5 md:space-x-6 lg:space-x-8 overflow-x-auto pb-4 scrollbar-hide snap-x snap-mandatory">
                    @forelse($featuredQuotes as $quote)
                        <div class="flex-shrink-0 w-80 sm:w-96 snap-start">
                            <div class="bg-light-bg dark:bg-dark-bg-secondary rounded-lg p-5 md:p-6 hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-colors duration-200 h-full flex flex-col shadow-sm hover:shadow-md">
                                <div class="text-4xl md:text-5xl text-orange-500/30 mb-4 md:mb-5">"</div>
                                <p class="text-light-text-secondary dark:text-dark-text-secondary text-base md:text-lg lg:text-xl italic leading-relaxed mb-4 md:mb-5 flex-1">
                                    {{ $quote->content }}
                                </p>
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-auto gap-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-light-text-primary dark:text-dark-text-primary font-semibold text-base md:text-lg truncate">{{ $quote->user ? $quote->user->name : 'Анонімний автор' }}</p>
                                        <p class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm md:text-base truncate">{{ $quote->book_title ?? 'Без назви книги' }}</p>
                                    </div>
                                    <div class="flex items-center space-x-2 flex-shrink-0">
                                        <button class="text-orange-500 hover:text-orange-400 transition-colors">
                                            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                        </button>
                                        <span class="text-gray-600 dark:text-gray-400 text-sm md:text-base">{{ $quote->likes_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex-shrink-0 w-full text-center py-8">
                            <p class="text-light-text-tertiary dark:text-dark-text-tertiary">Поки що немає цитат</p>
                        </div>
                    @endforelse
                </div>
                <!-- Scroll Arrow -->
                <button class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-light-bg dark:bg-dark-bg-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary text-light-text-secondary dark:text-dark-text-primary p-2 rounded-full transition-colors duration-200 shadow-lg">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </section>


@push('styles')
<style>
/* Hide scrollbar for horizontal scroll */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-4 {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom hover state */
.hover\:bg-gray-750:hover {
    background-color: #374151;
}

/* Custom animations for hero banner */
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

@keyframes fade-in-up {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

@keyframes float-delayed {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-15px);
    }
}

@keyframes spin-slow {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes pulse-delayed {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* Animation classes */
.animate-blob {
    animation: blob 7s infinite;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 6s ease-in-out infinite;
    animation-delay: 2s;
}

.animate-spin-slow {
    animation: spin-slow 3s linear infinite;
}

.animate-pulse-delayed {
    animation: pulse-delayed 2s ease-in-out infinite;
    animation-delay: 1s;
}

/* Animation delays */
.animation-delay-200 {
    animation-delay: 0.2s;
}

.animation-delay-400 {
    animation-delay: 0.4s;
}

.animation-delay-600 {
    animation-delay: 0.6s;
}

.animation-delay-800 {
    animation-delay: 0.8s;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animation-delay-1000 {
    animation-delay: 1s;
}

.animation-delay-1200 {
    animation-delay: 1.2s;
}

/* Initial state for fade-in animations */
.animate-fade-in-up {
    opacity: 0;
}

/* Mobile scroll improvements */
@media (max-width: 768px) {
    .snap-x {
        scroll-snap-type: x mandatory;
    }
    
    .snap-start {
        scroll-snap-align: start;
    }
    
    /* Smooth scrolling for mobile */
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }
    
    /* Better touch targets for mobile */
    button, a {
        min-height: 48px;
        min-width: 48px;
    }
    
    /* Ensure buttons are easily tappable */
    .bg-brand-500, .bg-gradient-to-r {
        min-height: 48px;
    }
    
    /* Improved spacing for mobile */
    .space-x-3 > * + * {
        margin-left: 0.75rem;
    }
    
    .space-x-4 > * + * {
        margin-left: 1rem;
    }
}
</style>
@endpush

<script>
// Reviews scroll functionality
document.addEventListener('DOMContentLoaded', function() {
    const reviewsContainer = document.querySelector('.flex.space-x-4.md\\:space-x-6.overflow-x-auto');
    const scrollButton = document.querySelector('.absolute.right-0.top-1\\/2 button');
    
    if (reviewsContainer && scrollButton) {
        scrollButton.addEventListener('click', function() {
            // Calculate scroll distance based on screen size
            const isMobile = window.innerWidth < 640; // sm breakpoint
            const isTablet = window.innerWidth < 1024; // lg breakpoint
            let cardWidth, gap;
            
            if (isMobile) {
                cardWidth = 320; // w-80 = 320px
                gap = 16; // space-x-4 = 16px
            } else if (isTablet) {
                cardWidth = 384; // w-96 = 384px
                gap = 24; // space-x-6 = 24px
            } else {
                cardWidth = 448; // w-[28rem] = 448px
                gap = 24; // space-x-6 = 24px
            }
            
            reviewsContainer.scrollBy({
                left: cardWidth + gap,
                behavior: 'smooth'
            });
        });
    }
    
    // Add touch support for horizontal scrolling
    let isScrolling = false;
    const containers = document.querySelectorAll('.overflow-x-auto');
    
    containers.forEach(container => {
        container.addEventListener('touchstart', function() {
            isScrolling = true;
        });
        
        container.addEventListener('touchend', function() {
            isScrolling = false;
        });
        
        container.addEventListener('touchmove', function(e) {
            if (isScrolling) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection