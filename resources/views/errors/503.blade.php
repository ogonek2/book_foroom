@extends('errors.layout')

@section('title', 'Сервіс тимчасово недоступний - 503')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-100 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <!-- 503 Icon -->
        <div class="mx-auto h-24 w-24 text-gray-600">
            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </div>

        <!-- Error Code -->
        <div>
            <h1 class="text-9xl font-bold text-gray-600 mb-4">503</h1>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Сервіс тимчасово недоступний</h2>
            <p class="text-lg text-gray-600 mb-8">
                Ми проводимо технічні роботи. Сайт буде доступний найближчим часом.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-4">
            <button onclick="window.location.reload()" 
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Спробувати знову
            </button>
            
            <a href="{{ url('/') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Повернутися на головну
            </a>
        </div>

        <!-- Status Info -->
        <div class="mt-8 p-4 bg-blue-50 rounded-lg">
            <div class="flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm text-blue-800">Очікуваний час відновлення: 15-30 хвилин</span>
            </div>
        </div>

        <!-- Help Text -->
        <div class="mt-4 text-sm text-gray-500">
            <p>Дякуємо за терпіння. Ми працюємо над швидким відновленням сервісу.</p>
        </div>
    </div>
</div>
@endsection
