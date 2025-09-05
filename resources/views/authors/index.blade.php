@extends('layouts.app')

@section('title', 'Автори - Книжковий форум')

@section('main')
<div class="min-h-screen bg-light-bg dark:bg-dark-bg transition-colors duration-300">

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sticky Sidebar -->
            <div class="lg:w-80 flex-shrink-0">
                <div class="sticky top-24 space-y-6 max-h-screen overflow-y-auto">
                    <!-- Search -->
                    <div class="bg-light-bg dark:bg-dark-bg-secondary rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-light-text-primary dark:text-dark-text-primary mb-4">Пошук</h3>
                        <form method="GET" action="{{ route('authors.index') }}">
                            <div class="relative">
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Пошук автора..."
                                       class="w-full pl-10 pr-4 py-3 border border-light-border dark:border-dark-border rounded-lg bg-light-bg dark:bg-dark-bg text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all duration-200">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-light-text-tertiary dark:text-dark-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Alphabet Filter -->
                    <div class="bg-light-bg dark:bg-dark-bg-secondary rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-light-text-primary dark:text-dark-text-primary mb-4">Алфавіт</h3>
                        <div class="space-y-2">
                            <a href="{{ route('authors.index', request()->except('letter')) }}" 
                               class="block px-3 py-2 text-sm rounded-lg {{ !request('letter') ? 'bg-brand-500 text-white' : 'bg-light-bg dark:bg-dark-bg text-light-text-secondary dark:text-dark-text-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary' }} transition-colors duration-200">
                                Всі
                            </a>
                            <div class="grid grid-cols-6 gap-1">
                                @foreach($letters as $letter)
                                    <a href="{{ route('authors.index', array_merge(request()->except('letter'), ['letter' => $letter])) }}" 
                                       class="px-2 py-1 text-xs text-center rounded {{ request('letter') == $letter ? 'bg-brand-500 text-white' : 'bg-light-bg dark:bg-dark-bg text-light-text-secondary dark:text-dark-text-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary' }} transition-colors duration-200">
                                        {{ $letter }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>


                    <!-- Sort Options -->
                    <div class="bg-light-bg dark:bg-dark-bg-secondary rounded-xl p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-light-text-primary dark:text-dark-text-primary mb-4">Сортування</h3>
                        <div class="space-y-2">
                            <a href="{{ route('authors.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'last_name', 'direction' => 'asc'])) }}" 
                               class="block px-3 py-2 text-sm rounded-lg {{ request('sort') == 'last_name' && request('direction') == 'asc' ? 'bg-brand-500 text-white' : 'bg-light-bg dark:bg-dark-bg text-light-text-secondary dark:text-dark-text-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary' }} transition-colors duration-200">
                                Прізвище А-Я
                            </a>
                            <a href="{{ route('authors.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'last_name', 'direction' => 'desc'])) }}" 
                               class="block px-3 py-2 text-sm rounded-lg {{ request('sort') == 'last_name' && request('direction') == 'desc' ? 'bg-brand-500 text-white' : 'bg-light-bg dark:bg-dark-bg text-light-text-secondary dark:text-dark-text-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary' }} transition-colors duration-200">
                                Прізвище Я-А
                            </a>
                            <a href="{{ route('authors.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'books_count', 'direction' => 'desc'])) }}" 
                               class="block px-3 py-2 text-sm rounded-lg {{ request('sort') == 'books_count' && request('direction') == 'desc' ? 'bg-brand-500 text-white' : 'bg-light-bg dark:bg-dark-bg text-light-text-secondary dark:text-dark-text-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary' }} transition-colors duration-200">
                                Кількість книг
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Authors Grid -->
            <div class="flex-1 min-w-0">
                @if($authors->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($authors as $author)
                            <div class="bg-light-bg dark:bg-dark-bg-secondary rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 group">
                                <a href="{{ route('authors.show', $author) }}" class="block">
                                    <!-- Author Photo -->
                                    <div class="aspect-[3/4] overflow-hidden">
                                        @if($author->photo_url)
                                            <img src="{{ $author->photo_url }}" 
                                                 alt="{{ $author->full_name }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full bg-light-bg-secondary dark:bg-dark-bg-secondary flex items-center justify-center group-hover:bg-light-bg-tertiary dark:group-hover:bg-dark-bg-secondary transition-colors duration-300">
                                                <svg class="w-12 h-12 text-light-text-tertiary dark:text-dark-text-tertiary group-hover:text-brand-500 dark:group-hover:text-brand-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Author Info -->
                                    <div class="p-4">
                                        <h3 class="text-base font-semibold text-light-text-primary dark:text-dark-text-primary mb-2 line-clamp-2">
                                            {{ $author->full_name }}
                                        </h3>
                                        
                                        @if($author->nationality)
                                            <p class="text-sm text-light-text-tertiary dark:text-dark-text-tertiary mb-3">
                                                {{ $author->nationality }}
                                            </p>
                                        @endif
                                        
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-light-text-secondary dark:text-dark-text-secondary">
                                                {{ $author->books_count }} {{ Str::plural('книга', $author->books_count) }}
                                            </span>
                                            
                                            @if($author->is_featured)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-brand-500/10 text-brand-500">
                                                    Рекомендовано
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12">
                        {{ $authors->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="max-w-md mx-auto">
                            <svg class="mx-auto h-24 w-24 text-light-text-tertiary dark:text-dark-text-tertiary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-light-text-primary dark:text-dark-text-primary mb-2">
                                Автори не знайдені
                            </h3>
                            <p class="text-light-text-secondary dark:text-dark-text-secondary mb-6">
                                Спробуйте змінити параметри пошуку або фільтри
                            </p>
                            <a href="{{ route('authors.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg font-medium transition-colors duration-200">
                                Скинути фільтри
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection