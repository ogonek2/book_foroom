@extends('layouts.app')

@section('title', $category->name . ' — Категорія')

@php
    $booksCountLabel = function (int $n): string {
        $mod10 = $n % 10;
        $mod100 = $n % 100;
        if ($mod10 === 1 && $mod100 !== 11) {
            return $n . ' книга';
        }
        if ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 10 || $mod100 >= 20)) {
            return $n . ' книги';
        }

        return $n . ' книг';
    };
@endphp

@section('main')
<div class="max-w-7xl mx-auto pb-20">
    {{-- Breadcrumb --}}
    <nav class="pt-4 pb-2 flex flex-wrap items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
        <a href="{{ route('categories.index') }}" class="hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
            Категорії
        </a>
        @foreach($breadcrumbs as $crumb)
            <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            @if($crumb->id === $category->id)
                <span class="text-gray-700 dark:text-gray-200 font-medium truncate max-w-[12rem] sm:max-w-none">{{ $crumb->name }}</span>
            @else
                <a href="{{ route('categories.show', $crumb->slug) }}" class="hover:text-orange-600 dark:hover:text-orange-400 transition-colors truncate max-w-[10rem] sm:max-w-none">
                    {{ $crumb->name }}
                </a>
            @endif
        @endforeach
    </nav>

    {{-- Hero --}}
    <header class="pt-4 pb-8 sm:pb-10">
        @if($category->parent)
            <p class="text-sm font-semibold tracking-wide text-orange-600 dark:text-orange-400 uppercase mb-2">
                {{ $category->parent->name }}
            </p>
        @else
            <p class="text-sm font-semibold tracking-wide text-orange-600 dark:text-orange-400 uppercase mb-2">FoxyBooks</p>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6">
            <div class="min-w-0 flex-1">
                <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 dark:text-white tracking-tight leading-tight"
                    style="font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;">
                    {{ $category->name }}
                </h1>
                @if($category->description)
                    <p class="mt-3 text-lg text-gray-600 dark:text-gray-400 max-w-2xl leading-relaxed">{{ $category->description }}</p>
                @endif
                <p class="mt-3 text-base text-gray-500 dark:text-gray-400 font-medium">
                    {{ $booksCountLabel($booksCount) }}
                    <span class="text-gray-400 dark:text-gray-500 font-normal">· з урахуванням підкатегорій</span>
                </p>
            </div>

            <a href="{{ route('books.index', ['category' => $category->slug]) }}"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-semibold hover:opacity-90 transition-all shadow-lg flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Фільтрувати в каталозі
            </a>
        </div>
    </header>

    {{-- Subcategories --}}
    @if($category->children->isNotEmpty())
        <section class="mb-10 sm:mb-12">
            <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3 px-0.5">
                Підкатегорії
            </h2>
            <div class="subcat-scroll flex gap-2.5 overflow-x-auto pb-2 -mx-1 px-1 snap-x snap-mandatory">
                @foreach($category->children as $child)
                    <a href="{{ route('categories.show', $child->slug) }}"
                        class="subcat-pill snap-start flex-shrink-0 inline-flex items-center px-4 py-2.5 rounded-full text-sm font-medium
                            bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm border border-gray-200/30 dark:border-gray-700/30
                            text-gray-800 dark:text-gray-200 hover:bg-white/80 dark:hover:bg-gray-800/80 hover:shadow-md
                            transition-all duration-200">
                        {{ $child->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Books shelf --}}
    @if($books->count() > 0)
        <section>
            <div class="flex items-baseline justify-between gap-4 mb-5">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight"
                    style="font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', sans-serif;">
                    Книги
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 hidden sm:block">
                    Сторінка {{ $books->currentPage() }} з {{ $books->lastPage() }}
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-x-4 gap-y-8 sm:gap-x-5 sm:gap-y-10">
                @foreach($books as $book)
                    <a href="{{ route('books.show', $book->slug) }}" class="book-shelf-item group block">
                        <div class="book-cover-wrap relative rounded-xl overflow-hidden shadow-md group-hover:shadow-xl transition-all duration-300 group-hover:-translate-y-1">
                            <img src="{{ $book->cover_image_display }}" data-fallback="bookCover"
                                alt="{{ $book->book_name_ua ?: $book->title }}"
                                class="w-full aspect-[2/3] object-cover bg-gray-100 dark:bg-gray-800"
                                loading="{{ $loop->iteration <= 10 ? 'eager' : 'lazy' }}"
                                decoding="async"
                                @if($loop->iteration <= 10) fetchpriority="high" @endif
                                width="200"
                                height="300">
                            @if($book->rating > 0)
                                <div class="absolute bottom-2 right-2 px-2 py-0.5 rounded-full text-[11px] font-bold
                                    bg-black/55 backdrop-blur-sm text-white tabular-nums">
                                    {{ number_format($book->rating, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="mt-2.5 px-0.5">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white leading-snug line-clamp-2 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                {{ $book->book_name_ua ?: $book->title }}
                            </h3>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 line-clamp-1">{{ $book->author }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($books->hasPages())
                <div class="mt-12">
                    {{ $books->links('vendor.pagination.custom') }}
                </div>
            @endif
        </section>
    @else
        <div class="text-center py-20 px-6 rounded-3xl bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm border border-gray-200/30 dark:border-gray-700/30">
            <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-gray-100 dark:bg-gray-700/80 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">У цій категорії поки немає книг</h3>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Спробуйте іншу підкатегорію або перегляньте весь каталог</p>
            <a href="{{ route('books.index') }}" class="inline-flex items-center mt-6 px-6 py-3 rounded-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-semibold hover:opacity-90 transition-all">
                Перейти до каталогу
            </a>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .subcat-scroll {
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .subcat-scroll::-webkit-scrollbar {
        display: none;
    }

    .book-shelf-item {
        -webkit-tap-highlight-color: transparent;
    }

    .book-cover-wrap {
        background: linear-gradient(145deg, rgba(255,255,255,0.08), rgba(0,0,0,0.04));
    }

    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
