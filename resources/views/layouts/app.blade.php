@extends('app')

@section('content')
<div class="min-h-screen bg-light-bg dark:bg-dark-bg transition-colors duration-300">
    @include('layouts.nav')
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('main')
    </main>
    @include('layouts.footer')
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
