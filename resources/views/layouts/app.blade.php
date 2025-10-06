<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-id" content="{{ auth()->id() ?? '' }}">
        <meta name="theme-color" content="#ffffff">

        {{-- Inline script to prevent flash of wrong theme --}}
        <script>
            (function() {
                // Получаем сохраненную тему или системную
                const savedTheme = localStorage.getItem('bookforum-theme');
                const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                const theme = savedTheme || systemTheme;
                
                // Применяем тему немедленно
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                    document.querySelector('meta[name="theme-color"]').setAttribute('content', '#0f172a');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.querySelector('meta[name="theme-color"]').setAttribute('content', '#ffffff');
                }
            })();
        </script>

        <title>@yield('title', 'Книжковий форум')</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        @stack('styles')
        
        <style>
            /* User mini header styles */
            .user-mini-header {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .user-avatar {
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: 1rem;
                color: white;
                position: relative;
                overflow: hidden;
            }

            .avatar-guest {
                background: linear-gradient(135deg, #6b7280, #9ca3af);
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .avatar-fallback {
                background: linear-gradient(135deg, hsl(var(--primary)), hsl(var(--accent)));
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .avatar-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 50%;
            }

            .user-info {
                display: flex;
                flex-direction: column;
                gap: 0.125rem;
            }

            .user-name {
                font-weight: 600;
                font-size: 0.875rem;
                color: hsl(var(--foreground));
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .guest-badge {
                background: #f97316;
                color: white;
                font-size: 0.75rem;
                padding: 0.125rem 0.5rem;
                border-radius: 9999px;
                font-weight: 500;
            }

            .user-timestamp {
                font-size: 0.75rem;
                color: hsl(var(--muted-foreground));
            }

            /* Dark theme adjustments */
            @media (prefers-color-scheme: dark) {
                .guest-badge {
                    background: #ea580c;
                }
            }

            /* Custom animations */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            .animate-fade-in-up {
                animation: fadeInUp 0.6s ease-out;
            }
            
            .animate-fade-in {
                animation: fadeIn 0.4s ease-out;
            }
            
            .animate-slide-in-right {
                animation: slideInRight 0.5s ease-out;
            }
            
            /* Loading state */
            body:not(.loaded) {
                overflow: hidden;
            }
            
            body:not(.loaded)::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            body:not(.loaded)::after {
                content: '';
                position: fixed;
                top: 50%;
                left: 50%;
                width: 50px;
                height: 50px;
                margin: -25px 0 0 -25px;
                border: 3px solid rgba(255, 255, 255, 0.3);
                border-top: 3px solid white;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                z-index: 10000;
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            /* Smooth scrolling */
            html {
                scroll-behavior: smooth;
            }
            
            /* Custom scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
            }
            
            ::-webkit-scrollbar-track {
                background: transparent;
            }
            
            ::-webkit-scrollbar-thumb {
                background: rgba(156, 163, 175, 0.5);
                border-radius: 4px;
            }
            
            ::-webkit-scrollbar-thumb:hover {
                background: rgba(156, 163, 175, 0.8);
            }
            
            /* Glass morphism effect */
            .glass {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            
            .glass-dark {
                background: rgba(0, 0, 0, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            /* Hover effects */
            .hover-lift {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            
            .hover-lift:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
            
            /* Gradient text */
            .gradient-text {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            /* Pulse animation for interactive elements */
            .pulse-slow {
                animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
            
            /* Stagger animation for lists */
            .stagger-item {
                opacity: 0;
                transform: translateY(20px);
                animation: fadeInUp 0.6s ease-out forwards;
            }
            
            .stagger-item:nth-child(1) { animation-delay: 0.1s; }
            .stagger-item:nth-child(2) { animation-delay: 0.2s; }
            .stagger-item:nth-child(3) { animation-delay: 0.3s; }
            .stagger-item:nth-child(4) { animation-delay: 0.4s; }
            .stagger-item:nth-child(5) { animation-delay: 0.5s; }
        </style>
    </head>
    <body class="font-sans antialiased bg-light-bg dark:bg-dark-bg text-light-text-primary dark:text-dark-text-primary transition-colors duration-300">
        <div class="min-h-screen bg-light-bg dark:bg-dark-bg transition-colors duration-300">
            @include('layouts.nav')
            {{-- Profile banner --}}
            @yield('profile-banner')
            <!-- Main Content -->
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                @yield('main')
            </main>
            @include('layouts.footer')
        </div>
        
        @stack('scripts')
        
        <script src="{{ mix('js/app.js') }}"></script>
        
        <script>
            // Add loading animation
            window.addEventListener('load', function() {
                document.body.classList.add('loaded');
            });
        </script>
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
    </body>
</html>
