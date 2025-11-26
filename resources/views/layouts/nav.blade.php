<!-- Navigation -->
    <nav id="main-nav" class="bg-light-bg dark:bg-dark-bg border-b border-light-border dark:border-dark-border sticky top-0 z-50 backdrop-blur-sm bg-opacity-95 dark:bg-opacity-95 transition-all duration-300 transform translate-y-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}" class="flex items-center group">
                            <span class="text-2xl font-bold text-light-text-primary dark:text-dark-text-primary">Foxy</span>
                        </a>
                    </div>
                    <!-- Navigation Links -->
                    <div class="hidden md:ml-8 md:flex md:space-x-1">
                        <a href="{{ route('books.index') }}" class="{{ request()->routeIs('books.*') ? 'text-white px-3 py-2 text-sm font-medium rounded-lg' : 'text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary px-4 py-2 text-sm font-medium rounded-lg hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary' }} transition-all duration-200" style="{{ request()->routeIs('books.*') ? 'background-color: #F97316;' : '' }}">
                            Книги
                        </a>
                        <a href="{{ route('libraries.index') }}" class="{{ request()->routeIs('libraries.*') ? 'text-white px-3 py-2 text-sm font-medium rounded-lg' : 'text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary px-4 py-2 text-sm font-medium rounded-lg hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary' }} transition-all duration-200" style="{{ request()->routeIs('libraries.*') ? 'background-color: #F97316;' : '' }}">
                            Добірки
                        </a>
                        <a href="{{ route('discussions.index') }}" class="{{ request()->routeIs('discussions.*') ? 'text-white px-3 py-2 text-sm font-medium rounded-lg' : 'text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary px-4 py-2 text-sm font-medium rounded-lg hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary' }} transition-all duration-200" style="{{ request()->routeIs('discussions.*') ? 'background-color: #F97316;' : '' }}">
                           Обговорення
                        </a>
                        <a href="{{ route('authors.index') }}" class="{{ request()->routeIs('authors.*') ? 'text-white px-3 py-2 text-sm font-medium rounded-lg' : 'text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary px-4 py-2 text-sm font-medium rounded-lg hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary' }} transition-all duration-200" style="{{ request()->routeIs('authors.*') ? 'background-color: #F97316;' : '' }}">
                            Автори
                        </a>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="hidden flex-1 max-w-lg mx-8 md:flex" style="flex-direction: column; justify-content: center;">
                    <div id="nav-search-app">
                        <book-search :initial-value="'{{ request('q') }}'" placeholder="Пошук книг..."></book-search>
                    </div>
                </div>

                <!-- Right side -->
                <div class="flex items-center space-x-3">
                     <!-- Mobile Search Button -->
                    <button id="mobile-search-button" class="md:hidden p-2 text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary rounded-lg transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
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
                        <!-- Notification Bell (Vue Component) -->
                        <div id="notification-app">
                            <notification-bell></notification-bell>
                        </div>
                    @endauth

                    @auth
                        <!-- User Menu -->
                        <div class="relative hidden md:block" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary p-2 transition-all duration-200">
                                @if(auth()->user()->avatar)
                                    <img src="{{ auth()->user()->avatar }}" 
                                         alt="{{ auth()->user()->name }}" 
                                         class="w-8 h-8 rounded-lg object-cover shadow-md">
                                @else
                                    <div class="w-8 h-8 bg-gradient-to-br from-brand-500 to-brand-600 rounded-lg flex items-center justify-center shadow-md">
                                        <span class="text-sm font-medium text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </button>

                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-48 bg-light-bg/90 dark:bg-dark-bg/90 backdrop-blur-md rounded-xl shadow-xl py-2 z-50 border border-light-border/20 dark:border-dark-border/20">
                                <a href="{{ route('users.public.profile', auth()->user()->username) }}" class="block px-4 py-2 text-sm text-light-text-secondary dark:text-dark-text-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-colors"><i class="fas fa-user"></i> Публічний профіль</a>
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-light-text-secondary dark:text-dark-text-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-colors"><i class="fas fa-cog"></i> Панель керування</a>
                                <a href="{{ route('users.index') }}" class="block px-4 py-2 text-sm text-light-text-secondary dark:text-dark-text-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-colors"><i class="fas fa-star"></i> Рейтинг читачів</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-light-text-secondary dark:text-dark-text-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-colors">
                                        <i class="fas fa-sign-out-alt"></i> Вийти</button>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Auth Links -->
                        <a href="{{ route('login') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary px-3 py-2 text-sm font-medium transition-colors duration-200">Увійти</a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-brand-500 to-accent-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-brand-600 hover:to-accent-600 transition-all duration-200 shadow-sm hover:shadow-md">Реєстрація</a>
                    @endauth

                    <!-- Mobile Burger Menu Button -->
                    <button id="mobile-menu-button" class="md:hidden ml-4 p-2 text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary rounded-lg transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Bottom Menu -->
    <div id="mobile-menu" class="fixed inset-x-0 bottom-0 z-50 transform translate-y-full transition-transform duration-300 h-full max-h-[85vh] ease-in-out md:hidden">
        <div class="bg-light-bg dark:bg-dark-bg border-t border-light-border dark:border-dark-border shadow-2xl rounded-t-3xl h-full overflow-hidden flex flex-col">
            <!-- Menu Header -->
            <div class="flex items-center justify-between p-4 border-b border-light-border dark:border-dark-border">
                <h2 class="text-xl font-bold text-light-text-primary dark:text-dark-text-primary">Меню</h2>
                <button id="close-mobile-menu" class="p-2 text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary rounded-lg transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-light-border dark:border-dark-border overflow-x-auto scrollbar-hide">
                <button class="mobile-tab active px-6 py-3 text-sm font-medium text-light-text-primary dark:text-dark-text-primary border-b-2 border-brand-500 transition-all duration-200 whitespace-nowrap" data-tab="navigation">
                    Навігація
                </button>
                <button class="mobile-tab px-6 py-3 text-sm font-medium text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary border-b-2 border-transparent transition-all duration-200 whitespace-nowrap" data-tab="search">
                    Пошук
                </button>
                @auth
                <button class="mobile-tab px-6 py-3 text-sm font-medium text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary border-b-2 border-transparent transition-all duration-200 whitespace-nowrap" data-tab="profile">
                    Профіль
                </button>
                @endauth
            </div>

            <!-- Tab Content -->
            <div class="flex-1 overflow-y-auto">
                <!-- Navigation Tab -->
                <div id="tab-navigation" class="tab-content p-4 space-y-2">
                    <a href="{{ route('books.index') }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-all duration-200 {{ request()->routeIs('books.*') ? 'bg-brand-500/10 text-brand-500 dark:text-brand-400' : 'text-light-text-primary dark:text-dark-text-primary' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span class="font-medium">Книги</span>
                    </a>
                    <a href="{{ route('libraries.index') }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-all duration-200 {{ request()->routeIs('libraries.*') ? 'bg-brand-500/10 text-brand-500 dark:text-brand-400' : 'text-light-text-primary dark:text-dark-text-primary' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span class="font-medium">Добірки</span>
                    </a>
                    <a href="{{ route('discussions.index') }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-all duration-200 {{ request()->routeIs('discussions.*') ? 'bg-brand-500/10 text-brand-500 dark:text-brand-400' : 'text-light-text-primary dark:text-dark-text-primary' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span class="font-medium">Обговорення</span>
                    </a>
                    <a href="{{ route('authors.index') }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-all duration-200 {{ request()->routeIs('authors.*') ? 'bg-brand-500/10 text-brand-500 dark:text-brand-400' : 'text-light-text-primary dark:text-dark-text-primary' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-medium">Автори</span>
                    </a>
                </div>

                <!-- Search Tab -->
                <div id="tab-search" class="tab-content hidden p-4">
                    <div id="mobile-search-app">
                        <book-search :initial-value="'{{ request('q') }}'" placeholder="Пошук книг, авторів..."></book-search>
                    </div>
                </div>

                <!-- Profile Tab -->
                @auth
                <div id="tab-profile" class="tab-content hidden p-4 space-y-2">
                    <a href="{{ route('users.public.profile', auth()->user()->username) }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-all duration-200 text-light-text-primary dark:text-dark-text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-medium">Публічний профіль</span>
                    </a>
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-all duration-200 text-light-text-primary dark:text-dark-text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="font-medium">Панель керування</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-all duration-200 text-light-text-primary dark:text-dark-text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        <span class="font-medium">Рейтинг читачів</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center space-x-3 p-3 rounded-xl hover:bg-red-500/10 dark:hover:bg-red-500/20 transition-all duration-200 text-red-500 dark:text-red-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span class="font-medium">Вийти</span>
                        </button>
                    </form>
                </div>
                @else
                <div id="tab-profile" class="tab-content hidden p-4 space-y-3">
                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 rounded-xl border border-light-border dark:border-dark-border text-light-text-primary dark:text-dark-text-primary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-all duration-200 font-medium">
                        Увійти
                    </a>
                    <a href="{{ route('register') }}" class="block w-full text-center px-4 py-3 rounded-xl bg-gradient-to-r from-brand-500 to-accent-500 text-white hover:from-brand-600 hover:to-accent-600 transition-all duration-200 font-medium shadow-sm hover:shadow-md">
                        Реєстрація
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"></div>

    @push('scripts')
    <script>
        // Navigation Hide/Show on Scroll
        (function() {
            let lastScrollTop = 0;
            let scrollThreshold = 10; // Минимальное расстояние скролла для срабатывания
            let ticking = false;
            const nav = document.getElementById('main-nav');

            function handleScroll() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Игнорируем небольшие изменения скролла
                if (Math.abs(scrollTop - lastScrollTop) < scrollThreshold) {
                    ticking = false;
                    return;
                }

                if (scrollTop > lastScrollTop && scrollTop > 100) {
                    // Скролл вниз - скрываем навигатор
                    nav.classList.add('-translate-y-full');
                    nav.classList.remove('translate-y-0');
                } else if (scrollTop < lastScrollTop) {
                    // Скролл вверх - показываем навигатор
                    nav.classList.remove('-translate-y-full');
                    nav.classList.add('translate-y-0');
                }

                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
                ticking = false;
            }

            window.addEventListener('scroll', function() {
                if (!ticking) {
                    window.requestAnimationFrame(handleScroll);
                    ticking = true;
                }
            }, { passive: true });
        })();

        // Mobile Menu Management
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileSearchButton = document.getElementById('mobile-search-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
            const closeMobileMenuButton = document.getElementById('close-mobile-menu');
            const mobileTabs = document.querySelectorAll('.mobile-tab');
            const tabContents = document.querySelectorAll('.tab-content');

            // Open menu functions
            function openMobileMenu(tab = 'navigation') {
                mobileMenu.classList.remove('translate-y-full');
                mobileMenuOverlay.classList.remove('opacity-0', 'pointer-events-none');
                mobileMenuOverlay.classList.add('opacity-100', 'pointer-events-auto');
                document.body.style.overflow = 'hidden';
                
                // Switch to specified tab
                if (tab) {
                    switchTab(tab);
                }
            }

            // Close menu function
            function closeMobileMenu() {
                mobileMenu.classList.add('translate-y-full');
                mobileMenuOverlay.classList.remove('opacity-100', 'pointer-events-auto');
                mobileMenuOverlay.classList.add('opacity-0', 'pointer-events-none');
                document.body.style.overflow = '';
            }

            // Switch tab function
            function switchTab(tabName) {
                // Update tab buttons
                mobileTabs.forEach(tab => {
                    if (tab.dataset.tab === tabName) {
                        tab.classList.add('active', 'text-light-text-primary', 'dark:text-dark-text-primary', 'border-brand-500');
                        tab.classList.remove('text-light-text-secondary', 'dark:text-dark-text-secondary', 'border-transparent');
                    } else {
                        tab.classList.remove('active', 'text-light-text-primary', 'dark:text-dark-text-primary', 'border-brand-500');
                        tab.classList.add('text-light-text-secondary', 'dark:text-dark-text-secondary', 'border-transparent');
                    }
                });

                // Update tab content
                tabContents.forEach(content => {
                    if (content.id === `tab-${tabName}`) {
                        content.classList.remove('hidden');
                    } else {
                        content.classList.add('hidden');
                    }
                });
            }

            // Event listeners
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', () => openMobileMenu('navigation'));
            }

            if (mobileSearchButton) {
                mobileSearchButton.addEventListener('click', () => openMobileMenu('search'));
            }

            if (closeMobileMenuButton) {
                closeMobileMenuButton.addEventListener('click', closeMobileMenu);
            }

            if (mobileMenuOverlay) {
                mobileMenuOverlay.addEventListener('click', closeMobileMenu);
            }

            // Tab switching
            mobileTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    switchTab(tab.dataset.tab);
                });
            });

            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !mobileMenu.classList.contains('translate-y-full')) {
                    closeMobileMenu();
                }
            });

            // Prevent body scroll when menu is open
            let lastScrollTop = 0;
            mobileMenu.addEventListener('touchmove', function(e) {
                const scrollTop = mobileMenu.scrollTop;
                const scrollHeight = mobileMenu.scrollHeight;
                const height = mobileMenu.clientHeight;
                const isScrollingUp = scrollTop < lastScrollTop;
                const isAtTop = scrollTop === 0;
                const isAtBottom = scrollTop + height >= scrollHeight - 1;

                if ((isAtTop && isScrollingUp) || (isAtBottom && !isScrollingUp)) {
                    e.preventDefault();
                }
                lastScrollTop = scrollTop;
            }, { passive: false });
        });

        // Инициализация Vue приложения для уведомлений
        @auth
        if (document.getElementById('notification-app')) {
            new Vue({
                el: '#notification-app'
            });
        }
        @endauth

        // Инициализация Vue приложения для поиска
        if (window.Vue) {
            if (document.getElementById('nav-search-app')) {
                new Vue({
                    el: '#nav-search-app'
                });
            }
            if (document.getElementById('mobile-search-app')) {
                new Vue({
                    el: '#mobile-search-app'
                });
            }
        }
    </script>
    @endpush
