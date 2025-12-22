@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
    @endphp
    
    <nav class="flex items-center justify-center space-x-2 my-6" aria-label="Пагинация">
        {{-- Previous Button --}}
        @if ($paginator->onFirstPage())
            <button
                disabled
                class="flex items-center justify-center w-10 h-10 rounded-xl font-semibold transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-600 cursor-not-allowed"
                aria-label="Попередня сторінка"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        @else
            <a
                href="{{ $paginator->previousPageUrl() }}"
                class="flex items-center justify-center w-10 h-10 rounded-xl font-semibold transition-all duration-200 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-500 hover:to-purple-600 hover:text-white dark:hover:text-white border border-gray-200 dark:border-gray-700 hover:border-transparent shadow-sm hover:shadow-md"
                aria-label="Попередня сторінка"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="flex items-center justify-center w-10 h-10 text-gray-400 dark:text-gray-600 font-semibold">
                    {{ $element }}
                </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $currentPage)
                        <span
                            class="flex items-center justify-center min-w-[2.5rem] h-10 px-3 rounded-xl font-semibold bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-md"
                            aria-current="page"
                            aria-label="Сторінка {{ $page }}"
                        >
                            {{ $page }}
                        </span>
                    @else
                        <a
                            href="{{ $url }}"
                            class="flex items-center justify-center min-w-[2.5rem] h-10 px-3 rounded-xl font-semibold transition-all duration-200 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md"
                            aria-label="Перейти на сторінку {{ $page }}"
                        >
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Button --}}
        @if ($paginator->hasMorePages())
            <a
                href="{{ $paginator->nextPageUrl() }}"
                class="flex items-center justify-center w-10 h-10 rounded-xl font-semibold transition-all duration-200 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-500 hover:to-purple-600 hover:text-white dark:hover:text-white border border-gray-200 dark:border-gray-700 hover:border-transparent shadow-sm hover:shadow-md"
                aria-label="Наступна сторінка"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @else
            <button
                disabled
                class="flex items-center justify-center w-10 h-10 rounded-xl font-semibold transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-600 cursor-not-allowed"
                aria-label="Наступна сторінка"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        @endif
    </nav>
@endif
