<!-- Footer -->
    <footer class="bg-light-bg dark:bg-dark-bg border-t border-light-border dark:border-dark-border mt-16 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center mb-4">
                        <span class="text-xl font-bold text-light-text-primary dark:text-dark-text-primary">Foxy</span>
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
                        <li><a href="{{ route('categories.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary text-sm transition-colors duration-200">Категорії</a></li>
                        <li><a href="{{ route('authors.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary text-sm transition-colors duration-200">Автори</a></li>
                        <li><a href="{{ route('users.index') }}" class="text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary text-sm transition-colors duration-200">Користувачі</a></li>
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