@extends('layouts.app')

@section('title', 'Автори - Книжковий форум')

@section('description', 'Каталог авторів на FOXY. Знайдіть улюблених авторів, перегляньте їхні книги та рецензії.')
@section('keywords', 'автори, письменники, книги, рецензії, FOXY')
@section('canonical', route('authors.index'))
@section('og_type', 'website')
@section('og_title', 'Автори - FOXY')
@section('og_description', 'Каталог авторів на FOXY. Знайдіть улюблених авторів, перегляньте їхні книги та рецензії.')
@section('og_url', route('authors.index'))
@section('og_image', asset('favicon.svg'))
@section('twitter_title', 'Автори - FOXY')
@section('twitter_description', 'Каталог авторів на FOXY. Знайдіть улюблених авторів, перегляньте їхні книги та рецензії.')
@section('twitter_image', asset('favicon.svg'))

@section('main')
    <div class="content-with-skeleton relative min-h-[480px]">
    <div id="authors-page" class="min-h-screen bg-light-bg dark:bg-dark-bg transition-colors duration-300" v-cloak>
        <!-- Header -->
        <div class="mb-2">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Автори</h1>
                    <p class="text-gray-600 dark:text-gray-400">Відкрийте для себе цікавих авторів</p>
                </div>
                <!-- Mobile Filter Button -->
                <button @@click="showMobileFilters = true" 
                    class="lg:hidden flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold transition-colors shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>Фільтри</span>
                </button>
            </div>
        </div>
        <!-- Main Content -->
        <div class="max-w-8xl mx-auto py-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sticky Sidebar (Desktop) -->
                <div class="hidden lg:block lg:w-80 flex-shrink-0">
                    <div class="sticky top-24 max-h-screen overflow-y-auto">
                        <!-- Unified Sidebar Container -->
                        <div
                            class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-6">
                            <!-- Main Header -->
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Фільтри</h3>

                            <!-- Search Section -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Пошук</label>
                                <form method="GET" action="{{ route('authors.index') }}">
                                    <div class="relative">
                                        <input type="text" name="search" value="{{ request('search') }}"
                                            placeholder="Назва, автор..."
                                            class="w-full px-4 py-3 border-0 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-gray-600 transition-all duration-200">
                                    </div>
                                </form>
                            </div>

                            <!-- Alphabet Filter Section -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Фільтр за прізвищем</label>
                                <div class="grid grid-cols-6 gap-2">
                                    <a href="{{ route('authors.index', request()->except('letter')) }}"
                                        class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full {{ !request('letter') ? 'bg-gradient-to-r from-brand-500 to-accent-500 text-white scale-110' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600' }} transition-all duration-200">
                                        Усі
                                    </a>
                                    @foreach ($letters as $letter)
                                        <a href="{{ route('authors.index', array_merge(request()->except('letter'), ['letter' => $letter])) }}"
                                            class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full {{ request('letter') == $letter ? 'bg-gradient-to-r from-brand-500 to-accent-500 text-white scale-110' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600' }} transition-all duration-200">
                                            {{ $letter }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Authors Grid -->
                <div class="flex-1 min-w-0">
                    @if ($authors->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                            @foreach ($authors as $author)
                                <div
                                    class="group relative bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-xs border border-gray-200/30 dark:border-gray-700/30 overflow-hidden hover:shadow-xl transition-all duration-300">
                                    <a href="{{ route('authors.show', $author) }}" class="block">
                                        <!-- Card Container -->
                                        <div class="aspect-[3/4] relative">
                                            <img src="{{ $author->photo_url }}" alt="{{ $author->full_name ?? 'Автор' }}"
                                                class="w-full h-full object-cover"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                            <!-- Icon Fallback (only shown when image fails to load) -->
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800"
                                                style="display: none;">
                                                <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>

                                            <!-- Dark Overlay -->
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent dark:from-black/80 dark:via-black/20">
                                            </div>

                                            <!-- Books Count -->
                                            <div class="absolute top-4 right-4 z-20">
                                                <div
                                                    class="flex items-center space-x-1 bg-white/80 dark:bg-black/70 rounded-full px-2 py-1.5">
                                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                    <span
                                                        class="text-gray-600 dark:text-gray-400 text-sm font-medium">{{ $author->books_count ?? 0 }}</span>
                                                </div>
                                            </div>

                                            <!-- Author Info -->
                                            <div class="absolute left-0 right-0 z-10 p-4"
                                                style="bottom: 0rem; background: linear-gradient(to top, rgba(0, 0, 0, 0.863) 0%, rgba(0, 0, 0, 0.603) 67.31%, rgba(0,0,0,0) 100%);">
                                                <h6
                                                    class="text-l font-bold text-white group-hover:text-purple-200 transition-colors line-clamp-2">
                                                    {{ $author->full_name ?? ($author->name ?? 'Автор #' . ($author->id ?? '?')) }}
                                                </h6>
                                            </div>

                                            <!-- Hover Button -->
                                            <div class="absolute top-4 left-4 z-20">
                                                <div
                                                    class="flex items-center space-x-1 bg-gradient-to-r from-brand-500 to-accent-500 rounded-full px-2 py-1.5">
                                                    <i class="fa fa-arrow-right"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-12">
                            {{ $authors->appends(request()->query())->links('vendor.pagination.custom') }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="max-w-md mx-auto">
                                <svg class="mx-auto h-24 w-24 text-light-text-tertiary dark:text-dark-text-tertiary mb-4"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <h3 class="text-xl font-semibold text-light-text-primary dark:text-dark-text-primary mb-2">
                                    Автори не знайдені
                                </h3>
                                <p class="text-light-text-secondary dark:text-dark-text-secondary mb-6">
                                    Спробуйте змінити параметри пошуку або фільтри
                                </p>
                                <a href="{{ route('authors.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg font-medium transition-colors duration-200">
                                    Скинути фільтри
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mobile Filters Sidebar -->
        <transition name="fade">
            <div v-if="showMobileFilters" 
                class="lg:hidden fixed inset-0 z-50 overflow-hidden"
                @@click.self="showMobileFilters = false">
                <!-- Overlay -->
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                    @@click="showMobileFilters = false"></div>
                
                <!-- Sidebar -->
                <transition name="slide-left">
                    <div v-if="showMobileFilters"
                        class="absolute right-0 top-0 h-full w-full max-w-sm bg-white dark:bg-gray-800 shadow-2xl overflow-y-auto">
                        <!-- Header -->
                        <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between z-10">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Фільтри</h2>
                            <button @@click="showMobileFilters = false"
                                class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Filters Content -->
                        <div class="p-6 space-y-6">
                            <!-- Search Section -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Пошук</label>
                                <form method="GET" action="{{ route('authors.index') }}">
                                    <div class="relative">
                                        <input type="text" name="search" value="{{ request('search') }}"
                                            placeholder="Назва, автор..."
                                            class="w-full px-4 py-3 border-0 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-gray-600 transition-all duration-200">
                                    </div>
                                </form>
                            </div>

                            <!-- Alphabet Filter Section -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Фільтр за прізвищем</label>
                                <div class="grid grid-cols-6 gap-2">
                                    <a href="{{ route('authors.index', request()->except('letter')) }}"
                                        class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full {{ !request('letter') ? 'bg-gradient-to-r from-brand-500 to-accent-500 text-white scale-110' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600' }} transition-all duration-200">
                                        Усі
                                    </a>
                                    @foreach ($letters as $letter)
                                        <a href="{{ route('authors.index', array_merge(request()->except('letter'), ['letter' => $letter])) }}"
                                            class="w-10 h-10 flex items-center justify-center text-sm font-medium rounded-full {{ request('letter') == $letter ? 'bg-gradient-to-r from-brand-500 to-accent-500 text-white scale-110' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600' }} transition-all duration-200">
                                            {{ $letter }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Apply Button -->
                            <div class="sticky bottom-0 pt-4 pb-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 -mx-6 px-6">
                                <button @@click="showMobileFilters = false"
                                    class="w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold transition-colors shadow-lg">
                                    Застосувати фільтри
                                </button>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </div>

    <!-- Skeleton: сітка авторів -->
    <div class="skeleton-placeholder pointer-events-none" aria-hidden="true">
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="hidden lg:block lg:w-80 flex-shrink-0"><div class="h-64 skeleton rounded-2xl"></div></div>
            <div class="flex-1">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                    @for($i = 0; $i < 8; $i++)
                    <div class="rounded-2xl overflow-hidden border border-gray-200/30 dark:border-gray-700/30">
                        <div class="skeleton aspect-[3/4] w-full"></div>
                        <div class="p-3"><div class="skeleton h-5 w-full rounded"></div></div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Vue) {
                new Vue({
                    el: '#authors-page',
                    data: {
                        showMobileFilters: false
                    }
                });
            }
        });
    </script>
    @endpush

    @push('styles')
    <style>
        .slide-left-enter-active {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .slide-left-leave-active {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .slide-left-enter {
            transform: translateX(100%);
        }

        .slide-left-enter-to {
            transform: translateX(0);
        }

        .slide-left-leave {
            transform: translateX(0);
        }

        .slide-left-leave-to {
            transform: translateX(100%);
        }
    </style>
    @endpush
@endsection