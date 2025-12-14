@extends('layouts.app')

@section('title', 'Реєстрація - Книжковий форум')

@section('main')
    <div>
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">
                Створити акаунт
            </h2>
            <p class="text-xs text-slate-600 dark:text-slate-400">
                Заповніть форму для реєстрації
            </p>
        </div>
        <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Right Side - Information Text -->
            <div class="hidden lg:block">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">
                        Приєднуйтесь до нашої спільноти
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
                                    Обговорюйте книги
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Діліться своїми думками про прочитані книги, отримуйте рекомендації та знаходьте
                                    нові цікаві твори
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
                                    Знаходьте однодумців
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Спілкуйтеся з людьми, які поділяють ваші літературні смаки та інтереси
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
                                    Відстежуйте прочитане
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Створюйте власну бібліотеку, відстежуйте прогрес читання та отримуйте
                                    персоналізовані рекомендації
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">
                                    Оцінюйте та рецензуйте
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Діліться своїми оцінками та детальними рецензіями, допомагаючи іншим знаходити
                                    найкращі книги
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Registration Card with Glass Effect -->
            <div class="backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 p-6">
                <!-- Registration Form -->
                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-900 dark:text-white mb-1.5 mb-2">
                            Ім'я <span class="text-red-500">*</span>
                        </label>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}" placeholder="Ваше ім'я"
                            class="w-full px-3 py-2 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-900 dark:text-white mb-1.5 mb-2">
                            Email <span class="text-red-500">*</span>
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
                        <label for="password" class="block text-xs font-semibold text-slate-900 dark:text-white mb-1.5 mb-2">
                            Пароль <span class="text-red-500">*</span>
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

                    <!-- Password Confirmation Field -->
                    <div>
                        <label for="password_confirmation"
                            class="block text-xs font-semibold text-slate-900 dark:text-white mb-1.5 mb-2">
                            Підтвердіть пароль <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                placeholder="••••••••"
                                class="w-full px-3 py-2 pr-10 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            <button type="button" onclick="togglePassword('password_confirmation')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                                <svg id="password_confirmation-eye" class="h-4 w-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="password_confirmation-eye-off" class="h-4 w-4 hidden" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-[1.02] text-sm">
                        Зареєструватися
                    </button>
                </form>

                <!-- Login Link -->
                <div class="mt-4 text-center">
                    <p class="text-xs text-slate-600 dark:text-slate-400">
                        Вже є акаунт?
                        <a href="{{ route('login') }}"
                            class="font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                            Увійти
                        </a>
                    </p>
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
        </script>
@endsection