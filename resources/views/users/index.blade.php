@extends('layouts.app')

@section('title', 'Участники сообщества')

@section('main')
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="text-center mb-12">
        <h1 class="text-5xl font-bold text-gray-900 dark:text-white mb-4">
            Участники сообщества
        </h1>
        <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
            Познакомьтесь с активными читателями, критиками и авторами нашего книжного сообщества
        </p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-6 border border-blue-200/50 dark:border-blue-700/50">
            <div class="flex items-center">
                <div class="p-3 bg-blue-500 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $users->total() }}</p>
                    <p class="text-blue-700 dark:text-blue-300 font-medium">Участников</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/20 dark:to-emerald-900/20 rounded-2xl p-6 border border-green-200/50 dark:border-green-700/50">
            <div class="flex items-center">
                <div class="p-3 bg-green-500 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $users->sum('main_reviews_count') }}</p>
                    <p class="text-green-700 dark:text-green-300 font-medium">Рецензий</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-violet-100 dark:from-purple-900/20 dark:to-violet-900/20 rounded-2xl p-6 border border-purple-200/50 dark:border-purple-700/50">
            <div class="flex items-center">
                <div class="p-3 bg-purple-500 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $users->sum('public_quotes_count') }}</p>
                    <p class="text-purple-700 dark:text-purple-300 font-medium">Цитат</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-amber-100 dark:from-orange-900/20 dark:to-amber-900/20 rounded-2xl p-6 border border-orange-200/50 dark:border-orange-700/50">
            <div class="flex items-center">
                <div class="p-3 bg-orange-500 rounded-xl">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-orange-900 dark:text-orange-100">{{ $users->sum('published_publications_count') }}</p>
                    <p class="text-orange-700 dark:text-orange-300 font-medium">Публикаций</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($users as $index => $user)
        <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-500 overflow-hidden border border-gray-200 dark:border-gray-700 hover:scale-105 cursor-pointer">
            <!-- Rank Badge -->
            <div class="absolute top-3 right-3 z-20 group-hover:opacity-0 transition-opacity duration-300">
                @if($index < 3)
                    @if($index === 0)
                        <div class="w-8 h-8 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                    @elseif($index === 1)
                        <div class="w-8 h-8 bg-gradient-to-r from-gray-300 to-gray-400 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                    @else
                        <div class="w-8 h-8 bg-gradient-to-r from-orange-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </div>
                    @endif
                @else
                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-gray-600 dark:text-gray-300 font-bold text-sm">{{ $index + 1 }}</span>
                    </div>
                @endif
            </div>

            <!-- Main Content Container -->
            <div class="relative p-5 pb-3 h-full">
                <!-- User Avatar - Always Visible -->
                <div class="flex justify-center mb-4">
                    <div class="relative">
                        <img src="{{ $user->avatar ?: 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&size=80' }}" 
                             alt="{{ $user->name }}" 
                             class="w-16 h-16 rounded-full object-cover border-3 border-white dark:border-gray-700 shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- User Info - Always Visible -->
                <div class="text-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 group-hover:text-indigo-500 dark:group-hover:text-indigo-300 transition-colors duration-300">{{ $user->username }}</p>
                </div>

                <!-- Content that disappears on hover -->
                <div class="group-hover:opacity-0 group-hover:scale-95 transition-all duration-300">
                    <!-- Rating -->
                    <div class="flex items-center justify-center mb-3">
                        <div class="flex text-yellow-500 mr-2">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="text-sm">{{ $i <= floor($user->rating) ? '★' : '☆' }}</span>
                            @endfor
                        </div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->rating }}</span>
                    </div>

                    @if($user->bio)
                    <p class="text-gray-600 dark:text-gray-300 text-xs leading-relaxed px-2 line-clamp-2 mb-4">{{ Str::limit($user->bio, 80) }}</p>
                    @endif

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-center p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $user->public_quotes_count }}</div>
                            <div class="text-xs text-blue-600 dark:text-blue-400 font-medium">Цитаты</div>
                        </div>
                        <div class="text-center p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="text-lg font-bold text-green-600 dark:text-green-400">{{ $user->published_publications_count }}</div>
                            <div class="text-xs text-green-600 dark:text-green-400 font-medium">Публикации</div>
                        </div>
                        <div class="text-center p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <div class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $user->main_reviews_count }}</div>
                            <div class="text-xs text-purple-600 dark:text-purple-400 font-medium">Рецензии</div>
                        </div>
                    </div>
                </div>

                <!-- Hover Button - Appears on hover -->
                <div class="absolute bottom-4 left-4 right-4 opacity-0 group-hover:opacity-100 group-hover:translate-y-0 translate-y-4 transition-all duration-300">
                    <a href="{{ route('users.show', $user) }}" 
                       class="block w-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-center py-3 px-4 rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Перейти на профиль
                        </div>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-16 flex justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
