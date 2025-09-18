@extends('layouts.app')

@section('title', $user->name . ' - Профіль користувача')

@section('main')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Profile Header -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-start lg:space-x-8">
                <!-- Left Side - Profile Info -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-6 mb-6 lg:mb-0">
                    <!-- Avatar -->
                    <div class="relative mb-4 sm:mb-0">
                        <img src="{{ $user->avatar ?: 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&size=120' }}" 
                             alt="{{ $user->name }}" 
                             class="w-24 h-24 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-lg">
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center">
                            <div class="w-3 h-3 bg-white rounded-full"></div>
                        </div>
                    </div>
                    
                    <!-- User Details -->
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $user->name }}</h1>
                        <p class="text-lg text-gray-600 dark:text-gray-400 mb-3">@{{ $user->username }}</p>
                        
                        @if($user->bio)
                        <p class="text-gray-700 dark:text-gray-300 mb-4 max-w-md">{{ $user->bio }}</p>
                        @endif
                        
                        <!-- Follow Stats -->
                        <div class="flex space-x-6 mb-4">
                            <div class="text-center">
                                <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['quotes_count'] }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Цитат</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['reviews_count'] }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Рецензий</div>
                            </div>
                        </div>
                        
                        <!-- Follow Button -->
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            + Подписаться
                        </button>
                    </div>
                </div>
                
                <!-- Right Side - Statistics -->
                <div class="flex-1 lg:max-w-md">
                    <!-- Category Tabs -->
                    <div class="flex space-x-1 mb-6">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">
                            Книги
                        </button>
                        <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                            Цитаты
                        </button>
                        <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                            Публикации
                        </button>
                    </div>
                    
                    <!-- Donut Chart -->
                    <div class="flex items-center space-x-6">
                        <div class="relative w-32 h-32">
                            <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 36 36">
                                <!-- Background circle -->
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                      fill="none" 
                                      stroke="#e5e7eb" 
                                      stroke-width="2"/>
                                <!-- Progress circle -->
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                                      fill="none" 
                                      stroke="#10b981" 
                                      stroke-width="2" 
                                      stroke-dasharray="75, 100"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['library_count'] }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Прочитано</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status List -->
                        <div class="flex-1 space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Читаю</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ rand(1, 10) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Запланировано</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ rand(5, 25) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Отложено</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ rand(1, 8) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Брошено</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ rand(0, 5) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Activity -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Activity Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Активность</h3>
                    <div class="h-32 flex items-end space-x-1">
                        @for($i = 0; $i < 30; $i++)
                        <div class="flex-1 bg-gradient-to-t from-purple-500 to-pink-400 rounded-sm" 
                             style="height: {{ rand(20, 100) }}%"></div>
                        @endfor
                    </div>
                </div>
                
                <!-- Reading Time -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Время чтения</h3>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ rand(1, 12) }} месяцев {{ rand(1, 30) }} дней</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ rand(500, 2000) }} часов</div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-400 h-2 rounded-full" 
                             style="width: {{ rand(30, 90) }}%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - History -->
            <div class="space-y-6">
                <!-- History -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">История</h3>
                    <div class="space-y-4">
                        @if($user->savedBooks->count() > 0)
                            @foreach($user->savedBooks->take(3) as $book)
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-16 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $book->title }}</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Прочитано {{ rand(50, 300) }} страниц</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">{{ rand(1, 24) }} часов назад</p>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">История пуста</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Favorites Section -->
        <div class="mt-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <!-- Favorites Tabs -->
                <div class="flex space-x-1 mb-6">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">
                        Книги
                    </button>
                    <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                        Цитаты
                    </button>
                    <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                        Публикации
                    </button>
                    <button class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                        Авторы
                    </button>
                </div>
                
                <!-- Favorites Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                    @if($user->savedBooks->count() > 0)
                        @foreach($user->savedBooks->take(16) as $book)
                        <div class="group cursor-pointer">
                            <div class="aspect-[3/4] bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-2 group-hover:shadow-lg transition-shadow duration-200">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h4 class="text-xs font-medium text-gray-900 dark:text-white text-center truncate">{{ Str::limit($book->title, 20) }}</h4>
                        </div>
                        @endforeach
                    @else
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400">Избранное пусто</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection