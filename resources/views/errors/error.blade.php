@extends('errors.layout')

@section('title', 'Помилка - ' . $exception->getStatusCode())

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-100 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <!-- Error Icon -->
        <div class="mx-auto h-24 w-24 text-gray-600">
            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
        </div>

        <!-- Error Code -->
        <div>
            <h1 class="text-9xl font-bold text-gray-600 mb-4">{{ $exception->getStatusCode() }}</h1>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                @switch($exception->getStatusCode())
                    @case(400)
                        Поганий запит
                        @break
                    @case(401)
                        Не авторизовано
                        @break
                    @case(402)
                        Потрібна оплата
                        @break
                    @case(404)
                        Сторінка не знайдена
                        @break
                    @case(405)
                        Метод не дозволено
                        @break
                    @case(408)
                        Час очікування вичерпано
                        @break
                    @case(409)
                        Конфлікт
                        @break
                    @case(410)
                        Ресурс більше не існує
                        @break
                    @case(422)
                        Необроблювана сутність
                        @break
                    @case(429)
                        Забагато запитів
                        @break
                    @case(500)
                        Внутрішня помилка сервера
                        @break
                    @case(502)
                        Поганий шлюз
                        @break
                    @case(503)
                        Сервіс недоступний
                        @break
                    @case(504)
                        Тайм-аут шлюзу
                        @break
                    @default
                        Помилка
                @endswitch
            </h2>
            <p class="text-lg text-gray-600 mb-8">
                @if($exception->getMessage())
                    {{ $exception->getMessage() }}
                @else
                    Вибачте, але сталася неочікувана помилка.
                @endif
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-4">
            <a href="{{ url('/') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Повернутися на головну
            </a>
            
            <button onclick="window.history.back()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Назад
            </button>
        </div>

        <!-- Help Text -->
        <div class="mt-8 text-sm text-gray-500">
            <p>Якщо проблема повторюється, будь ласка, зв'яжіться з нами.</p>
        </div>
    </div>
</div>
@endsection
