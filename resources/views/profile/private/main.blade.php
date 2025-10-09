@extends('layouts.app')

@section('title', 'Мій профіль')

@php
    // Функция для определения активного состояния навигации
    $currentTab = request('tab', 'overview');
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
                        <div class="flex-shrink-0 relative">
                            @if ($user->avatar)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                    class="w-full h-full rounded-full object-cover border-2 border-gray-600 shadow-lg"
                                    style="width: 200px; height: 200px;">
                            @else
                                <div class="w-40 h-40 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center border-2 border-gray-600 shadow-lg">
                                    <span class="text-2xl font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            
                            <!-- Edit Avatar Button -->
                            <button onclick="openAvatarModal()" 
                                    class="absolute bottom-2 right-2 w-10 h-10 bg-purple-500 hover:bg-purple-600 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-300 transform hover:scale-110">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                        </div>

                        <!-- User Info -->
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $user->name }}</h1>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ '@' . $user->username }}</p>

                            <!-- Follow Stats -->
                            <div class="flex space-x-6 mb-4">
                                <div class="text-center">
                                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['total_discussions'] ?? 0 }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Постів</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['total_reviews'] ?? 0 }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Рецензій</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['total_libraries'] ?? 0 }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">Добірок</div>
                                </div>
                            </div>

                            <!-- Edit Button -->
                            <button onclick="openEditProfileModal()" 
                                class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Редагувати профіль
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
                                <a href="{{ route('profile.show') }}"
                                    class="px-4 py-2 {{ $currentTab === 'overview' ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }} rounded-lg text-sm font-medium transition-all">
                                    Профіль
                                </a>
                                <a href="{{ route('profile.show') }}?tab=library"
                                    class="px-4 py-2 {{ $currentTab === 'library' ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }} rounded-lg text-sm font-medium transition-all">
                                    Бібліотека
                                </a>
                                <a href="{{ route('profile.show') }}?tab=reviews"
                                    class="px-4 py-2 {{ $currentTab === 'reviews' ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }} rounded-lg text-sm font-medium transition-all">
                                    Рецензії
                                </a>
                                <a href="{{ route('profile.show') }}?tab=discussions"
                                    class="px-4 py-2 {{ $currentTab === 'discussions' ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }} rounded-lg text-sm font-medium transition-all">
                                    Обговорення
                                </a>
                                <a href="{{ route('profile.show') }}?tab=quotes"
                                    class="px-4 py-2 {{ $currentTab === 'quotes' ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }} rounded-lg text-sm font-medium transition-all">
                                    Цитати
                                </a>
                                <a href="{{ route('profile.show') }}?tab=collections"
                                    class="px-4 py-2 {{ $currentTab === 'collections' ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }} rounded-lg text-sm font-medium transition-all">
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
            <div class="rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-900 dark:text-white font-semibold">Статистика читання</h3>
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </div>

                <!-- Progress Circle -->
                <div class="flex items-center space-x-6 mb-6">
                    @php
                        $readCount = $user->bookReadingStatuses()->where('status', 'read')->count();
                        $readingCount = $user->bookReadingStatuses()->where('status', 'reading')->count();
                        $wantToReadCount = $user->bookReadingStatuses()->where('status', 'want_to_read')->count();
                        $totalBooks = max(1, $readCount + $readingCount + $wantToReadCount);
                        $readPercentage = ($readCount / $totalBooks) * 100;
                    @endphp
                    
                    <div class="relative w-32 h-32">
                        <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                fill="none" 
                                stroke="#374151" 
                                stroke-width="2"/>
                            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                fill="none" 
                                stroke="#10b981" 
                                stroke-width="2" 
                                stroke-dasharray="{{ $readPercentage }}, 100"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $readCount }}</div>
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
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $readingCount }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Планує</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $wantToReadCount }}</span>
                        </div>
                        @if ($stats['average_rating'])
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Середня оцінка</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($stats['average_rating'], 1) }}/10</span>
                        </div>
                        @endif
                    </div>
                </div>
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
                    @php
                        $recentReadBooks = $user->bookReadingStatuses()
                            ->with('book')
                            ->orderBy('updated_at', 'desc')
                            ->limit(3)
                            ->get();
                    @endphp
                    
                    @if ($recentReadBooks->count() > 0)
                        @foreach ($recentReadBooks as $readingStatus)
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

    @push('scripts')
        <script>
            function openAvatarModal() {
                // TODO: Implement avatar editing modal
                console.log('Open avatar modal');
            }

            function openEditProfileModal() {
                window.location.href = '{{ route("profile.edit") }}';
            }
        </script>
    @endpush
@endsection
