@extends('layouts.app')

@section('title', 'Приватний профіль')

@section('main')
<div class="flex items-center justify-center">
    <div class="max-w-2xl w-full mx-4">
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 p-12 text-center">
            <!-- Lock Icon -->
            <div class="mb-8 relative">
                <div class="absolute inset-0 bg-purple-500/20 blur-3xl rounded-full"></div>
                <div class="relative inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full shadow-xl">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>

            <!-- User Avatar (if available) -->
            @if($user->avatar)
                <div class="mb-6">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                         class="w-24 h-24 rounded-full object-cover border-4 border-purple-200 dark:border-purple-800 mx-auto shadow-lg">
                </div>
            @endif

            <!-- Title -->
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Приватний профіль
            </h1>

            <!-- User Info -->
            <div class="mb-6">
                <p class="text-xl text-gray-700 dark:text-gray-300 font-medium">{{ $user->name }}</p>
                <p class="text-gray-500 dark:text-gray-400">{{ '@' . $user->username }}</p>
            </div>

            <!-- Description -->
            <div class="mb-8 space-y-3">
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Цей користувач встановив свій профіль як приватний
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-500">
                    Тільки власник профілю може переглядати цю сторінку
                </p>
            </div>

            <!-- Decorative Elements -->
            <div class="flex justify-center space-x-2 mb-8">
                <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                <div class="w-2 h-2 bg-pink-500 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    На головну
                </a>

                <a href="{{ url()->previous() }}" 
                   class="inline-flex items-center justify-center px-8 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Назад
                </a>
            </div>

            <!-- Additional Info -->
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-500">
                    Якщо ви вважаєте, що це помилка, зв'яжіться з власником профілю
                </p>
            </div>
        </div>

        <!-- Background Decorations -->
        <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden -z-10">
            <div class="absolute top-20 left-10 w-72 h-72 bg-purple-300/30 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-pink-300/30 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-indigo-300/20 rounded-full blur-3xl"></div>
        </div>
    </div>
</div>
@endsection

