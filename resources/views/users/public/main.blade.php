@extends('layouts.app')

@section('title', $user->name . ' - Публічний профіль')

@php
    // Функция для определения активного состояния навигации
    function isActiveRoute($routeName, $currentRoute)
    {
        return $currentRoute === $routeName;
    }

    $currentRoute = Route::currentRouteName();
@endphp

@section('profile-banner')
    <!-- Cover Image with Gradient Overlay -->
    <div class="absolute inset-0 from-indigo-600 via-purple-600 to-pink-600 overflow-hidden" style="height: 320px;">
        @if ($user->avatar)
            <div class="absolute inset-0">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover filter"
                    style="filter: blur(8px); scale: 1.1; opacity: 0.7;">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
            </div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600"></div>
        @endif

        <!-- Decorative Elements -->
        <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
            <div class="absolute top-32 right-20 w-32 h-32 bg-pink-500/20 rounded-full blur-2xl"></div>
            <div class="absolute bottom-20 left-1/3 w-16 h-16 bg-purple-500/30 rounded-full blur-lg"></div>
        </div>
    </div>
@endsection

@section('main')
    <div class="relative flex flex-col lg:flex-row" style="margin-top: 90px;">
        <div class="flex-1">
            <!-- Header with Avatar and Basic Info -->
            <div class="relative">
                <div class="container mx-auto">
                    <div class="flex items-start space-x-6 flex-col sm:flex-row space-y-6 lg:space-y-0">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            @if ($user->avatar)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                    class="w-full h-full rounded-full object-cover border-2 border-gray-600 shadow-lg"
                                    style="width: 200px; height: 200px;">
                            @else
                                <div
                                    class="w-40 h-40 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center border-2 border-gray-600 shadow-lg">
                                    <span class="text-2xl font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- User Info -->
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $user->name }}</h1>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ '@' . $user->username }}</p>

                            <!-- Follow Stats -->
                            <div class="flex space-x-6 mb-4">
                                <div class="text-center">
                                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['discussions_count'] }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Постів</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['reviews_count'] }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Рецензій</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['quotes_count'] }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Цитат</div>
                                </div>
                            </div>

                            <!-- Follow Button -->
                            <button
                                class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                                + Відстежувати
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="container mx-auto px-4 py-6">
                <div>
                    <!-- Left Column - Main Content -->
                    <div class="space-y-6">
                        <!-- Favorites Section -->
                        <div class="rounded-lg">
                            <!-- Navigation Tabs -->
                            <div>
                                @if ($user->bio)
                                    <p class="text-gray-700 dark:text-gray-300 mb-4 max-w-lg leading-relaxed text-sm">{{ $user->bio }}</p>
                                @endif
                            </div>
                            <div class="flex gap-2 mb-6" style="flex-wrap: wrap;">
                                <a href="{{ route('users.public.profile', $user->username) }}"
                                    class="px-4 py-2 {{ isActiveRoute('users.public.profile', $currentRoute) ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }} rounded-lg text-sm font-medium transition-all">
                                    Профіль
                                </a>
                                <a href="{{ route('users.public.library', $user->username) }}"
                                    class="px-4 py-2 {{ isActiveRoute('users.public.library', $currentRoute) ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }} rounded-lg text-sm font-medium transition-all">
                                    Бібліотека
                                </a>
                                <a href="{{ route('users.public.reviews', $user->username) }}"
                                    class="px-4 py-2 {{ isActiveRoute('users.public.reviews', $currentRoute) ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }} rounded-lg text-sm font-medium transition-all">
                                    Рецензії
                                </a>
                                <a href="{{ route('users.public.discussions', $user->username) }}"
                                    class="px-4 py-2 {{ isActiveRoute('users.public.discussions', $currentRoute) ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }} rounded-lg text-sm font-medium transition-all">
                                    Обговорення
                                </a>
                                <a href="{{ route('users.public.quotes', $user->username) }}"
                                    class="px-4 py-2 {{ isActiveRoute('users.public.quotes', $currentRoute) ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }} rounded-lg text-sm font-medium transition-all">
                                    Цитати
                                </a>
                                <a href="{{ route('users.public.collections', $user->username) }}"
                                    class="px-4 py-2 {{ isActiveRoute('users.public.collections', $currentRoute) ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }} rounded-lg text-sm font-medium transition-all">
                                    Добірки
                                </a>
                            </div>

                            <!-- Content Area -->
                            @yield('profile-content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Reading Stats Sidebar -->
        <div class="w-full h-full lg:w-80 px-6 bg-white backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl dark:bg-gray-900">
            @if(isset($stats['read_count']) && $stats['read_count'] !== null)
                <div class="rounded-lg">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-900 dark:text-white font-semibold">Статистика читання</h3>
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </div>

                    <!-- Progress Circle -->
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="relative w-28 h-28">
                            <svg class="w-28 h-28 transform -rotate-90" viewBox="0 0 36 36">
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                    fill="none" 
                                    stroke="#374151" 
                                    stroke-width="2"/>
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                    fill="none" 
                                    stroke="#10b981" 
                                    stroke-width="2" 
                                    stroke-dasharray="{{ ($stats['read_count'] / max(1, $stats['read_count'] + $stats['reading_count'] + $stats['want_to_read_count'])) * 100 }}, 100"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['read_count'] }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Прочитано</div>
                                </div>
                            </div>
                        </div>

                        <!-- Status List -->
                        <div class="flex-1 space-y-2">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Читає</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $stats['reading_count'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Планує</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $stats['want_to_read_count'] }}</span>
                            </div>
                            @if ($stats['average_rating'])
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Середня оцінка</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($stats['average_rating'], 1) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Privacy Message -->
                <div class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Статистика прихована</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Користувач приховав свою статистику читання</p>
                </div>
            @endif

            <!-- History -->
            <div class="mt-6">
                <div class="flex items-center space-x-3 mb-4">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    <h3 class="text-gray-900 dark:text-white font-semibold">Історія</h3>
                </div>
                <div class="space-y-4">
                    @if (isset($recentReadBooks) && $recentReadBooks->count() > 0)
                        @foreach ($recentReadBooks->take(3) as $readingStatus)
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-16 bg-gray-300 dark:bg-gray-700 rounded flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ Str::limit($readingStatus->book->title, 30) }}</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ ucfirst($readingStatus->status) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">
                                        {{ $readingStatus->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 dark:text-gray-500">Історія пуста</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
