@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900">
    <!-- Main Layout with Sidebar -->
    <div class="flex">
        <!-- Left Sidebar -->

        <!-- Main Content Area -->
        <div class="flex-1">
            <!-- Profile Header -->
            <div class="relative">
                <!-- Cover Image with Blur Effect -->
                <div class="relative bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 overflow-hidden" style="height: 200px;">
                    @if($user->avatar)
                        <div class="absolute inset-0">
                            <img src="{{ Storage::url($user->avatar) }}" 
                                 alt="{{ $user->name }}" 
                                 class="w-full h-full object-cover filter" style="filter: blur(10px); scale: 1.1;">
                            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                        </div>
                    @else
                        <div class="absolute inset-0 bg-gradient-to-br from-orange-500 via-pink-500 to-purple-600"></div>
                    @endif
                    
                    <!-- Bottom inner shadow for text readability -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-60"></div>
                    
                </div>
                <!-- Profile Info at bottom left -->
                <div class="flex justify-between w-full px-8" style="margin-top: -90px;">
                    <div class="relative">
                        <div class="bg-gray-800">
                            <div>
                                <!-- Profile Avatar -->
                                <div class="relative mb-4">
                                    @if($user->avatar)
                                        <img src="{{ Storage::url($user->avatar) }}" 
                                            alt="{{ $user->name }}" 
                                            class="rounded-lg object-cover border-4 border-white shadow-2xl" 
                                            style="min-width: 200px; height: auto; aspect-ratio: 1/1;">
                                    @else
                                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-orange-500 to-pink-600 flex items-center justify-center border-4 border-white shadow-2xl">
                                            <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <!-- User Stats -->
                                <div class="flex space-x-4 mb-4 ">
                                    <div class="flex items-center space-x-3 text-gray-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <span class="text-lg font-semibold">{{ $user->topics->count() + $user->posts->count() }}</span>
                                    </div>
                                    <div class="flex items-center space-x-3 text-gray-300">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        <span class="text-lg font-semibold">{{ $user->reviews->count() }}</span>
                                    </div>
                                </div>

                                <!-- Navigation Menu -->
                                <nav class="p-1 rounded-lg bg-gray-500 h-full z-10 g-light-bg dark:bg-dark-bg-secondary">
                                    <a href="{{ route('profile.show') }}" class="flex items-center space-x-3 px-4 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>Профіль</span>
                                    </a>
                                    <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <span>Бібліотека</span>
                                    </a>
                                    <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        <span>Рецензії</span>
                                    </a>
                                    <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        <span>Обговорення</span>
                                    </a>
                                    <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                        </svg>
                                        <span>Цитати</span>
                                    </a>
                                    <a href="#" class="flex items-center space-x-3 px-4 py-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        </svg>
                                        <span>Добірки</span>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="left-0 w-full">
                        <div class="flex flex-col items-end px-4">
                            <!-- User Info -->
                            <div class="flex-1 text-white" style="z-index: 100;">
                                <h1 class="text-4xl font-bold mb-2 drop-shadow-lg">{{ $user->name }}</h1>
                                <p class="text-xl text-gray-200 mb-4 drop-shadow-md">{{ $user->username }}</p>
                                
                                <!-- Achievement Badges -->
                                <div class="flex space-x-2 mb-4" style="margin-top: 40px;">
                                    <div class="w-8 h-8 bg-green-500 rounded-full border-2 border-white flex items-center justify-center shadow-lg">
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                    <div class="w-8 h-8 bg-green-500 rounded-full border-2 border-white flex items-center justify-center shadow-lg">
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                    <div class="w-8 h-8 bg-green-500 rounded-full border-2 border-white flex items-center justify-center shadow-lg">
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                    <div class="w-8 h-8 bg-green-500 rounded-full border-2 border-white flex items-center justify-center shadow-lg">
                                        <i class="fa-solid fa-star"></i>
                                    </div>
                                </div>
                                <!-- Bio -->
                                @if($user->bio)
                                    <div class="max-w-2xl">
                                        <p class="text-gray-200 leading-relaxed drop-shadow-md">{{ $user->bio }}</p>
                                        <a href="#" class="text-orange-300 hover:text-orange-200 text-sm mt-2 inline-block drop-shadow-md">Розгорнути</a>
                                    </div>
                                @endif
                            </div>
                            <!-- Recent Books Section -->
                            <div class="mt-6">
                                <h2 class="text-2xl font-bold text-white mb-6">Останні прочитані книги</h2>
                                
                                <!-- Books Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <!-- Book Card 1 -->
                                    <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                                        <div class="aspect-w-3 aspect-h-4 bg-gray-700">
                                            <div class="flex items-center justify-center h-64 bg-gradient-to-br from-gray-800 to-gray-900">
                                                <div class="text-center p-4">
                                                    <div class="text-white font-bold text-lg mb-2">РЕСПУБЛІКА</div>
                                                    <div class="text-white font-bold text-lg mb-2">ДРАКОНА</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="py-4">
                                            <h3 class="text-white font-semibold mb-2">Республіка Дракона</h3>
                                            <div class="flex items-center space-x-1 mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                                <span class="text-gray-300 text-sm ml-1">9.3</span>
                                            </div>
                                            <div class="flex items-center space-x-4 text-gray-400 text-sm mb-3">
                                                <div class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                    </svg>
                                                    <span>11</span>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                    </svg>
                                                    <span>34</span>
                                                </div>
                                            </div>
                                            <button class="w-full bg-gradient-to-r from-orange-500 to-pink-500 text-white py-2 px-4 rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all duration-200">
                                                + Додати
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Book Card 2 -->
                                    <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                                        <div class="aspect-w-3 aspect-h-4 bg-red-600">
                                            <div class="flex items-center justify-center h-64 bg-red-600">
                                                <div class="text-center p-4 text-white">
                                                    <div class="text-sm font-bold mb-2">СВІТОВИЙ БЕСТСЕЛЕР</div>
                                                    <div class="text-lg font-bold mb-2">ВІД ХОРОШОГО</div>
                                                    <div class="text-lg font-bold mb-2">до ВЕЛИЧНОГО</div>
                                                    <div class="text-sm">Джим Коллінз</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="py-4">
                                            <h3 class="text-white font-semibold mb-2">Від хорошого до величного</h3>
                                            <div class="flex items-center space-x-1 mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                                <span class="text-gray-300 text-sm ml-1">9.3</span>
                                            </div>
                                            <div class="flex items-center space-x-4 text-gray-400 text-sm mb-3">
                                                <div class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                    </svg>
                                                    <span>11</span>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                    </svg>
                                                    <span>34</span>
                                                </div>
                                            </div>
                                            <button class="w-full bg-gradient-to-r from-orange-500 to-pink-500 text-white py-2 px-4 rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all duration-200">
                                                + Додати
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-500 h-full z-10 g-light-bg dark:bg-dark-bg-secondary rounded-lg">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                            
                            <!-- Right Panel -->
                            <div class="w-80 bg-gray-800 rounded-lg p-6 ml-8">
                                <!-- Status -->
                                <div class="flex items-center justify-between mb-4">
                                    <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">Бібліоман</span>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span class="text-green-400 text-sm">online</span>
                                    </div>
                                </div>
                                
                                <!-- Reading Now -->
                                <div class="mb-6">
                                    <h3 class="text-white font-medium mb-2">Читає</h3>
                                    <a href="#" class="text-cyan-400 hover:text-cyan-300">Маруся Чурай та ще 5 книг</a>
                                </div>
                                
                                <!-- Ratings -->
                                <div class="mb-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="text-white font-medium">Оцінки</h3>
                                        <span class="text-gray-300">93</span>
                                    </div>
                                    <div class="flex items-center space-x-1 mb-2">
                                        <span class="text-white font-semibold">8/10</span>
                                        <div class="flex space-x-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= 4 ? 'text-yellow-400' : ($i == 5 ? 'text-yellow-200' : 'text-gray-600') }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                    
                                    <!-- Rating Distribution Chart -->
                                    <div class="space-y-1">
                                        @for($i = 10; $i >= 1; $i--)
                                            <div class="flex items-center space-x-2">
                                                <span class="text-gray-400 text-xs w-4">{{ $i }}</span>
                                                <div class="flex-1 bg-gray-700 rounded-full h-2">
                                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $i >= 7 ? ($i == 10 ? '100%' : ($i == 9 ? '80%' : ($i == 8 ? '60%' : '40%'))) : '10%' }}"></div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection