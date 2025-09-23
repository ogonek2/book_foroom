@extends('errors.layout')

@section('title', 'Внутрішня помилка сервера - 500')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-pink-100 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <!-- 500 Icon -->
        <div class="mx-auto h-24 w-24 text-red-600">
            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
        </div>

        <!-- Error Code -->
        <div>
            <h1 class="text-9xl font-bold text-red-600 mb-4">500</h1>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Внутрішня помилка сервера</h2>
            <p class="text-lg text-gray-600 mb-8">
                Щось пішло не так на нашому сервері. Ми працюємо над вирішенням проблеми.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-4">
            <a href="{{ url('/') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Повернутися на головну
            </a>
            
            <button onclick="window.location.reload()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Спробувати знову
            </button>
        </div>

        <!-- Help Text -->
        <div class="mt-8 text-sm text-gray-500">
            <p>Якщо проблема повторюється, будь ласка, спробуйте пізніше або зв'яжіться з нами.</p>
        </div>
    </div>
</div>
@endsection
