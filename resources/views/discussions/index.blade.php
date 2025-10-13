@extends('layouts.app')

@section('title', 'Обговорення')

@section('main')
    <div id="app" class="min-h-screen bg-light-bg dark:bg-dark-bg transition-colors duration-300">
        <!-- Header -->
        <div class="">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-8">
                    <div>
                        <h1 class="text-4xl font-bold text-light-text-primary dark:text-dark-text-primary mb-2">
                            Обговорення
                        </h1>
                        <p class="text-light-text-secondary dark:text-dark-text-secondary">
                            Знайдіть цікаві теми для обговорення або створіть власне
                        </p>
                    </div>
                    @auth
                        <a href="{{ route('discussions.create') }}"
                            class="bg-gradient-to-r from-brand-500 to-accent-500 text-white px-6 py-3 rounded-xl font-bold hover:from-brand-600 hover:to-accent-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-plus mr-2"></i>
                            Створити обговорення
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar with Filters -->
                <div class="w-full lg:w-80 flex-shrink-0 order-2 lg:order-1">
                    <div class="sticky" style="top: 2rem;">
                        <content-filters 
                            :active-filter="'{{ $filter }}'"
                            :sort-by="'{{ $sortBy }}'"
                            @filter-changed="handleFilterChange"
                            @sort-changed="handleSortChange"
                            @filters-applied="handleFiltersApplied">
                        </content-filters>
                    </div>
                </div>

                <!-- Content List -->
                <div class="flex-1 order-1 lg:order-2">
                    <unified-content-list 
                        :discussions="{{ json_encode($discussions->values()->toArray()) }}"
                        :reviews="{{ json_encode($reviews->values()->toArray()) }}"
                        :active-filter="'{{ $filter }}'"
                        :sort-by="'{{ $sortBy }}'"
                        :current-page="{{ request()->get('page', 1) }}"
                        @page-changed="handlePageChange">
                    </unified-content-list>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Vue приложение для страницы обсуждений
                const discussionsApp = new Vue({
                    el: '#app',
                    data: {
                        // Данные для Vue приложения
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
@endsection