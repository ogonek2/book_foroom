@extends('layouts.app')

@section('title', 'Обговорення - Книжковий форум')

@section('description', 'Обговорення книг на FOXY. Знайдіть цікаві теми для обговорення або створіть власну.')
@section('keywords', 'обговорення, книги, форум, рецензії, FOXY')
@section('canonical', route('discussions.index'))
@section('og_type', 'website')
@section('og_title', 'Обговорення - FOXY')
@section('og_description', 'Обговорення книг на FOXY. Знайдіть цікаві теми для обговорення або створіть власну.')
@section('og_url', route('discussions.index'))
@section('og_image', asset('favicon.svg'))
@section('twitter_title', 'Обговорення - FOXY')
@section('twitter_description', 'Обговорення книг на FOXY. Знайдіть цікаві теми для обговорення або створіть власну.')
@section('twitter_image', asset('favicon.svg'))

@section('main')
    <div class="content-with-skeleton relative min-h-[480px]">
    <div id="discussions-app" class="min-h-screen bg-light-bg dark:bg-dark-bg transition-colors duration-300" v-cloak>
        <!-- Header -->
        <div class="flex items-center justify-between py-8">
            <div>
                <h1 class="text-4xl font-bold text-light-text-primary dark:text-dark-text-primary mb-2">
                    Обговорення
                </h1>
                <p class="text-light-text-secondary dark:text-dark-text-secondary">
                    Знайдіть цікаві теми для обговорення або створіть власну
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3 mb-4">
            <!-- Mobile Filter Button -->
            <button @@click="showMobileFilters = true"
                class="lg:hidden flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold transition-colors shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span>Фільтри</span>
            </button>
            @auth
                <a href="{{ route('discussions.create') }}"
                    class="lg:hidden bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-500 text-white px-6 py-2 rounded-xl font-bold hover:from-violet-600 hover:via-purple-600 hover:to-fuchsia-600 transition-all duration-300 transform hover:scale-105 shadow-lg shadow-purple-500/30">
                    <i class="fas fa-plus mr-2"></i>
                    Створити тему
                </a>
            @endauth
        </div>

        <!-- Main Content -->
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar with Filters (Desktop) -->
            <div class="hidden lg:block lg:w-80 flex-shrink-0 order-2 lg:order-1">
                <div class="sticky" style="top: 2rem;">
                    <content-filters :active-filter="currentFilter" :sort-by="currentSortBy"
                        :create-discussion-url="{{ auth()->check() ? json_encode(route('discussions.create')) : 'null' }}"
                        @filter-changed="handleFilterChange" @sort-changed="handleSortChange">
                    </content-filters>
                </div>
            </div>

            <!-- Content List -->
            <div class="flex-1 order-1 lg:order-2 min-w-0">
                <unified-content-list :discussions='@json($discussionsData)'
                    :reviews='@json($reviewsData)' :active-filter="currentFilter"
                    :sort-by="currentSortBy">
                </unified-content-list>
            </div>
        </div>

        <!-- Mobile Filters Sidebar -->
        <transition name="fade">
            <div v-if="showMobileFilters" class="lg:hidden fixed inset-0 z-50 overflow-hidden"
                @@click.self="showMobileFilters = false">
                <!-- Overlay -->
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @@click="showMobileFilters = false"></div>

                <!-- Sidebar -->
                <transition name="slide-left">
                    <div v-if="showMobileFilters"
                        class="absolute right-0 top-0 h-full w-full max-w-sm bg-white dark:bg-gray-800 shadow-2xl overflow-y-auto">
                        <!-- Header -->
                        <div
                            class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between z-10">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Фільтри</h2>
                            <button @@click="showMobileFilters = false"
                                class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Filters Content -->
                        <div class="p-6 space-y-6">
                            <content-filters :active-filter="currentFilter" :sort-by="currentSortBy"
                                :mobile="true"
                                :create-discussion-url="{{ auth()->check() ? json_encode(route('discussions.create')) : 'null' }}"
                                @filter-changed="handleFilterChange" @sort-changed="handleSortChange">
                            </content-filters>

                            <!-- Close Button -->
                            <div
                                class="sticky bottom-0 pt-4 pb-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 -mx-6 px-6 mt-4">
                                <button @@click="showMobileFilters = false"
                                    class="w-full px-4 py-3 bg-gradient-to-r from-violet-500 via-purple-500 to-fuchsia-500 hover:from-violet-600 hover:via-purple-600 hover:to-fuchsia-600 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg shadow-purple-500/30">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Готово
                                </button>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </div>

    <!-- Skeleton: список обговорень/контенту -->
    <div class="skeleton-placeholder pointer-events-none" aria-hidden="true">
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="hidden lg:block lg:w-80 flex-shrink-0"><div class="h-48 skeleton rounded-2xl"></div></div>
            <div class="flex-1 space-y-4">
                @for($i = 0; $i < 5; $i++)
                <div class="bg-white/60 dark:bg-gray-800/60 rounded-2xl border border-gray-200/30 dark:border-gray-700/30 p-6">
                    <div class="flex gap-4">
                        <div class="skeleton w-12 h-12 rounded-full flex-shrink-0"></div>
                        <div class="flex-1 space-y-2">
                            <div class="skeleton h-4 w-1/3 rounded"></div>
                            <div class="skeleton h-3 w-full rounded"></div>
                            <div class="skeleton h-3 w-2/3 rounded"></div>
                            <div class="skeleton h-3 w-1/4 rounded"></div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Vue приложение для страницы обсуждений
                const discussionsApp = new Vue({
                    el: '#discussions-app',
                    data: {
                        showMobileFilters: false,
                        currentFilter: '{{ $filter }}',
                        currentSortBy: '{{ $sortBy }}',
                    },
                    methods: {
                        handleFilterChange(filter) {
                            this.currentFilter = filter;
                            this.updateUrl({ filter: filter, page: 1 });
                        },

                        handleSortChange(sortBy) {
                            this.currentSortBy = sortBy;
                            this.updateUrl({ sort: sortBy, page: 1 });
                        },


                        updateUrl(params) {
                            const url = new URL(window.location);

                            Object.keys(params).forEach(key => {
                                if (params[key]) {
                                    url.searchParams.set(key, params[key]);
                                } else {
                                    url.searchParams.delete(key);
                                }
                            });

                            // Обновляем URL без перезагрузки страницы
                            window.history.pushState({}, '', url.toString());
                        }
                    },

                    mounted() {
                        console.log('Discussions Vue app mounted');
                    }
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .slide-left-enter-active {
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .slide-left-leave-active {
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .slide-left-enter {
                transform: translateX(100%);
            }

            .slide-left-enter-to {
                transform: translateX(0);
            }

            .slide-left-leave {
                transform: translateX(0);
            }

            .slide-left-leave-to {
                transform: translateX(100%);
            }
        </style>
    @endpush
@endsection