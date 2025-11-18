@extends('layouts.app')

@section('title', 'Пошук книг')

@section('main')
<div class="min-h-screen bg-light-bg dark:bg-dark-bg transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-light-text-primary dark:text-dark-text-primary mb-2">
                Результати пошуку
            </h1>
            @if($query)
                <p class="text-light-text-secondary dark:text-dark-text-secondary">
                    Знайдено результатів для запиту: <span class="font-semibold text-brand-500 dark:text-brand-400">"{{ $query }}"</span>
                </p>
            @endif
        </div>

        <!-- Search Form -->
        <div class="mb-8">
            <div id="search-form-app">
                <book-search :initial-value="'{{ $query }}'" placeholder="Пошук книг..."></book-search>
            </div>
        </div>

        <!-- Results -->
        @if($query && $books->count() > 0)
            <div class="mb-6">
                <p class="text-light-text-secondary dark:text-dark-text-secondary">
                    Знайдено {{ $books->total() }} {{ $books->total() === 1 ? 'книгу' : ($books->total() < 5 ? 'книги' : 'книг') }}
                </p>
            </div>

            <!-- Books Grid -->
            <div id="search-results-app" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 mb-8">
                @foreach($books as $book)
                    <div>
                        <book-card
                            :book="{{ json_encode([
                                'id' => $book->id,
                                'title' => $book->title,
                                'slug' => $book->slug,
                                'author' => $book->author ?? 'Невідомий автор',
                                'cover_image' => $book->cover_image_display ?? $book->cover_image ?? null,
                                'rating' => (float) ($book->rating ?? 0),
                                'reviews_count' => (int) ($book->reviews_count ?? 0),
                                'category' => $book->categories->first()->name ?? 'Без категорії',
                                'publication_year' => $book->publication_year ?? 'N/A',
                                'description' => $book->annotation ?? '',
                                'pages' => (int) ($book->pages ?? 0),
                            ]) }}"
                            :is-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
                            :user="{{ auth()->check() ? json_encode(auth()->user()) : 'null' }}"
                            :user-libraries="{{ json_encode($userLibraries->map(function($lib) { return ['id' => $lib->id, 'name' => $lib->name]; })->values()) }}">
                        </book-card>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($books->hasPages())
                <div class="flex justify-center mt-8">
                    {{ $books->links() }}
                </div>
            @endif
        @elseif($query && $books->count() === 0)
            <!-- No Results -->
            <div class="text-center py-16">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 border border-gray-200 dark:border-gray-700 shadow-xl">
                    <svg class="w-24 h-24 mx-auto mb-6 text-light-text-tertiary dark:text-dark-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-light-text-primary dark:text-dark-text-primary mb-4">
                        Нічого не знайдено
                    </h3>
                    <p class="text-light-text-secondary dark:text-dark-text-secondary mb-6">
                        Спробуйте змінити запит або перевірте правильність написання
                    </p>
                    <a href="{{ route('books.index') }}" 
                       class="inline-block bg-gradient-to-r from-brand-500 to-accent-500 text-white px-6 py-3 rounded-xl font-medium hover:from-brand-600 hover:to-accent-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Переглянути всі книги
                    </a>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 border border-gray-200 dark:border-gray-700 shadow-xl">
                    <svg class="w-24 h-24 mx-auto mb-6 text-light-text-tertiary dark:text-dark-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-light-text-primary dark:text-dark-text-primary mb-4">
                        Введіть запит для пошуку
                    </h3>
                    <p class="text-light-text-secondary dark:text-dark-text-secondary">
                        Пошук працює за назвою, автором, синонімами та видавництвом
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Vue for search form
        if (window.Vue && document.getElementById('search-form-app')) {
            new Vue({
                el: '#search-form-app'
            });
        }

        // Initialize Vue for book cards
        function initializeSearchResults() {
            if (window.Vue && document.getElementById('search-results-app')) {
                new Vue({
                    el: '#search-results-app',
                    data: {
                        isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
                        user: @json(auth()->check() ? auth()->user() : null),
                        userId: {{ auth()->id() ?? 'null' }},
                        userLibraries: @json($userLibraries->map(function($lib) { 
                            return ['id' => $lib->id, 'name' => $lib->name]; 
                        })->values())
                    },
                    mounted() {
                        console.log('Search results Vue app mounted');
                    }
                });
            } else if (window.Vue) {
                // Retry after a short delay if Vue is not ready
                setTimeout(initializeSearchResults, 100);
            }
        }

        initializeSearchResults();
    });
</script>
@endpush
@endsection
