@extends('layouts.app')

@section('title', $author->full_name . ' — Цитати з книг автора')

@section('main')
    <div id="author-quotes-app" class="max-w-7xl mx-auto space-y-8">
        <nav class="text-sm text-slate-500 dark:text-slate-400 relative mb-8">
            <div class="relative overflow-x-auto scrollbar-hide">
                <ol class="flex items-center gap-2 flex-nowrap min-w-max">
                    <li><a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 whitespace-nowrap">Головна</a></li>
                    <li class="flex-shrink-0">/</li>
                    <li><a href="{{ route('authors.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 whitespace-nowrap">Автори</a></li>
                    <li class="flex-shrink-0">/</li>
                    <li><a href="{{ route('authors.show', $author->slug) }}"
                           class="hover:text-indigo-600 dark:hover:text-indigo-400 whitespace-nowrap">{{ $author->full_name }}</a></li>
                    <li class="flex-shrink-0">/</li>
                    <li class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">Цитати</li>
                </ol>
            </div>
            <!-- Gradient fade on the right -->
            <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-light-bg to-transparent dark:from-dark-bg pointer-events-none"></div>
        </nav>

        <div class="w-full flex items-start">
            <div class="w-full max-w-[220px] hidden md:block lg:sticky sm:sticky top-4">
                @if ($author->photo_url)
                    <img src="{{ $author->photo_url }}" alt="{{ $author->full_name }}" class="w-full object-cover rounded-lg"
                         style="aspect-ratio: 3 / 4;">
                @else
                    <div
                        class="w-full aspect-[3/4] bg-slate-100 dark:bg-slate-700 flex items-center justify-center rounded-lg">
                        <svg class="w-16 h-16 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                @endif
                <div class="py-4 space-y-2">
                    <p class="text-xs uppercase text-slate-500 dark:text-slate-400 mb-1">Автор</p>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $author->full_name }}</h2>
                </div>
            </div>

            <div class="w-full flex-1 space-y-2">
                <div class="lg:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">
                                Усі цитати з книг автора
                            </p>
                            <h1 class="text-md md:text-2xl lg:text-3xl font-black text-slate-900 dark:text-white">
                                {{ $author->full_name }}
                            </h1>
                            <p class="text-sm text-slate-600 dark:text-slate-300 mt-2">
                                Всього цитат: <span class="font-semibold">{{ $quotesCount }}</span>
                            </p>
                        </div>
                        <a href="{{ route('authors.show', $author->slug) }}"
                           class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200">
                            <i class="fas fa-user mr-2"></i> До автора
                        </a>
                    </div>
                </div>

                <div class="pt-4 lg:pt-0 p-0">
                    <quotes-page-list :quotes='@json($quotesData)'
                        book-slug=""
                        :current-user-id="{{ auth()->check() ? auth()->id() : 'null' }}">
                    </quotes-page-list>

                    @if(isset($quotesPaginator) && $quotesPaginator->hasPages())
                        <div class="mt-6">
                            {{ $quotesPaginator->onEachSide(1)->links('vendor.pagination.custom') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Vue === 'undefined') return;

            new Vue({
                el: '#author-quotes-app',
            });
        });
    </script>
@endpush


