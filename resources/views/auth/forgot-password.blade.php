@extends('layouts.app')

@section('title', 'Забули пароль - Книжковий форум')

@section('main')
    <div>
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">
                Забули пароль?
            </h2>
            <p class="text-xs text-slate-600 dark:text-slate-400">
                Введіть вашу електронну адресу, і ми надішлемо вам посилання для відновлення пароля
            </p>
        </div>

        <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Right Side - Information Text -->
            <div class="hidden lg:block">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">
                        Відновлення доступу
                    </h3>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">
                                    Перевірте пошту
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Ми надішлемо вам посилання для відновлення пароля на вказану електронну адресу
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">
                                    Безпека
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Посилання для відновлення дійсне протягом обмеженого часу для вашої безпеки
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">
                                    Швидко та просто
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Процес відновлення пароля займає лише кілька хвилин
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Left Side - Form -->
            <div class="w-full">
                <!-- Form Card with Glass Effect -->
                <div
                    class="backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 p-6">
                    @if (session('status'))
                        <div
                            class="mb-4 bg-green-50 dark:bg-green-900/50 border border-green-200 dark:border-green-800 rounded-xl p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-4 w-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-2">
                                    <p class="text-xs text-green-800 dark:text-green-200">{{ session('status') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Forgot Password Form -->
                    <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-900 dark:text-white mb-2">
                                Email
                            </label>
                            <input id="email" name="email" type="email" required value="{{ old('email') }}"
                                placeholder="your@email.com"
                                class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-[1.02] text-sm">
                            Надіслати посилання для відновлення
                        </button>
                    </form>

                    <!-- Back to Login Link -->
                    <div class="mt-4 text-center">
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Згадали пароль?
                            <a href="{{ route('login') }}"
                                class="font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                                Увійти
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

