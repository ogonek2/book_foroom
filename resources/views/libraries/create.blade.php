@extends('layouts.app')

@section('title', 'Створити добірку')

@section('main')
    <div id="app" class="">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <ol class="flex items-center space-x-2 text-sm">
                    <li><a href="{{ route('libraries.index') }}" class="text-slate-600 dark:text-slate-400 hover:text-orange-500">Добірки</a></li>
                    <li><span class="text-slate-400 dark:text-slate-500">/</span></li>
                    <li class="text-slate-900 dark:text-white font-medium">Створити добірку</li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-black text-slate-900 dark:text-white mb-2">Створити нову добірку</h1>
                <p class="text-lg text-slate-600 dark:text-slate-400">Створіть персональну добірку ваших улюблених книг</p>
            </div>

            <!-- Create Form -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 p-8">
                <form action="{{ route('libraries.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Library Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Назва добірки <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white transition-all duration-200"
                               placeholder="Наприклад: Мої улюблені фантастичні романи"
                               required>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Library Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Опис добірки
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-slate-700 text-slate-900 dark:text-white transition-all duration-200 resize-none"
                                  placeholder="Опишіть тематику вашої добірки, що вас надихнуло її створити...">{{ old('description') }}</textarea>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">До 1000 символів</p>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Visibility -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                            Видимість
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="radio" 
                                       name="is_private" 
                                       value="0"
                                       {{ old('is_private', '0') == '0' ? 'checked' : '' }}
                                       class="w-4 h-4 text-orange-600 bg-slate-100 border-slate-300 focus:ring-orange-500 dark:focus:ring-orange-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-medium text-slate-900 dark:text-white">Публічна</span>
                                    </div>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">Добірка буде видна всім користувачам та може отримати лайки</p>
                                </div>
                            </label>
                            
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="radio" 
                                       name="is_private" 
                                       value="1"
                                       {{ old('is_private') == '1' ? 'checked' : '' }}
                                       class="w-4 h-4 text-orange-600 bg-slate-100 border-slate-300 focus:ring-orange-500 dark:focus:ring-orange-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-medium text-slate-900 dark:text-white">Приватна</span>
                                    </div>
                                    <p class="text-sm text-slate-600 dark:text-slate-400">Добірка буде видна тільки вам</p>
                                </div>
                            </label>
                        </div>
                        @error('is_private')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tips Section -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-1">Поради для створення добірки</h4>
                                <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                                    <li>• Використовуйте зрозумілу та привабливу назву</li>
                                    <li>• Додайте опис, щоб інші користувачі зрозуміли тематику</li>
                                    <li>• Публічні добірки можуть отримати лайки від інших користувачів</li>
                                    <li>• Після створення ви зможете додати книги з каталогу</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-slate-200 dark:border-slate-700">
                        <a href="{{ route('libraries.index') }}" 
                           class="px-6 py-3 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-medium transition-colors">
                            Скасувати
                        </a>
                        
                        <button type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-xl font-bold hover:from-orange-600 hover:to-red-600 transition-all duration-300 transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Створити добірку
                        </button>
                    </div>
                </form>
            </div>

            <!-- Next Steps -->
            <div class="mt-8 bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-orange-500 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-orange-900 dark:text-orange-100 mb-2">Що далі?</h3>
                        <p class="text-orange-800 dark:text-orange-200 mb-3">
                            Після створення добірки ви зможете:
                        </p>
                        <ul class="text-orange-800 dark:text-orange-200 space-y-1">
                            <li>• Додавати книги з каталогу до вашої добірки</li>
                            <li>• Редагувати назву та опис в будь-який час</li>
                            <li>• Змінювати видимість (публічна/приватна)</li>
                            <li>• Видаляти книги з добірки</li>
                            <li>• Поділитися добіркою з друзями</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
