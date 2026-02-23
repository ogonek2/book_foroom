@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        // Обмежуємо кількість номерів сторінок, щоб не ламати верстку (особливо на мобільних)
        $onEachSide = 1; // скільки номерів показувати зліва/справа від поточної
        $slots = [];

        if ($lastPage <= 5) {
            $slots = range(1, $lastPage);
        } else {
            $slots[] = 1;
            $windowStart = max(2, $currentPage - $onEachSide);
            $windowEnd = min($lastPage - 1, $currentPage + $onEachSide);
            if ($windowStart > 2) {
                $slots[] = 'ellipsis';
            }
            for ($i = $windowStart; $i <= $windowEnd; $i++) {
                $slots[] = $i;
            }
            if ($windowEnd < $lastPage - 1) {
                $slots[] = 'ellipsis';
            }
            if ($lastPage > 1) {
                $slots[] = $lastPage;
            }
        }
    @endphp

    <nav class="flex flex-wrap items-center justify-center gap-2 sm:gap-2 my-6 px-1" aria-label="Пагінація">
        {{-- Попередня --}}
        @if ($paginator->onFirstPage())
            <span
                class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl font-semibold transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed flex-shrink-0"
                aria-label="Попередня сторінка"
            >
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
        @else
            <a
                href="{{ $paginator->previousPageUrl() }}"
                rel="prev"
                class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl font-semibold transition-all duration-200 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-orange-500 hover:to-purple-600 hover:text-white dark:hover:text-white border border-gray-200 dark:border-gray-600 hover:border-transparent shadow-sm hover:shadow-md flex-shrink-0"
                aria-label="Попередня сторінка"
            >
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        @endif

        {{-- Номери сторінок (обмежений набір) --}}
        @foreach ($slots as $slot)
            @if ($slot === 'ellipsis')
                <span
                    class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl text-gray-400 dark:text-gray-500 font-medium flex-shrink-0"
                    aria-hidden="true"
                >
                    <span class="text-sm">…</span>
                </span>
            @else
                @php
                    $url = $paginator->url($slot);
                    $isCurrent = (int) $slot === (int) $currentPage;
                @endphp
                @if ($isCurrent)
                    <span
                        class="inline-flex items-center justify-center min-w-[2rem] sm:min-w-[2.5rem] h-9 sm:h-10 px-2 sm:px-3 rounded-xl font-semibold bg-gradient-to-r from-orange-500 to-purple-600 text-white shadow-md flex-shrink-0 text-sm sm:text-base"
                        aria-current="page"
                        aria-label="Сторінка {{ $slot }}"
                    >
                        {{ $slot }}
                    </span>
                @else
                    <a
                        href="{{ $url }}"
                        class="inline-flex items-center justify-center min-w-[2rem] sm:min-w-[2.5rem] h-9 sm:h-10 px-2 sm:px-3 rounded-xl font-semibold transition-all duration-200 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md flex-shrink-0 text-sm sm:text-base"
                        aria-label="Сторінка {{ $slot }}"
                    >
                        {{ $slot }}
                    </a>
                @endif
            @endif
        @endforeach

        {{-- Наступна --}}
        @if ($paginator->hasMorePages())
            <a
                href="{{ $paginator->nextPageUrl() }}"
                rel="next"
                class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl font-semibold transition-all duration-200 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-orange-500 hover:to-purple-600 hover:text-white dark:hover:text-white border border-gray-200 dark:border-gray-600 hover:border-transparent shadow-sm hover:shadow-md flex-shrink-0"
                aria-label="Наступна сторінка"
            >
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @else
            <span
                class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl font-semibold transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed flex-shrink-0"
                aria-label="Наступна сторінка"
            >
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        @endif
    </nav>
@endif
