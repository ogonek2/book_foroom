<!-- Footer -->
    <footer class="bg-light-bg dark:bg-dark-bg border-t border-light-border dark:border-dark-border mt-16 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 mb-8">
            <!-- Brand Section -->
            <div class="lg:col-span-2">
                    <div class="flex items-center mb-4">
                    <span class="text-2xl font-bold bg-gradient-to-r from-brand-500 via-accent-500 to-brand-600 bg-clip-text text-transparent">
                        Foxy
                    </span>
                    </div>
                <p class="text-light-text-secondary dark:text-dark-text-secondary text-sm leading-relaxed mb-4">
                    Україномовна книжкова спільнота для читачів, рецензій та власних бібліотек.
                </p>
                <!-- Social Links -->
                <div class="flex items-center space-x-4">
                    <a href="https://ko-fi.com/foxybooks" target="_blank" rel="noopener noreferrer" 
                       class="w-10 h-10 rounded-lg bg-light-bg-secondary dark:bg-dark-bg-secondary hover:bg-brand-500 hover:text-white flex items-center justify-center transition-all duration-200 group">
                        <svg class="w-5 h-5 text-light-text-secondary dark:text-dark-text-secondary group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.881 8.58c0-4.54-3.704-8.244-8.244-8.244H8.362C3.822.336.118 4.04.118 8.58v4.8c0 4.54 3.704 8.244 8.244 8.244h3.197c.124 0 .246-.01.368-.024l3.24 3.24a.96.96 0 001.36 0l3.24-3.24c.122.014.244.024.368.024h.19c4.54 0 8.244-3.704 8.244-8.244V8.58zm-1.44 5.04c0 3.704-3.02 6.724-6.724 6.724h-.19l-3.24 3.24a.24.24 0 01-.34 0l-3.24-3.24h-.19c-3.704 0-6.724-3.02-6.724-6.724v-4.8c0-3.704 3.02-6.724 6.724-6.724h7.275c3.704 0 6.724 3.02 6.724 6.724v4.8z"/>
                            <path d="M12.495 15.678c-.372 0-.67-.298-.67-.67v-2.39H9.76c-.372 0-.67-.298-.67-.67s.298-.67.67-.67h2.065V9.478c0-.372.298-.67.67-.67s.67.298.67.67v2.002h2.065c.372 0 .67.298.67.67s-.298.67-.67.67h-2.065v2.39c0 .372-.298.67-.67.67z"/>
                        </svg>
                    </a>
                    <a href="#" target="_blank" rel="noopener noreferrer" 
                       class="w-10 h-10 rounded-lg bg-light-bg-secondary dark:bg-dark-bg-secondary hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all duration-200 group">
                        <svg class="w-5 h-5 text-light-text-secondary dark:text-dark-text-secondary group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="#" target="_blank" rel="noopener noreferrer" 
                       class="w-10 h-10 rounded-lg bg-light-bg-secondary dark:bg-dark-bg-secondary hover:bg-purple-500 hover:text-white flex items-center justify-center transition-all duration-200 group">
                        <svg class="w-5 h-5 text-light-text-secondary dark:text-dark-text-secondary group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.374 0 0 5.373 0 12s5.374 12 12 12 12-5.373 12-12S18.626 0 12 0zm5.568 8.16c-.169 1.858-.896 3.305-2.051 4.348-.577.525-1.275.93-2.053 1.206-.777.276-1.624.414-2.464.414-.84 0-1.687-.138-2.464-.414-.778-.276-1.476-.681-2.053-1.206-1.155-1.043-1.882-2.49-2.051-4.348-.034-.374-.051-.754-.051-1.16 0-.406.017-.786.051-1.16.169-1.858.896-3.305 2.051-4.348.577-.525 1.275-.93 2.053-1.206C8.313 2.138 9.16 2 10 2s1.687.138 2.464.414c.778.276 1.476.681 2.053 1.206 1.155 1.043 1.882 2.49 2.051 4.348.034.374.051.754.051 1.16 0 .406-.017.786-.051 1.16z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Navigation Section -->
                <div>
                <h3 class="text-sm font-semibold text-light-text-primary dark:text-dark-text-primary uppercase tracking-wider mb-4 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    Навігація
                </h3>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('home') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Головна
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('books.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Книги
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('authors.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Автори
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('discussions.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Обговорення
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('libraries.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Добірки
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Користувачі
                        </a>
                    </li>
                    </ul>
                </div>

            <!-- Community Section -->
                <div>
                <h3 class="text-sm font-semibold text-light-text-primary dark:text-dark-text-primary uppercase tracking-wider mb-4 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Спільнота
                </h3>
                <ul class="space-y-3">
                    <li>
                        <a href="#" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Правила спільноти
                        </a>
                    </li>
                    <li>
                        <a href="https://ko-fi.com/foxybooks" target="_blank" rel="noopener noreferrer" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Підтримати проект
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Контакти
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                            <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            Про нас
                        </a>
                    </li>
                    </ul>
                </div>

            <!-- For Users Section -->
            <div>
                <h3 class="text-sm font-semibold text-light-text-primary dark:text-dark-text-primary uppercase tracking-wider mb-4 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Користувачам
                </h3>
                <ul class="space-y-3">
                    @auth
                        <li>
                            <a href="{{ route('profile.show') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                                <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                Мій профіль
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.show', ['tab' => 'library']) }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                                <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                Моя бібліотека
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.show', ['tab' => 'reviews']) }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                                <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                Мої рецензії
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.show', ['tab' => 'discussions']) }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                                <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                Мої обговорення
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('login') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                                <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                Увійти
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 text-sm transition-colors duration-200 flex items-center group">
                                <svg class="w-4 h-4 mr-2 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                Зареєструватися
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="mt-8 pt-8 border-t border-light-border dark:border-dark-border">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <p class="text-center md:text-left text-light-text-secondary dark:text-dark-text-secondary text-sm">
                    © {{ date('Y') }} <span class="font-semibold bg-gradient-to-r from-brand-500 to-brand-600 bg-clip-text text-transparent">Foxy</span>. Всі права захищені.
                </p>
                <div class="flex items-center space-x-6 text-sm text-light-text-secondary dark:text-dark-text-secondary">
                    <a href="#" class="hover:text-brand-500 dark:hover:text-brand-400 transition-colors duration-200">Політика конфіденційності</a>
                    <a href="#" class="hover:text-brand-500 dark:hover:text-brand-400 transition-colors duration-200">Умови використання</a>
                </div>
            </div>
            </div>
        </div>
    </footer>
