@extends('layouts.app')

@section('title', 'Відновлення пароля - Книжковий форум')

@section('main')
    <div>
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">
                Відновлення пароля
            </h2>
            <p class="text-xs text-slate-600 dark:text-slate-400">
                Введіть новий пароль для вашого акаунту
            </p>
        </div>

        <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Right Side - Information Text -->
            <div class="hidden lg:block">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">
                        Створіть новий пароль
                    </h3>

                    <div class="space-y-6">
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
                                    Безпечний пароль
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Використовуйте мінімум 8 символів, включаючи літери та цифри
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
                                    Підтвердження
                                </h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    Після зміни пароля вам потрібно буде увійти знову з новим паролем
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

                    <!-- Reset Password Form -->
                    <form action="{{ route('password.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Token -->
                        <input type="hidden" name="token" value="{{ $token }}">

                        <!-- Email -->
                        <input type="hidden" name="email" value="{{ $email }}">

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-900 dark:text-white mb-2">
                                Новий пароль
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
                                class="block text-xs font-semibold text-slate-900 dark:text-white mb-2">
                                Підтвердіть пароль
                            </label>
                            <div class="relative">
                                <input id="password_confirmation" name="password_confirmation" type="password" required
                                    placeholder="••••••••"
                                    class="w-full px-3 py-2 pr-10 text-sm border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('password_confirmation') border-red-500 @enderror">
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
                            @error('password_confirmation')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-[1.02] text-sm">
                            Відновити пароль
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

