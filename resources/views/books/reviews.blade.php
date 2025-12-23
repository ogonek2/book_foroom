@extends('layouts.app')

@section('title', $book->title . ' — Рецензії та відгуки')

@section('main')
    <div id="book-reviews-app" class="max-w-7xl mx-auto space-y-8">
        <nav class="text-sm text-slate-500 dark:text-slate-400 relative mb-8">
            <div class="relative overflow-x-auto scrollbar-hide">
                <ol class="flex items-center gap-2 flex-nowrap min-w-max">
                    <li><a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 whitespace-nowrap">Головна</a></li>
                    <li class="flex-shrink-0">/</li>
                    <li><a href="{{ route('books.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 whitespace-nowrap">Книги</a>
                    </li>
                    <li class="flex-shrink-0">/</li>
                    <li><a href="{{ route('books.show', $book) }}"
                            class="hover:text-indigo-600 dark:hover:text-indigo-400 whitespace-nowrap">{{ $book->title }}</a></li>
                    <li class="flex-shrink-0">/</li>
                    <li class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">Рецензії</li>
                </ol>
            </div>
            <!-- Gradient fade on the right -->
            <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-light-bg to-transparent dark:from-dark-bg pointer-events-none"></div>
        </nav>

        <div class="w-full flex items-start">
            <div class="w-full max-w-[220px] hidden md:block lg:sticky sm:sticky top-4">
                <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=450&fit=crop&crop=center' }}"
                    alt="{{ $book->title }}" class="w-full object-cover rounded-lg" style="aspect-ratio: 2 / 3;">
                <div class="py-4 space-y-4">
                    <div>
                        <p class="text-xs uppercase text-slate-500 dark:text-slate-400 mb-1">Книга</p>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $book->title }}</h2>
                        <p class="text-sm text-slate-600 dark:text-slate-300">
                            @if ($authorModel)
                                <a href="{{ route('authors.show', $authorModel->slug) }}"
                                    class="hover:text-brand-500 dark:hover:text-brand-400">
                                    {{ $authorModel->full_name ?? $authorModel->short_name ?? $book->author }}
                                </a>
                            @else
                                {{ $book->author }}
                            @endif
                        </p>
                    </div>
                    @if ($book->categories->isNotEmpty())
                        <div class="flex flex-wrap gap-2">
                            @foreach ($book->categories->take(4) as $category)
                                <span
                                    class="px-3 py-1 rounded-full text-xs bg-purple-100/80 dark:bg-purple-500/20 text-purple-700 dark:text-purple-200">
                                    #{{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="w-full flex-1 space-y-2">
                <div class="lg:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex gap-4 items-center">
                            <div class="w-full max-w-[120px] block md:hidden lg:hidden">
                                <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=450&fit=crop&crop=center' }}"
                                    alt="{{ $book->title }}" class="w-full object-cover rounded-lg"
                                    style="aspect-ratio: 2 / 3;">
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Усі
                                    рецензії
                                    на книгу</p>
                                <h1 class="text-md md:text-2xl lg:text-3xl font-black text-slate-900 dark:text-white">{{ $book->title }}</h1>
                                <p class="text-sm md:text-md lg:text-lg text-slate-600 dark:text-slate-300">
                                    @if ($authorModel)
                                        <a href="{{ route('authors.show', $authorModel->slug) }}"
                                            class="hover:text-brand-500 dark:hover:text-brand-400">
                                            {{ $authorModel->full_name ?? $authorModel->short_name ?? $book->author }}
                                        </a>
                                    @else
                                        {{ $book->author }}
                                    @endif
                                </p>
                                <div class="flex gap-6 mt-4">
                                    <div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase">Середня оцінка</p>
                                        <p class="text-2xl font-bold text-slate-900 dark:text-white">
                                            {{ number_format($book->rating, 1) }}/10
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500 dark:text-сlate-400 uppercase">Рецензій</p>
                                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $reviewsCount }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('books.show', $book) }}"
                            class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200">
                            <i class="fas fa-book-open mr-2"></i> До книги
                        </a>
                        <a href="{{ route('books.reviews.create', $book) }}"
                            class="inline-flex items-center px-5 py-2 rounded-xl text-white bg-gradient-to-r from-indigo-500 to-purple-600 shadow-lg">
                            <i class="fas fa-plus mr-2"></i> Написати рецензію
                        </a>
                    </div>
                </div>

                <div class="pt-4 lg:pt-0 p-0">
                    <book-reviews-list :reviews='@json($reviewsData)' book-slug="{{ $book->slug }}"
                        :current-user-id="{{ auth()->check() ? auth()->id() : 'null' }}"
                        :user-review='@json($userReviewData)' :hide-header="true" :hide-add-button="true"
                        @@show-notification="showNotification">
                    </book-reviews-list>

                    @if(isset($reviewsPaginator) && $reviewsPaginator->hasPages())
                        <div class="mt-6">
                            {{ $reviewsPaginator->onEachSide(1)->links('vendor.pagination.custom') }}
                        </div>
                    @endif
                </div>
            </div>

            <aside class="space-y-6 w-full max-w-[220px] hidden lg:block lg:sticky top-4">

                @php $maxDistribution = max($ratingDistribution ?: [0]); @endphp
                <div
                    class="bg-white dark:bg-slate-900/80 rounded-3xl border border-white/30 dark:border-slate-800/60 shadow-xl p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 uppercase">Рейтинг спільноти</p>
                            <p class="text-sm text-сlate-500 dark:text-slate-400">Розподіл оцінок</p>
                        </div>
                        <p class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($book->rating, 1) }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        @for ($rating = 10; $rating >= 1; $rating--)
                            @php
                                $count = $ratingDistribution[$rating] ?? 0;
                                $percentage = $maxDistribution > 0 ? ($count / $maxDistribution) * 100 : 0;
                            @endphp
                            <div class="flex items-center gap-3">
                                <span class="w-6 text-xs text-slate-500 dark:text-slate-400">{{ $rating }}</span>
                                <div class="flex-1 h-2 rounded-full bg-slate-200/80 dark:bg-slate-800">
                                    <div class="h-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500"
                                        style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="w-8 text-xs text-right text-slate-500 dark:text-slate-400">{{ $count }}</span>
                            </div>
                        @endfor
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-slate-900/80 rounded-3xl border border-white/30 dark:border-slate-800/60 shadow-xl p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Статистика читання</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500 dark:text-slate-400">Прочитали</dt>
                            <dd class="text-lg font-bold text-slate-900 dark:text-white">{{ $readingStats['read'] }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500 dark:text-slate-400">Читають зараз</dt>
                            <dd class="text-lg font-bold text-slate-900 dark:text-white">{{ $readingStats['reading'] }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500 dark:text-сlate-400">Хочуть прочитати</dt>
                            <dd class="text-lg font-bold text-slate-900 dark:text-white">{{ $readingStats['want_to_read'] }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </aside>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .reviews-list-container {
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            border-radius: 0 !important;

            .reviews-list-container-header {
                border: none !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Vue === 'undefined') return;

            new Vue({
                el: '#book-reviews-app',
                methods: {
                    showNotification(message, type = 'success') {
                        document.querySelectorAll('.notification').forEach(node => node.remove());

                        const node = document.createElement('div');
                        node.className = `notification fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${type === 'success'
                            ? 'bg-green-500 text-white'
                            : type === 'error'
                                ? 'bg-red-500 text-white'
                                : 'bg-slate-800 text-white'
                            }`;
                        node.textContent = message;
                        document.body.appendChild(node);

                        requestAnimationFrame(() => node.style.transform = 'translateX(0)');
                        setTimeout(() => {
                            node.style.transform = 'translateX(100%)';
                            setTimeout(() => node.remove(), 300);
                        }, 3000);
                    }
                }
            });
        });
    </script>
@endpush