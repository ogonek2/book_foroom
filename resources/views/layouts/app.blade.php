@extends('app')

@section('content')
<div class="min-h-screen bg-light-bg dark:bg-dark-bg transition-colors duration-300">
    <!-- Navigation -->
    <nav class="bg-light-bg dark:bg-dark-bg border-b border-light-border dark:border-dark-border sticky top-0 z-50 backdrop-blur-sm bg-opacity-95 dark:bg-opacity-95 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}" class="flex items-center group">
                            <span class="text-2xl font-bold text-light-text-primary dark:text-dark-text-primary">foxy</span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:ml-8 md:flex md:space-x-1">
                        <a href="{{ route('home') }}" class="text-light-text-primary dark:text-dark-text-primary bg-light-bg-secondary dark:bg-dark-bg-secondary px-3 py-2 text-sm font-medium rounded-lg">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Головна
                        </a>
                        <a href="{{ route('books.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary px-4 py-2 text-sm font-medium rounded-lg hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-all duration-200">
                            Книги
                        </a>
                        <a href="{{ route('forum.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary px-4 py-2 text-sm font-medium rounded-lg hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-all duration-200">
                            Форум
                        </a>
                        <a href="{{ route('categories.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary px-4 py-2 text-sm font-medium rounded-lg hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-all duration-200">
                            Категорії
                        </a>
                        <a href="{{ route('authors.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary px-4 py-2 text-sm font-medium rounded-lg hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-all duration-200">
                            Автори
                        </a>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="flex-1 max-w-lg mx-8 hidden lg:block" style="display: flex; flex-direction: column; justify-content: center;">
                    <form action="{{ route('search') }}" method="GET" class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-light-text-tertiary dark:text-dark-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" 
                               class="block w-full pl-10 pr-3 py-2 border border-light-border dark:border-dark-border rounded-lg bg-light-bg-secondary dark:bg-dark-bg-secondary text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors duration-200" 
                               placeholder="Пошук книг...">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <span class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm">/</span>
                        </div>
                    </form>
                </div>

                <!-- Right side -->
                <div class="flex items-center space-x-3">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="p-2.5 text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary rounded-lg transition-all duration-200">
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
                            <button @click="open = !open" class="flex items-center text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary p-2 transition-all duration-200">
                                                        <div class="w-8 h-8 bg-gradient-to-br from-brand-500 to-brand-600 rounded-lg flex items-center justify-center shadow-md">
                            <span class="text-sm font-medium text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                            </button>

                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-48 bg-light-bg/90 dark:bg-dark-bg/90 backdrop-blur-md rounded-xl shadow-xl py-2 z-50 border border-light-border/20 dark:border-dark-border/20">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-light-text-secondary dark:text-dark-text-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-colors">Панель керування</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-light-text-secondary dark:text-dark-text-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-colors">
                                        Вийти
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Auth Links -->
                        <a href="{{ route('login') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary px-3 py-2 text-sm font-medium transition-colors duration-200">Увійти</a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-brand-500 to-accent-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-brand-600 hover:to-accent-600 transition-all duration-200 shadow-sm hover:shadow-md">Реєстрація</a>
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
    <footer class="bg-light-bg dark:bg-dark-bg border-t border-light-border dark:border-dark-border mt-16 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-gradient-to-r from-brand-500 to-brand-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="ml-2 text-xl font-bold text-light-text-primary dark:text-dark-text-primary">BookForum</span>
                    </div>
                    <p class="text-light-text-secondary dark:text-dark-text-secondary text-sm">
                        Книжковий форум для обговорення літератури, обміну думками та пошуку нових книг для читання.
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-light-text-primary dark:text-dark-text-primary uppercase tracking-wider mb-4">Навігація</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary text-sm transition-colors duration-200">Головна</a></li>
                        <li><a href="{{ route('books.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary text-sm transition-colors duration-200">Книги</a></li>
                        <li><a href="{{ route('forum.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary text-sm transition-colors duration-200">Форум</a></li>
                        <li><a href="{{ route('categories.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary text-sm transition-colors duration-200">Категорії</a></li>
                        <li><a href="{{ route('authors.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary text-sm transition-colors duration-200">Автори</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-light-text-primary dark:text-dark-text-primary uppercase tracking-wider mb-4">Спільнота</h3>
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
            // Новая система управления темами
            class ThemeManager {
                constructor() {
                    this.themeKey = 'bookforum-theme';
                    this.systemThemeKey = 'bookforum-system-theme';
                    this.themeToggleBtn = document.getElementById('theme-toggle');
                    this.themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
                    this.themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
                    
                    this.init();
                }

                init() {
                    // Применяем тему при загрузке
                    this.applyTheme(this.getCurrentTheme());
                    
                    // Слушаем клик по кнопке переключения
                    if (this.themeToggleBtn) {
                        this.themeToggleBtn.addEventListener('click', () => this.toggleTheme());
                    }
                    
                    // Слушаем изменения системной темы
                    this.watchSystemTheme();
                    
                    // Применяем анимацию перехода
                    this.enableTransitions();
                }

                getSystemTheme() {
                    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }

                getCurrentTheme() {
                    // Проверяем сохраненную тему
                    const savedTheme = localStorage.getItem(this.themeKey);
                    if (savedTheme && ['light', 'dark'].includes(savedTheme)) {
                        return savedTheme;
                    }
                    
                    // Если нет сохраненной темы, используем системную
                    return this.getSystemTheme();
                }

                applyTheme(theme) {
                    const isDark = theme === 'dark';
                    
                    // Применяем класс к HTML элементу
                    if (isDark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                    
                    // Обновляем иконки
                    this.updateIcons(isDark);
                    
                    // Сохраняем тему
                    localStorage.setItem(this.themeKey, theme);
                    
                    // Обновляем мета-тег для SEO
                    this.updateMetaTheme(theme);
                    
                    // Вызываем событие для других компонентов
                    this.dispatchThemeChange(theme);
                }

                updateIcons(isDark) {
                    if (this.themeToggleDarkIcon && this.themeToggleLightIcon) {
                        if (isDark) {
                            this.themeToggleLightIcon.classList.remove('hidden');
                            this.themeToggleDarkIcon.classList.add('hidden');
                        } else {
                            this.themeToggleDarkIcon.classList.remove('hidden');
                            this.themeToggleLightIcon.classList.add('hidden');
                        }
                    }
                }

                updateMetaTheme(theme) {
                    const metaTheme = document.querySelector('meta[name="theme-color"]');
                    if (metaTheme) {
                        metaTheme.setAttribute('content', theme === 'dark' ? '#0f172a' : '#ffffff');
                    }
                }

                dispatchThemeChange(theme) {
                    window.dispatchEvent(new CustomEvent('themeChanged', {
                        detail: { theme }
                    }));
                }

                toggleTheme() {
                    const currentTheme = this.getCurrentTheme();
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    this.applyTheme(newTheme);
                }

                watchSystemTheme() {
                    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                    
                    mediaQuery.addEventListener('change', (e) => {
                        // Применяем системную тему только если пользователь не выбрал свою
                        const savedTheme = localStorage.getItem(this.themeKey);
                        if (!savedTheme) {
                            this.applyTheme(e.matches ? 'dark' : 'light');
                        }
                    });
                }

                enableTransitions() {
                    // Добавляем плавные переходы для изменения темы
                    const style = document.createElement('style');
                    style.textContent = `
                        * {
                            transition: background-color 0.3s ease, 
                                       border-color 0.3s ease, 
                                       color 0.3s ease,
                                       box-shadow 0.3s ease !important;
                        }
                    `;
                    document.head.appendChild(style);
                }

                // Публичные методы для внешнего использования
                setTheme(theme) {
                    if (['light', 'dark'].includes(theme)) {
                        this.applyTheme(theme);
                    }
                }

                getTheme() {
                    return this.getCurrentTheme();
                }
            }

            // Инициализируем систему тем при загрузке DOM
            document.addEventListener('DOMContentLoaded', () => {
                window.themeManager = new ThemeManager();
            });

            // Экспортируем для глобального использования
            window.ThemeManager = ThemeManager;
        </script>
@endsection
