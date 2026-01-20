@extends('layouts.app')

@section('main')
    <div class="min-h-screen">
        <div class="mx-auto">
            <div class="flex flex-col lg:flex-row gap-8">

                <!-- Sidebar -->
                <div class="lg:w-80 flex-shrink-0">
                    <div
                        class="sticky top-10">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Налаштування профілю</h2>

                        <!-- Navigation -->
                        <nav class="space-y-2">
                            <button onclick="showSection('personal')"
                                class="w-full text-left px-4 py-3 rounded-lg font-medium transition-all duration-200 bg-purple-500 text-white sidebar-nav active"
                                data-section="personal">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-user text-gray-700 dark:text-gray-300"></i>
                                    <span>Особиста інформація</span>
                                </div>
                            </button>

                            <button onclick="showSection('account')"
                                class="w-full text-left px-4 py-3 rounded-lg font-medium transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 sidebar-nav"
                                data-section="account">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-user-cog text-gray-700 dark:text-gray-300"></i>
                                    <span>Управління акаунтом</span>
                                </div>
                            </button>

                            <button onclick="showSection('security')"
                                class="w-full text-left px-4 py-3 rounded-lg font-medium transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 sidebar-nav"
                                data-section="security">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-shield-alt text-gray-700 dark:text-gray-300"></i>
                                    <span>Безпека</span>
                                </div>
                            </button>

                            <button onclick="showSection('notifications')"
                                class="w-full text-left px-4 py-3 rounded-lg font-medium transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 sidebar-nav"
                                data-section="notifications">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-bell text-gray-700 dark:text-gray-300"></i>
                                    <span>Сповіщення</span>
                                </div>
                            </button>

                            <button onclick="showSection('privacy')"
                                class="w-full text-left px-4 py-3 rounded-lg font-medium transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 sidebar-nav"
                                data-section="privacy">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-lock text-gray-700 dark:text-gray-300"></i>
                                    <span>Приватність</span>
                                </div>
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    <!-- Notifications -->
                    @if (session('success'))
                        <div class="bg-green-50 dark:bg-green-900 border-l-4 border-green-400 p-4 mb-6 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-50 dark:bg-red-900 border-l-4 border-red-400 p-4 mb-6 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm text-red-700 dark:text-red-300">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Personal Information Section -->
                    <div id="personal-section" class="content-section">
                        <div>

                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Особиста інформація</h3>

                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Avatar Section -->
                                <div class="mb-8">
                                    <label
                                        class="block text-lg font-semibold text-gray-900 dark:text-white mb-4">Аватарка</label>
                                    <div
                                        class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6">
                                        <div class="relative">
                                            @if ($user->avatar)
                                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                                    class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 dark:border-gray-600 shadow-lg">
                                            @else
                                                <div
                                                    class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center border-4 border-gray-200 dark:border-gray-600 shadow-lg">
                                                    <svg class="w-12 h-12 text-white" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="space-y-3">
                                                <div class="relative">
                                                    <label for="avatar" class="cursor-pointer">
                                                        <input type="file" name="avatar" id="avatar"
                                                            accept="image/jpeg,image/png,image/gif,image/webp"
                                                            class="hidden"
                                                            onchange="document.getElementById('avatar-label').textContent = this.files.length > 0 ? 'Обрано файл: ' + this.files[0].name : 'Обрати файл'">
                                                        <span id="avatar-label" class="inline-block px-6 py-3 bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-xl font-semibold hover:bg-blue-100 dark:hover:bg-blue-800 transition-colors">Обрати файл</span>
                                                    </label>
                                                </div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Максимальний розмір:
                                                    2 МБ. Підтримувані формати: JPEG, PNG, GIF, WebP</p>
                                                @if ($user->avatar)
                                                    <button type="button"
                                                        onclick="(async function() { const confirmed = await confirm('Видалити аватарку?', 'Підтвердження', 'warning'); if (confirmed) { document.getElementById('delete-avatar-form').submit(); } })();"
                                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                        Видалити аватарку
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Personal Information Section -->
                                <div class="space-y-6">
                                    <h3
                                        class="text-lg font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">
                                        Особиста інформація</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Name -->
                                        <div>
                                            <label for="name"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ім'я
                                                *</label>
                                            <input type="text" name="name" id="name"
                                                value="{{ old('name', $user->name) }}" required
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                        </div>

                                        <!-- Username -->
                                        <div>
                                            <label for="username"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ім'я
                                                користувача *</label>
                                            <input type="text" name="username" id="username"
                                                value="{{ old('username', $user->username) }}" required
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <label for="email"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email-адреса
                                            *</label>
                                        <input type="email" name="email" id="email"
                                            value="{{ old('email', $user->email) }}" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                    </div>

                                    <!-- Bio -->
                                    <div>
                                        <label for="bio"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Про
                                            себе</label>
                                        <textarea name="bio" id="bio" rows="4" placeholder="Розкажіть про себе..."
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-none">{{ old('bio', $user->bio) }}</textarea>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Максимум 1000 символів</p>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div
                                    class="flex justify-between flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <a href="{{ route('profile.show') }}"
                                        class="flex-1 sm:flex-none bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg font-medium transition-all duration-200 text-center">
                                        <i class="fas fa-arrow-left"></i>
                                        Скасувати
                                    </a>
                                    <button type="submit"
                                        class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                                        Зберегти зміни
                                    </button>
                                </div>
                            </form>

                            <!-- Delete Avatar Form -->
                            @if ($user->avatar)
                                <form id="delete-avatar-form" action="{{ route('profile.avatar.destroy') }}"
                                    method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Account Management Section -->
                    <div id="account-section" class="content-section hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Управління акаунтом</h3>

                            <!-- Account Stats -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Прочитано книг
                                            </p>
                                            <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                                                {{ $user->bookReadingStatuses()->where('status', 'read')->count() }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-green-600 dark:text-green-400">Добірок</p>
                                            <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                                                {{ $user->libraries()->count() }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-8 text-purple-600 dark:text-purple-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Рецензій
                                            </p>
                                            <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                                                {{ $user->reviews()->count() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Actions -->
                            <div class="space-y-4">
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Експорт даних</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Завантажте всі свої дані у
                                        форматі JSON</p>
                                    <button onclick="exportUserData()"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                        Експортувати дані
                                    </button>
                                </div>

                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Видалити акаунт</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Ця дія незворотна. Всі ваші
                                        дані будуть видалені назавжди.</p>
                                    <button onclick="deleteAccount()"
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                        Видалити акаунт
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Section -->
                    <div id="security-section" class="content-section hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Безпека</h3>

                            <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-6">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label for="current_password"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Поточний
                                        пароль</label>
                                    <input type="password" name="current_password" id="current_password" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                </div>

                                <div>
                                    <label for="password"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Новий
                                        пароль</label>
                                    <input type="password" name="password" id="password" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                </div>

                                <div>
                                    <label for="password_confirmation"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Підтвердження
                                        паролю</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                </div>

                                <button type="submit"
                                    class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                                    Змінити пароль
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Notifications Section -->
                    <div id="notifications-section" class="content-section hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Сповіщення</h3>

                            <form action="{{ route('profile.notifications.update') }}" method="POST" class="space-y-6">
                                @csrf
                                @method('PUT')

                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Email сповіщення
                                            </h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Отримувати сповіщення на
                                                email</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="email_notifications" value="1"
                                                class="sr-only peer"
                                                {{ old('email_notifications', $user->email_notifications ?? true) ? 'checked' : '' }}>
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Сповіщення про
                                                нові книги</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Отримувати сповіщення про
                                                нові книги від улюблених авторів</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="new_books_notifications" value="1"
                                                class="sr-only peer"
                                                {{ old('new_books_notifications', $user->new_books_notifications ?? true) ? 'checked' : '' }}>
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Сповіщення про
                                                коментарі</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Отримувати сповіщення про
                                                нові коментарі до ваших рецензій</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="comments_notifications" value="1"
                                                class="sr-only peer"
                                                {{ old('comments_notifications', $user->comments_notifications ?? true) ? 'checked' : '' }}>
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                                    Зберегти налаштування
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Privacy Section -->
                    <div id="privacy-section" class="content-section hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Приватність</h3>

                            <form action="{{ route('profile.privacy.update') }}" method="POST" class="space-y-6">
                                @csrf
                                @method('PUT')

                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Публічний профіль
                                            </h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Дозволити іншим
                                                користувачам переглядати ваш профіль</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="public_profile" value="1"
                                                class="sr-only peer"
                                                {{ old('public_profile', $user->public_profile ?? true) ? 'checked' : '' }}>
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Показувати
                                                статистику читання</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Дозволити іншим бачити вашу
                                                статистику читання</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="show_reading_stats" value="1"
                                                class="sr-only peer"
                                                {{ old('show_reading_stats', $user->show_reading_stats ?? true) ? 'checked' : '' }}>
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Показувати
                                                оцінки книг</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Дозволити іншим бачити ваші
                                                оцінки книг</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="show_ratings" value="1"
                                                class="sr-only peer"
                                                {{ old('show_ratings', $user->show_ratings ?? true) ? 'checked' : '' }}>
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200">
                                    Зберегти налаштування
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function showSection(sectionName) {
                // Hide all sections
                document.querySelectorAll('.content-section').forEach(section => {
                    section.classList.add('hidden');
                });

                // Show selected section
                document.getElementById(sectionName + '-section').classList.remove('hidden');

                // Update sidebar navigation
                document.querySelectorAll('.sidebar-nav').forEach(btn => {
                    btn.classList.remove('bg-purple-500', 'text-white');
                    btn.classList.add('text-gray-700', 'dark:text-gray-300');
                });

                document.querySelector(`[data-section="${sectionName}"]`).classList.add('bg-purple-500', 'text-white');
                document.querySelector(`[data-section="${sectionName}"]`).classList.remove('text-gray-700',
                    'dark:text-gray-300');
            }

            async function exportUserData() {
                const confirmed = await confirm('Ви впевнені, що хочете експортувати свої дані?', 'Підтвердження', 'warning');
                if (confirmed) {
                    window.location.href = '{{ route('profile.export') }}';
                }
            }

            async function deleteAccount() {
                const confirmed1 = await confirm('Ви впевнені, що хочете видалити свій акаунт? Цю дію неможливо скасувати!', 'Підтвердження', 'warning');
                if (confirmed1) {
                    const confirmed2 = await confirm('Це останнє попередження. Всі ваші дані будуть видалені назавжди. Продовжити?', 'Останнє попередження', 'error');
                    if (confirmed2) {
                        document.getElementById('delete-account-form').submit();
                    }
                }
            }
        </script>
    @endpush

    <!-- Delete Account Form -->
    <form id="delete-account-form" action="{{ route('profile.destroy') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

@endsection
