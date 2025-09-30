@extends('layouts.app')

@section('content')
<div class="bg-gray-900 -mx-4 sm:-mx-6 lg:-mx-8 -my-8">
    <!-- Breadcrumbs -->

    <!-- Profile Header -->
    <div class="relative">
        <!-- Cover Image with Blur Effect -->
        <div class="relative bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 overflow-hidden" style="height: 200px;">
            @if($user->avatar)
                <div class="absolute inset-0">
                    <img src="{{ Storage::url($user->avatar) }}" 
                         alt="{{ $user->name }}" 
                         class="w-full h-full object-cover filter" style="filter: blur(4px); scale: 1.1;">
                    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                </div>
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500 via-pink-500 to-purple-600"></div>
            @endif
            
            <!-- Bottom inner shadow -->
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-60"></div>
        </div>

        <!-- Profile Info Container -->
        <div class="container mx-auto" style="margin-top: -120px;">
                    <ol class="flex items-center space-x-2 mb-4 text-gray-400 relative z-100">
                        <li>
                            <a href="{{ route('home') }}" class="hover:text-orange-400 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </li>
                        <li>
                            <a href="{{ route('profile.show', $user->username) }}" class="hover:text-orange-400 transition-colors">
                                {{ $user->name }}
                            </a>
                        </li>
                        @if(!request()->routeIs('profile.show') && !request()->routeIs('profile.user.show'))
                            <li>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </li>
                            <li class="text-white">
                                @if(request()->routeIs('profile.library'))
                                    Бібліотека
                                @elseif(request()->routeIs('profile.reviews'))
                                    Рецензії
                                @elseif(request()->routeIs('profile.discussions'))
                                    Обговорення
                                @elseif(request()->routeIs('profile.quotes'))
                                    Цитати
                                @elseif(request()->routeIs('profile.collections'))
                                    Добірки
                                @endif
                            </li>
                        @endif
                    </ol>
            <div class="flex gap-6">
                <!-- Left: Avatar & Navigation -->
                 
                <div style="width: 200px;">
                    <!-- Avatar -->
                    <div class="relative mb-4">
                        
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" 
                                alt="{{ $user->name }}" 
                                class="w-full rounded-lg object-cover border-4 border-white shadow-2xl" 
                                style="aspect-ratio: 1/1;">
                        @else
                            <div class="w-full rounded-lg bg-gradient-to-br from-orange-500 to-pink-600 flex items-center justify-center border-4 border-white shadow-2xl" style="aspect-ratio: 1/1;">
                                <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Stats below avatar -->
                    <div class="flex justify-around mb-4 text-gray-300 text-sm space-x-4">
                        <div class="text-center">
                            <div class="font-bold">{{ $user->topics->count() + $user->posts->count() }}</div>
                            <div class="text-xs">Пости</div>
                        </div>
                        <div class="text-center">
                            <div class="font-bold">{{ $user->reviews->count() }}</div>
                            <div class="text-xs">Рецензії</div>
                        </div>
                    </div>

                    <!-- Navigation Menu -->
                    <nav class="rounded-lg bg-gray-800 overflow-hidden p-1">
                        <a href="{{ route('profile.show', $user->username) }}" 
                           class="rounded-lg flex items-center space-x-3 px-4 py-3 transition-all {{ request()->routeIs('profile.show') || request()->routeIs('profile.user.show') ? 'bg-gradient-to-r from-orange-500 to-pink-500 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Профіль</span>
                        </a>
                        <a href="{{ route('profile.library', $user->username) }}" 
                           class="rounded-lg flex items-center space-x-3 px-4 py-3 transition-all {{ request()->routeIs('profile.library') ? 'bg-gradient-to-r from-orange-500 to-pink-500 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span>Бібліотека</span>
                        </a>
                        <a href="{{ route('profile.reviews', $user->username) }}" 
                           class="rounded-lg flex items-center space-x-3 px-4 py-3 transition-all {{ request()->routeIs('profile.reviews') ? 'bg-gradient-to-r from-orange-500 to-pink-500 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span>Рецензії</span>
                        </a>
                        <a href="{{ route('profile.discussions', $user->username) }}" 
                           class="rounded-lg flex items-center space-x-3 px-4 py-3 transition-all {{ request()->routeIs('profile.discussions') ? 'bg-gradient-to-r from-orange-500 to-pink-500 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                            </svg>
                            <span>Обговорення</span>
                        </a>
                        <a href="{{ route('profile.quotes', $user->username) }}" 
                           class="rounded-lg flex items-center space-x-3 px-4 py-3 transition-all {{ request()->routeIs('profile.quotes') ? 'bg-gradient-to-r from-orange-500 to-pink-500 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                            <span>Цитати</span>
                        </a>
                        <a href="{{ route('profile.collections', $user->username) }}" 
                           class="rounded-lg flex items-center space-x-3 px-4 py-3 transition-all {{ request()->routeIs('profile.collections') ? 'bg-gradient-to-r from-orange-500 to-pink-500 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span>Добірки</span>
                        </a>
                    </nav>
                </div>

                <!-- Center: User Info & Content -->
                <div class="flex-1 z-10">
                    <!-- User Name & Bio (overlapping header) -->
                    <div class="text-white mb-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h1 class="text-4xl font-bold mb-2 drop-shadow-lg">{{ $user->name }}</h1>
                                <p class="text-xl text-gray-200 drop-shadow-md">@ {{ $user->username }}</p>
                            </div>
                            
                            @if(auth()->check() && auth()->id() === $user->id)
                                <!-- Profile Management Tools -->
                                <div class="flex items-center space-x-3">
                                    <!-- Edit Profile Button -->
                                    <a href="{{ route('profile.edit') }}" 
                                       class="flex items-center space-x-2 bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-all shadow-lg opacity-50 hover:opacity-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span class="font-medium">Редагувати</span>
                                    </a>
                                    
                                    <!-- More Actions Menu -->
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" 
                                                class="flex items-center justify-center w-10 h-10 bg-gray-800 hover:bg-gray-700 text-white rounded-lg transition-all shadow-lg">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                            </svg>
                                        </button>
                                        
                                        <div x-show="open" 
                                             @click.away="open = false"
                                             x-transition
                                             style="display: none;"
                                             class="absolute right-0 mt-2 w-64 bg-gray-800 rounded-lg shadow-xl py-2 z-50 border border-gray-700">
                                            <div class="px-4 py-2 border-b border-gray-700">
                                                <p class="text-xs text-gray-400 uppercase">Керування профілем</p>
                                            </div>
                                            
                                            <a href="{{ route('profile.edit') }}" 
                                               class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <div>
                                                    <div class="font-medium">Особисті дані</div>
                                                    <div class="text-xs text-gray-400">Ім'я, email, біо</div>
                                                </div>
                                            </a>
                                            
                                            <a href="{{ route('profile.edit') }}#avatar" 
                                               class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <div>
                                                    <div class="font-medium">Аватар</div>
                                                    <div class="text-xs text-gray-400">Змінити фото профілю</div>
                                                </div>
                                            </a>
                                            
                                            <a href="{{ route('dashboard') }}" 
                                               class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                </svg>
                                                <div>
                                                    <div class="font-medium">Статистика</div>
                                                    <div class="text-xs text-gray-400">Детальна статистика</div>
                                                </div>
                                            </a>
                                            
                                            <div class="border-t border-gray-700 my-2"></div>
                                            
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" 
                                                        class="flex items-center space-x-3 px-4 py-3 text-red-400 hover:bg-gray-700 hover:text-red-300 transition-colors w-full text-left">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                    </svg>
                                                    <div>
                                                        <div class="font-medium">Вийти</div>
                                                        <div class="text-xs text-gray-400">Вийти з облікового запису</div>
                                                    </div>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        @if(request()->routeIs('profile.show') || request()->routeIs('profile.user.show'))
                            <!-- Achievement Badges -->
                            <div class="flex space-x-2 mb-4">
                                @for($i = 0; $i < 6; $i++)
                                    <div class="w-10 h-10 rounded-full border-2 border-{{ ['green', 'gray', 'yellow', 'blue', 'purple', 'pink'][$i] }}-500 flex items-center justify-center bg-gray-800">
                                        <svg class="w-5 h-5 text-{{ ['green', 'gray', 'yellow', 'blue', 'purple', 'pink'][$i] }}-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </div>
                                @endfor
                            </div>
                            
                            @if($user->bio)
                                <div class="max-w-3xl">
                                    <div id="bio-collapsed">
                                        <p class="text-gray-200 leading-relaxed">
                                            {{ Str::limit($user->bio, 150) }}
                                        </p>
                                        @if(strlen($user->bio) > 150)
                                            <button onclick="toggleBio()" 
                                                    class="text-orange-400 hover:text-orange-300 text-sm mt-2 transition-colors flex items-center space-x-1">
                                                <span>Розгорнути</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <div id="bio-expanded" style="display: none;">
                                        <p class="text-gray-200 leading-relaxed">{{ $user->bio }}</p>
                                        <button onclick="toggleBio()" 
                                                class="text-orange-400 hover:text-orange-300 text-sm mt-2 transition-colors flex items-center space-x-1">
                                            <span>Згорнути</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <script>
                                    function toggleBio() {
                                        const collapsed = document.getElementById('bio-collapsed');
                                        const expanded = document.getElementById('bio-expanded');
                                        
                                        if (collapsed.style.display === 'none') {
                                            collapsed.style.display = 'block';
                                            expanded.style.display = 'none';
                                        } else {
                                            collapsed.style.display = 'none';
                                            expanded.style.display = 'block';
                                        }
                                    }
                                    </script>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Main Content -->
                    <div>
                        @yield('profile-content')
                    </div>
                </div>

                <!-- Right: Stats Panel (overlapping header) -->
                <div style="width: 320px;" class="z-10">
                    <div class="bg-gray-800 rounded-lg p-6 shadow-2xl">
                        @php
                            $readingStats = [
                                'read_count' => $user->readingStatuses()->where('status', 'read')->count(),
                                'reading_count' => $user->readingStatuses()->where('status', 'reading')->count(),
                                'want_to_read_count' => $user->readingStatuses()->where('status', 'want_to_read')->count(),
                                'average_rating' => $user->readingStatuses()->where('status', 'read')->whereNotNull('rating')->avg('rating'),
                            ];
                            
                            $currentlyReading = $user->readingStatuses()
                                ->where('status', 'reading')
                                ->with('book')
                                ->orderBy('started_at', 'desc')
                                ->limit(3)
                                ->get();
                        @endphp
                        
                        <!-- Status Badge -->
                        <div class="flex items-center justify-between mb-6">
                            <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">Бібліоман</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-green-400 text-sm">online</span>
                            </div>
                        </div>
                        
                        <!-- Currently Reading -->
                        <div class="mb-6">
                            <h3 class="text-white font-medium mb-3">Читає</h3>
                            @if($currentlyReading->count() > 0)
                                <div class="space-y-2">
                                    @foreach($currentlyReading as $reading)
                                        <a href="{{ route('books.show', $reading->book->slug) }}" 
                                           class="block text-cyan-400 hover:text-cyan-300 text-sm transition-colors">
                                            {{ Str::limit($reading->book->title, 25) }}
                                        </a>
                                    @endforeach
                                    @if($readingStats['reading_count'] > 3)
                                        <span class="text-cyan-400 text-sm">та ще {{ $readingStats['reading_count'] - 3 }} книг</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">Немає активних книг</span>
                            @endif
                        </div>
                        
                        <!-- Ratings Section -->
                        <div class="pt-4">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-white font-medium">Оцінки</h3>
                                <span class="text-gray-300">{{ $readingStats['read_count'] }}</span>
                            </div>
                            
                            @if($readingStats['average_rating'])
                                <div class="flex items-center mb-4">
                                    <div class="flex space-x-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= $readingStats['average_rating']/2 ? 'text-yellow-400' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-white font-bold">{{ number_format($readingStats['average_rating'], 1) }}/10</span>
                                </div>

                                <!-- Rating Distribution (mini chart) -->
                                <div class="space-y-1">
                                    @for($rating = 10; $rating >= 1; $rating--)
                                        @php
                                            $count = $user->readingStatuses()
                                                ->where('status', 'read')
                                                ->where('rating', $rating)
                                                ->count();
                                            $maxCount = max(1, $readingStats['read_count']);
                                            $percentage = ($count / $maxCount) * 100;
                                        @endphp
                                        <div class="flex items-center text-xs">
                                            <span class="w-4 text-gray-400">{{ $rating }}</span>
                                            <div class="flex-1 bg-gray-700 rounded-full h-2 ml-2">
                                                <div class="bg-yellow-400 h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            @else
                                <p class="text-gray-400 text-sm">Ще немає оцінок</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@endsection