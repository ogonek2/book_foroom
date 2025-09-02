@extends('app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-black dark:to-gray-800">
    <!-- Navigation -->
    <nav class="bg-white/90 dark:bg-black/90 backdrop-blur-xl border-b border-gray-200/30 dark:border-gray-800/30 sticky top-0 z-50 shadow-lg shadow-gray-200/20 dark:shadow-gray-900/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}" class="flex items-center group">
                            <div class="w-8 h-8 bg-gradient-to-br from-[#F6762E] to-[#F78F54] rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="ml-3 text-xl font-bold bg-gradient-to-r from-[#F6762E] to-[#F78F54] bg-clip-text text-transparent">BookForum</span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:ml-8 md:flex md:space-x-1">
                        <a href="{{ route('home') }}" class="text-gray-900 dark:text-white hover:text-[#FF843E] dark:hover:text-[#FF843E] px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800/50 transition-all duration-200">
                            Головна
                        </a>
                        <a href="{{ route('books.index') }}" class="text-gray-600 dark:text-gray-300 hover:text-[#FF843E] dark:hover:text-[#FF843E] px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800/50 transition-all duration-200">
                            Книги
                        </a>
                        <a href="{{ route('forum.index') }}" class="text-gray-600 dark:text-gray-300 hover:text-[#FF843E] dark:hover:text-[#FF843E] px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800/50 transition-all duration-200">
                            Форум
                        </a>
                        <a href="{{ route('categories.index') }}" class="text-gray-600 dark:text-gray-300 hover:text-[#FF843E] dark:hover:text-[#FF843E] px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800/50 transition-all duration-200">
                            Категорії
                        </a>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="flex-1 max-w-lg mx-8 hidden lg:block">
                    <form action="{{ route('search') }}" method="GET" class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" 
                               class="block w-full pl-12 pr-4 py-3 border-0 rounded-xl leading-5 bg-gray-100 dark:bg-gray-800/50 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-gray-800 transition-all duration-200 backdrop-blur-sm" 
                               placeholder="Пошук книг, авторів, обговорень...">
                    </form>
                </div>

                <!-- Right side -->
                <div class="flex items-center space-x-3">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="p-2.5 text-gray-500 dark:text-gray-400 hover:text-[#FF843E] dark:hover:text-[#FF843E] hover:bg-gray-100 dark:hover:bg-gray-800/50 rounded-lg transition-all duration-200">
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    @auth
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FF843E] hover:bg-gray-100 dark:hover:bg-gray-800/50 p-2 transition-all duration-200">
                                                        <div class="w-8 h-8 bg-gradient-to-br from-[#F6762E] to-[#F78F54] rounded-lg flex items-center justify-center shadow-md">
                            <span class="text-sm font-medium text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                            </button>

                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-48 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md rounded-xl shadow-xl py-2 z-50 border border-gray-200/20 dark:border-gray-700/20">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/50 transition-colors">Панель керування</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800/50 transition-colors">
                                        Вийти
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Auth Links -->
                        <a href="{{ route('login') }}" class="text-gray-600 dark:text-gray-300 hover:text-[#FF843E] dark:hover:text-[#FF843E] px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800/50 transition-all duration-200">
                            Увійти
                        </a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-[#F6762E] to-[#F78F54] hover:from-[#FF843E] hover:to-[#F6762E] text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-lg hover:shadow-xl">
                            Реєстрація
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('main')
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-gradient-to-r from-[#F6762E] to-[#F78F54] rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="ml-2 text-xl font-bold text-gray-900 dark:text-white">BookForum</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Книжковий форум для обговорення літератури, обміну думками та пошуку нових книг для читання.
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Навігація</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm">Головна</a></li>
                        <li><a href="{{ route('books.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm">Книги</a></li>
                        <li><a href="{{ route('forum.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm">Форум</a></li>
                        <li><a href="{{ route('categories.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm">Категорії</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Спільнота</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm">Правила</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm">Допомога</a></li>
                        <li><a href="#" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm">Контакти</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <p class="text-center text-gray-600 dark:text-gray-400 text-sm">
                    © {{ date('Y') }} BookForum. Всі права захищені.
                </p>
            </div>
        </div>
    </footer>
</div>

<!-- Alpine.js for dropdowns -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Theme Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
    const themeToggleBtn = document.getElementById('theme-toggle');

    // Check for saved theme preference or default to 'light'
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // Apply the current theme
    if (currentTheme === 'dark') {
        document.documentElement.classList.add('dark');
        themeToggleLightIcon.classList.remove('hidden');
        themeToggleDarkIcon.classList.add('hidden');
    } else {
        document.documentElement.classList.remove('dark');
        themeToggleDarkIcon.classList.remove('hidden');
        themeToggleLightIcon.classList.add('hidden');
    }

    // Listen for the toggle button click
    themeToggleBtn.addEventListener('click', function() {
        // Toggle the theme
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
            themeToggleDarkIcon.classList.remove('hidden');
            themeToggleLightIcon.classList.add('hidden');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
            themeToggleLightIcon.classList.remove('hidden');
            themeToggleDarkIcon.classList.add('hidden');
        }
    });
});
</script>
@endsection
