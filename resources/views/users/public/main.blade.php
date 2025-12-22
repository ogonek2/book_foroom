@extends('layouts.app')

@section('title', $user->name . ' - Публічний профіль')

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Tooltip animations */
    .group:hover .tooltip {
        opacity: 1;
        transform: translateY(-2px);
    }
    
    .tooltip {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateY(0);
    }
    
    /* Award hover effects */
    .award-icon {
        transition: all 0.2s ease-in-out;
    }
    
    .award-icon:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    /* Fade in animation */
    .animate-fade-in {
        animation: fadeIn 0.6s ease-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

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
    <div class="absolute hidden lg:block inset-0 from-indigo-600 via-purple-600 to-pink-600 overflow-hidden" style="height: 320px;">
        @if ($user->avatar)
            <div class="absolute inset-0">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover filter"
                    style="filter: blur(8px); scale: 1.1; opacity: 0.7;">
                <div class="absolute inset-0 bg-light-bg dark:bg-dark-bg opacity-50"></div>
            </div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600"></div>
        @endif
    </div>
@endsection

@section('main')
    <div class="relative flex flex-col gap-4 lg:flex-row lg:mt-[90px]">
        <div class="flex-1">
            <!-- Header with Avatar and Basic Info -->
            <div class="relative">
                <div class="container mx-auto">
                    <div class="flex items-start flex-col gap-4">
                        <div class="flex items-center gap-4">
                            <!-- Avatar -->
                            <div class="flex-shrink-0 relative">
                                @if ($user->avatar)
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                        class="w-[100px] h-[100px] md:h-[180px] lg:h-[180px] md:w-[180px] lg:w-[180px] rounded-xl object-cover shadow-lg">
                                @else
                                    <div class="w-40 h-40 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center border-2 border-gray-600 shadow-lg">
                                        <span class="text-2xl font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div>
                                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ '@' . $user->username }}</p>
                                </div>
                                <div class="hidden md:block lg:block">
                                    @if(isset($userAwards) && $userAwards->count() > 0)
                                        <div class="mt-4">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($userAwards->take(8) as $award)
                                                    <div class="relative group">
                                                        @if($award->image)
                                                            <img src="{{ $award->image }}" 
                                                                alt="{{ $award->name }}" 
                                                                class="w-10 h-10 rounded-full object-cover cursor-pointer border-2 border-white/20 shadow-md">
                                                        @else
                                                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm cursor-pointer border-2 border-white/20 shadow-md" 
                                                                style="background-color: {{ $award->color }}">
                                                                {{ substr($award->name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        
                                                        <!-- Tooltip -->
                                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-4 py-3 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-xl shadow-2xl opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none z-20 min-w-[200px] max-w-[280px]">
                                                            <div class="font-bold text-sm mb-1">{{ $award->name }}</div>
                                                            @if($award->description)
                                                                <div class="text-gray-300 text-xs leading-relaxed mb-2">{{ $award->description }}</div>
                                                            @endif
                                                            @if($award->points > 0)
                                                                <div class="flex items-center space-x-1 text-yellow-400 text-xs font-medium">
                                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                    </svg>
                                                                    <span>+{{ $award->points }} очок</span>
                                                                </div>
                                                            @endif
                                                            <!-- Arrow -->
                                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                
                                                @if($userAwards->count() > 8)
                                                    <a href="{{ route('users.public.awards', $user->username) }}" class="relative group">
                                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm cursor-pointer border-2 border-white/20 shadow-md">
                                                            +{{ $userAwards->count() - 8 }}
                                                        </div>
                                                        
                                                        <!-- Tooltip for more -->
                                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-4 py-3 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-xl shadow-2xl opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none z-20 min-w-[180px]">
                                                            <div class="font-bold text-sm mb-1">Ще {{ $userAwards->count() - 8 }} нагород</div>
                                                            <div class="text-gray-300 text-xs">Натисніть, щоб побачити всі нагороди</div>
                                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></div>
                                                        </div>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                            <div class="block md:hidden lg:hidden">
                                @if(isset($userAwards) && $userAwards->count() > 0)
                                    <div class="mt-4">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($userAwards->take(8) as $award)
                                                <div class="relative group">
                                                    @if($award->image)
                                                        <img src="{{ $award->image }}" 
                                                            alt="{{ $award->name }}" 
                                                            class="w-10 h-10 rounded-full object-cover cursor-pointer border-2 border-white/20 shadow-md">
                                                    @else
                                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm cursor-pointer border-2 border-white/20 shadow-md" 
                                                            style="background-color: {{ $award->color }}">
                                                            {{ substr($award->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Tooltip -->
                                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-4 py-3 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-xl shadow-2xl opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none z-20 min-w-[200px] max-w-[280px]">
                                                        <div class="font-bold text-sm mb-1">{{ $award->name }}</div>
                                                        @if($award->description)
                                                            <div class="text-gray-300 text-xs leading-relaxed mb-2">{{ $award->description }}</div>
                                                        @endif
                                                        @if($award->points > 0)
                                                            <div class="flex items-center space-x-1 text-yellow-400 text-xs font-medium">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                </svg>
                                                                <span>+{{ $award->points }} очок</span>
                                                            </div>
                                                        @endif
                                                        <!-- Arrow -->
                                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            
                                            @if($userAwards->count() > 8)
                                                <a href="{{ route('users.public.awards', $user->username) }}" class="relative group">
                                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm cursor-pointer border-2 border-white/20 shadow-md">
                                                        +{{ $userAwards->count() - 8 }}
                                                    </div>
                                                    
                                                    <!-- Tooltip for more -->
                                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-4 py-3 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-xl shadow-2xl opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none z-20 min-w-[180px]">
                                                        <div class="font-bold text-sm mb-1">Ще {{ $userAwards->count() - 8 }} нагород</div>
                                                        <div class="text-gray-300 text-xs">Натисніть, щоб побачити всі нагороди</div>
                                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900 dark:border-t-gray-700"></div>
                                                    </div>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
            <!-- Main Content Grid -->
            <div class="container mx-auto py-6">
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
                                @if(isset($userAwards) && $userAwards->count() > 0)
                                <a href="{{ route('users.public.awards', $user->username) }}"
                                    class="px-4 py-2 {{ isActiveRoute('users.public.awards', $currentRoute) ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }} rounded-lg text-sm font-medium transition-all">
                                    Нагороди
                                </a>
                                @endif
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
                            @if(isset($userAwards) && $userAwards->sum('points') > 0)
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Очки нагород</span>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $userAwards->sum('points') }}</span>
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
                                @if(isset($userAwards) && $userAwards->count() > 0)
                                <div class="text-center">
                                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $userAwards->count() }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Нагород</div>
                                </div>
                                @endif
                            </div>
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
                                    <img src="{{ $readingStatus->book->cover_image }}" alt="{{ $readingStatus->book->title }}">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('books.show', $readingStatus->book->slug) }}">
                                        <h4 class="text-sm font-medium text-purple-500">
                                        {{ Str::limit($readingStatus->book->title, 30) }}</h4>
                                    </a>
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
