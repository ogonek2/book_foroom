@extends('layouts.app')

@section('title', 'Каталог категорій')

@section('main')
<div id="categories-catalog" class="max-w-7xl mx-auto pb-20" v-cloak>
    {{-- Hero --}}
    <header class="pt-6 pb-10 text-center sm:text-left">
        <p class="text-sm font-semibold tracking-wide text-orange-600 dark:text-orange-400 uppercase mb-2">FoxyBooks</p>
        <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 dark:text-white tracking-tight leading-tight"
            style="font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif;">
            Каталог категорій
        </h1>
        <p class="mt-3 text-lg text-gray-500 dark:text-gray-400 max-w-xl">
            Обирайте жанр — як у Apple Books. @{{ pagination.total }} категорій у каталозі.
        </p>

        <div class="mt-8 max-w-md mx-auto sm:mx-0">
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="search" v-model.trim="search" v-on:input="onSearchInput"
                    placeholder="Пошук категорії…"
                    class="w-full pl-12 pr-4 py-3.5 rounded-full bg-gray-100 dark:bg-gray-800/80 border-0 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500/60 text-base shadow-sm">
            </div>
        </div>
    </header>

    {{-- Grid --}}
    <div v-if="cards.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-3">
        <a v-for="card in cards" :key="card.id" :href="card.url"
            class="category-tile group block relative overflow-hidden rounded-3xl aspect-[4/3] bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm border border-gray-200/30 dark:border-gray-700/30 shadow-lg hover:shadow-xl hover:scale-[1.02] transition-all duration-300 ease-out">
            <div class="absolute inset-0 bg-gradient-to-br from-white/50 via-transparent to-gray-900/[0.04] dark:from-white/[0.06] dark:via-transparent dark:to-black/25 pointer-events-none"></div>

            <div class="relative z-10 p-5 sm:p-6 flex flex-col h-full">
                <div class="flex-1 min-h-0">
                    <p v-if="card.parent_name" class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 truncate">
                        @{{ card.parent_name }}
                    </p>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white leading-tight tracking-tight line-clamp-2"
                        style="font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', sans-serif;">
                        @{{ card.name }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">
                        @{{ booksCountLabel(card.books_count) }}
                        <span v-if="card.children_count > 0"> · @{{ card.children_count }} підкат.</span>
                    </p>
                </div>

                <div class="cover-fan mt-auto pt-4" v-if="card.books && card.books.length">
                    <img v-for="(book, idx) in card.books.slice(0, 4)" :key="idx"
                        :src="book.cover" :alt="book.title"
                        :style="coverFanStyle(idx, card.books.length)"
                        class="cover-fan-item"
                        loading="lazy"
                        v-on:error="onCoverError">
                </div>
                <div v-else class="mt-auto pt-4 text-sm text-gray-400 dark:text-gray-500 italic">Поки немає книг</div>
            </div>
        </a>
    </div>

    {{-- Empty --}}
    <div v-else-if="!loading" class="text-center py-20">
        <p class="text-lg text-gray-500 dark:text-gray-400">Категорій не знайдено</p>
        <button type="button" v-on:click="resetSearch" class="mt-4 text-orange-600 dark:text-orange-400 font-medium hover:underline">
            Скинути пошук
        </button>
    </div>

    {{-- Skeleton while first load / search --}}
    <div v-if="loading && cards.length === 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
        <div v-for="n in 6" :key="'sk-'+n" class="rounded-3xl aspect-[4/3] bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm border border-gray-200/30 dark:border-gray-700/30 animate-pulse"></div>
    </div>

    {{-- Load more --}}
    <div class="mt-12 flex flex-col items-center gap-3" v-if="cards.length">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Показано @{{ cards.length }} з @{{ pagination.total }}
        </p>
        <button v-if="pagination.has_more" type="button" v-on:click="loadMore" :disabled="loadingMore"
            class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-semibold text-sm hover:opacity-90 disabled:opacity-50 transition-all shadow-lg">
            <svg v-if="loadingMore" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span>@{{ loadingMore ? 'Завантаження…' : 'Завантажити ще' }}</span>
        </button>
        <p v-else class="text-sm text-gray-400">Усі категорії завантажено</p>
    </div>
</div>
@endsection

@push('styles')
<style>
    .category-tile {
        -webkit-tap-highlight-color: transparent;
    }

    .cover-fan {
        position: relative;
        height: 7.5rem;
        margin-bottom: -0.25rem;
    }

    .cover-fan-item {
        position: absolute;
        bottom: 0;
        width: 4.25rem;
        aspect-ratio: 2 / 3;
        border-radius: 0.4rem;
        object-fit: cover;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        border: 1px solid rgba(0, 0, 0, 0.06);
        transition: transform 0.3s ease;
    }

    .dark .cover-fan-item {
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.45);
        border-color: rgba(255, 255, 255, 0.12);
    }

    .category-tile:hover .cover-fan-item {
        transform: translateY(-4px) rotate(var(--fan-rotate, 0deg)) !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.Vue) return;

    new Vue({
        el: '#categories-catalog',
        data: {
            cards: @json($initialCards),
            pagination: @json($pagination),
            search: '',
            loading: false,
            loadingMore: false,
            searchTimer: null,
            cardsUrl: @json(route('categories.cards')),
            placeholderCover: @json(\App\Services\ImagePlaceholderService::bookCoverUrl()),
        },
        methods: {
            coverFanStyle(index, total) {
                const slots = Math.min(total, 4);
                const baseLeft = 8;
                const step = slots > 1 ? 72 / (slots - 1) : 0;
                const rotations = [-14, -6, 4, 12];
                const left = baseLeft + index * step;
                const rotate = rotations[index] ?? 0;
                return {
                    left: left + '%',
                    zIndex: index + 1,
                    transform: `rotate(${rotate}deg)`,
                    '--fan-rotate': rotate + 'deg',
                };
            },
            booksCountLabel(count) {
                const n = Number(count) || 0;
                const mod10 = n % 10;
                const mod100 = n % 100;
                if (mod10 === 1 && mod100 !== 11) return n + ' книга';
                if (mod10 >= 2 && mod10 <= 4 && (mod100 < 10 || mod100 >= 20)) return n + ' книги';
                return n + ' книг';
            },
            onCoverError(e) {
                if (e.target.src !== this.placeholderCover) {
                    e.target.src = this.placeholderCover;
                }
            },
            fetchCards(page, append) {
                const params = { page };
                if (this.search) params.search = this.search;

                const axiosInstance = window.axios;
                if (!axiosInstance) return Promise.reject();

                return axiosInstance.get(this.cardsUrl, {
                    params,
                    headers: { Accept: 'application/json' },
                }).then(response => {
                    const data = response.data;
                    if (append) {
                        const existing = new Set(this.cards.map(c => c.id));
                        data.data.forEach(card => {
                            if (!existing.has(card.id)) this.cards.push(card);
                        });
                    } else {
                        this.cards = data.data;
                    }
                    this.pagination = {
                        current_page: data.current_page,
                        last_page: data.last_page,
                        total: data.total,
                        has_more: data.has_more,
                        per_page: this.pagination.per_page,
                    };
                });
            },
            loadMore() {
                if (!this.pagination.has_more || this.loadingMore) return;
                this.loadingMore = true;
                this.fetchCards(this.pagination.current_page + 1, true)
                    .finally(() => { this.loadingMore = false; });
            },
            onSearchInput() {
                clearTimeout(this.searchTimer);
                this.searchTimer = setTimeout(() => {
                    this.loading = true;
                    this.fetchCards(1, false).finally(() => { this.loading = false; });
                }, 350);
            },
            resetSearch() {
                this.search = '';
                this.loading = true;
                this.fetchCards(1, false).finally(() => { this.loading = false; });
            },
        },
    });
});
</script>
@endpush
