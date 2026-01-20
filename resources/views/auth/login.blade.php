@extends('layouts.app')

@section('title', 'Вхід - Книжковий форум')

@section('main')
    <div>
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">
                Увійти до акаунту
            </h2>
            <p class="text-xs text-slate-600 dark:text-slate-400">
                Введіть свої дані, щоб увійти
            </p>
        </div>
        <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Right Side - Information Text -->
            <div class="hidden lg:block">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">
                        Ласкаво просимо назад!
                    </h3>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">
                                    Продовжуйте читання
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Поверніться до своєї бібліотеки та продовжуйте відстежувати прогрес читання улюблених книг
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">
                                    Спілкуйтеся зі спільнотою
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Приєднуйтесь до обговорень, діліться своїми думками та отримуйте рекомендації від інших
                                    читачів
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">
                                    Зберігайте прогрес
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Всі ваші оцінки, рецензії та закладки зберігаються та доступні в будь-який момент
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
                                    Швидкий доступ
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Використовуйте "Запам'ятати мене" для швидкого входу без введення даних кожного разу
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Left Side - Login Form -->
            <div class="w-full">
                <!-- Login Card with Glass Effect -->
                <div
                    class="backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30  p-6">
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

                    <!-- Login Form -->
                    <form action="{{ route('login') }}" method="POST" class="space-y-4">
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

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-900 dark:text-white mb-2">
                                Пароль
                            </label>
                            <div class="relative">
                                <input id="password" name="password" type="password" required placeholder="••••••••"
                                    class="w-full px-3 py-2 pr-10 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror">
                                <button type="button" onclick="togglePassword('password')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                                    <svg id="password-eye" class="h-4 w-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="password-eye-off" class="h-4 w-4 hidden" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember_me" name="remember" type="checkbox"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-600 rounded">
                                <label for="remember_me" class="ml-2 block text-xs text-slate-900 dark:text-slate-300">
                                    Запам'ятати мене
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <div class="text-xs">
                                    <a href="{{ route('password.request') }}"
                                        class="font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                                        Забули пароль?
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-[1.02] text-sm">
                            Увійти
                        </button>
                    </form>

                    <!-- Register Link -->
                    <div class="mt-4 text-center">
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Немає акаунту?
                            <a href="{{ route('register') }}"
                                class="font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                                Зареєструватися
                            </a>
                        </p>
                    </div>

                    <!-- OAuth Login Options -->
                    <div class="mt-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-300 dark:border-slate-600"></div>
                            </div>
                            <div class="relative flex justify-center text-xs">
                                <span class="px-2 bg-white/80 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400">Або
                                    увійдіть через</span>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <button type="button" onclick="loginWithGoogle()"
                                class="w-full inline-flex justify-center py-2.5 px-4 border border-slate-300 dark:border-slate-600 rounded-xl shadow-sm bg-white dark:bg-slate-700 text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                                <svg class="w-4 h-4" viewBox="0 0 24 24">
                                    <path fill="#4285F4"
                                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                    <path fill="#34A853"
                                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                    <path fill="#FBBC05"
                                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                    <path fill="#EA4335"
                                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                                </svg>
                                <span class="ml-2">Google</span>
                            </button>

                            <button type="button" onclick="loginWithFacebook()"
                                class="w-full inline-flex justify-center py-2.5 px-4 border border-slate-300 dark:border-slate-600 rounded-xl shadow-sm bg-white dark:bg-slate-700 text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors">
                                <svg class="w-4 h-4" fill="#1877F2" viewBox="0 0 24 24">
                                    <path
                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                </svg>
                                <span class="ml-2">Facebook</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '-eye');
            const eyeOff = document.getElementById(fieldId + '-eye-off');

            if (field.type === 'password') {
                field.type = 'text';
                if (eye) eye.classList.add('hidden');
                if (eyeOff) eyeOff.classList.remove('hidden');
            } else {
                field.type = 'password';
                if (eye) eye.classList.remove('hidden');
                if (eyeOff) eyeOff.classList.add('hidden');
            }
        }

        function loginWithGoogle() {
            window.location.href = '{{ route("auth.google") }}';
        }

        function loginWithFacebook() {
            window.location.href = '{{ route("auth.facebook") }}';
        }
    </script>
@endsection