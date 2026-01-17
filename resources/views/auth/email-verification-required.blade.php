@extends('layouts.app')

@section('title', 'Підтвердження електронної пошти - Книжковий форум')

@section('main')
<div class="flex items-center justify-center min-h-[60vh] py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30">
                <svg class="h-10 w-10 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-slate-900 dark:text-white">
                Реєстрація успішна!
            </h2>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-8">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/30 mb-4">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-3">
                    Перевірте свою електронну пошту
                </h3>
                
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                    Ми надіслали лист підтвердження на вашу електронну пошту 
                    @if(session('email'))
                        <strong class="text-slate-900 dark:text-white">{{ session('email') }}</strong>
                    @endif
                    <br><br>
                    Будь ласка, перевірте свою поштову скриньку та натисніть посилання для активації акаунту.
                </p>

                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6 text-left">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="h-5 w-5 text-blue-500 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">
                                Не отримали лист?
                            </h4>
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                Перевірте папку "Спам" або "Небажана пошта". Якщо листа немає, можете запросити повторне надсилання нижче.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Resend verification form -->
                <form action="{{ route('verification.resend') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Електронна пошта
                        </label>
                        <input id="email" name="email" type="email" required 
                               class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                               placeholder="your@email.com"
                               value="{{ old('email', session('email')) }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @if(session('status'))
                            <p class="mt-2 text-sm text-green-600 dark:text-green-400">{{ session('status') }}</p>
                        @endif
                        @if(session('error'))
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ session('error') }}</p>
                        @endif
                    </div>

                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Надіслати повторно
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Після підтвердження електронної пошти ви зможете 
                        <a href="{{ route('login') }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">
                            увійти до акаунту
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

