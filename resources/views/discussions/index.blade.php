@extends('layouts.app')

@section('title', 'Обговорення')

@section('main')
    <div id="app" class="min-h-screen bg-light-bg dark:bg-dark-bg transition-colors duration-300">
        <!-- Header -->
        <div class="">
            <div class="mx-auto">
                <div class="flex items-center justify-between py-8">
                    <div>
                        <h1 class="text-4xl font-bold text-light-text-primary dark:text-dark-text-primary mb-2">
                            Обговорення
                        </h1>
                        <p class="text-light-text-secondary dark:text-dark-text-secondary">
                            Знайдіть цікаві теми для обговорення або створіть власне
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
                            class="bg-gradient-to-r from-brand-500 to-accent-500 text-white px-6 py-2 rounded-xl font-bold hover:from-brand-600 hover:to-accent-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-plus mr-2"></i>
                            Створити тему
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="mx-auto pb-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar with Filters (Desktop) -->
                <div class="hidden lg:block lg:w-80 flex-shrink-0 order-2 lg:order-1">
                    <div class="sticky" style="top: 2rem;">
                        <content-filters :active-filter="'{{ $filter }}'" :sort-by="'{{ $sortBy }}'"
                            @filter-changed="handleFilterChange" @sort-changed="handleSortChange"
                            @filters-applied="handleFiltersApplied">
                        </content-filters>
                    </div>
                </div>

                <!-- Content List -->
                <div class="flex-1 order-1 lg:order-2">
                    <unified-content-list :discussions="{{ json_encode($discussions->values()->toArray()) }}"
                        :reviews="{{ json_encode($reviews->values()->toArray()) }}" :active-filter="'{{ $filter }}'"
                        :sort-by="'{{ $sortBy }}'" :current-page="{{ request()->get('page', 1) }}"
                        @page-changed="handlePageChange">
                    </unified-content-list>
                </div>
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
                            <content-filters :active-filter="'{{ $filter }}'" :sort-by="'{{ $sortBy }}'"
                                :mobile="true"
                                @filter-changed="handleFilterChange" @sort-changed="handleSortChange"
                                @filters-applied="handleFiltersApplied">
                            </content-filters>

                            <!-- Apply Button -->
                            <div
                                class="sticky bottom-0 pt-4 pb-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 -mx-6 px-6 mt-4">
                                <button @@click="showMobileFilters = false"
                                    class="w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold transition-colors shadow-lg">
                                    Застосувати фільтри
                                </button>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </transition>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Vue приложение для страницы обсуждений
                const discussionsApp = new Vue({
                    el: '#app',
                    data: {
                        showMobileFilters: false,
                    },
                    methods: {
                        handleFilterChange(filter) {
                            console.log('Filter changed to:', filter);
                            this.updateUrl({ filter: filter, page: 1 });
                        },

                        handleSortChange(sortBy) {
                            console.log('Sort changed to:', sortBy);
                            this.updateUrl({ sort: sortBy, page: 1 });
                        },

                        handleFiltersApplied(filters) {
                            console.log('Filters applied:', filters);
                            this.updateUrl({
                                filter: filters.filter,
                                sort: filters.sort,
                                page: 1
                            });
                        },

                        handlePageChange(page) {
                            console.log('Page changed to:', page);
                            const urlParams = new URLSearchParams(window.location.search);
                            urlParams.set('page', page);

                            window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
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

                            window.location.href = url.toString();
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