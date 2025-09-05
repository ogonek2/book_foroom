@extends('layouts.app')

@section('title', $author->full_name . ' - Книжковий форум')

@section('main')
<div class="min-h-screen bg-light-bg dark:bg-dark-bg transition-colors duration-300">
    <!-- Author Header -->
    <section class="bg-gradient-to-r from-brand-500/10 to-accent-500/10 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
                <!-- Author Photo -->
                <div class="lg:col-span-1">
                    <div class="aspect-[3/4] max-w-sm mx-auto lg:mx-0">
                        @if($author->photo_url)
                            <img src="{{ $author->photo_url }}" 
                                 alt="{{ $author->full_name }}"
                                 class="w-full h-full object-cover rounded-2xl shadow-lg">
                        @else
                            <div class="w-full h-full bg-light-bg-secondary dark:bg-dark-bg-secondary flex items-center justify-center rounded-2xl shadow-lg">
                                <svg class="w-24 h-24 text-light-text-tertiary dark:text-dark-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Author Info -->
                <div class="lg:col-span-2">
                    <div class="text-center lg:text-left">
                        <h1 class="text-4xl md:text-5xl font-bold text-light-text-primary dark:text-dark-text-primary mb-4">
                            {{ $author->full_name }}
                        </h1>
                        
                        @if($author->nationality)
                            <p class="text-lg text-light-text-secondary dark:text-dark-text-secondary mb-4">
                                {{ $author->nationality }}
                            </p>
                        @endif
                        
                        @if($author->birth_date)
                            <div class="flex flex-wrap justify-center lg:justify-start gap-4 mb-6">
                                <div class="flex items-center text-light-text-tertiary dark:text-dark-text-tertiary">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $author->birth_date->format('d.m.Y') }}</span>
                                    @if($author->death_date)
                                        <span class="mx-2">-</span>
                                        <span>{{ $author->death_date->format('d.m.Y') }}</span>
                                    @endif
                                </div>
                                
                                @if($author->age)
                                    <div class="flex items-center text-light-text-tertiary dark:text-dark-text-tertiary">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $author->age }} років</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                            <div class="bg-light-bg dark:bg-dark-bg-secondary rounded-lg px-4 py-2">
                                <span class="text-sm text-light-text-tertiary dark:text-dark-text-tertiary">Книг:</span>
                                <span class="text-lg font-semibold text-light-text-primary dark:text-dark-text-primary ml-2">
                                    {{ $author->books_count }}
                                </span>
                            </div>
                            
                            @if($author->is_featured)
                                <div class="bg-brand-500/10 text-brand-500 rounded-lg px-4 py-2">
                                    <span class="text-sm font-medium">Рекомендовано</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Author Biography -->
    @if($author->biography)
        <section class="py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-light-bg dark:bg-dark-bg-secondary rounded-xl p-8 shadow-sm">
                    <h2 class="text-2xl font-bold text-light-text-primary dark:text-dark-text-primary mb-6">
                        Біографія
                    </h2>
                    <div class="prose prose-lg max-w-none text-light-text-secondary dark:text-dark-text-secondary leading-relaxed">
                        {!! nl2br(e($author->biography)) !!}
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Awards -->
    @if($author->awards)
        <section class="py-12 bg-light-bg-secondary dark:bg-dark-bg-secondary">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-light-bg dark:bg-dark-bg-secondary rounded-xl p-8 shadow-sm">
                    <h2 class="text-2xl font-bold text-light-text-primary dark:text-dark-text-primary mb-6">
                        Нагороди та визнання
                    </h2>
                    <div class="prose prose-lg max-w-none text-light-text-secondary dark:text-dark-text-secondary leading-relaxed">
                        {!! nl2br(e($author->awards)) !!}
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Author's Books -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-light-text-primary dark:text-dark-text-primary">
                    Книги автора
                </h2>
                <span class="text-light-text-secondary dark:text-dark-text-secondary">
                    {{ $books->total() }} {{ Str::plural('книга', $books->total()) }}
                </span>
            </div>

            @if($books->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($books as $book)
                        <div class="bg-light-bg dark:bg-dark-bg-secondary rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 group">
                            <a href="{{ route('books.show', $book) }}" class="block">
                                <!-- Book Cover -->
                                <div class="aspect-[3/4] overflow-hidden">
                                    <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=400&fit=crop&crop=center' }}" 
                                         alt="{{ $book->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                                
                                <!-- Book Info -->
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-light-text-primary dark:text-dark-text-primary mb-2 line-clamp-2">
                                        {{ $book->title }}
                                    </h3>
                                    
                                    @if($book->category)
                                        <p class="text-sm text-brand-500 dark:text-brand-400 mb-2">
                                            {{ $book->category->name }}
                                        </p>
                                    @endif
                                    
                                    <div class="flex items-center justify-between">
                                        @if($book->rating)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                <span class="text-sm text-light-text-secondary dark:text-dark-text-secondary">
                                                    {{ $book->rating }}
                                                </span>
                                            </div>
                                        @endif
                                        
                                        @if($book->publication_year)
                                            <span class="text-sm text-light-text-tertiary dark:text-dark-text-tertiary">
                                                {{ $book->publication_year }}
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
                    {{ $books->links() }}
                </div>
            @else
                <!-- No Books Found -->
                <div class="text-center py-12">
                    <div class="mx-auto w-24 h-24 bg-light-bg-secondary dark:bg-dark-bg-secondary rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-light-text-tertiary dark:text-dark-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-light-text-primary dark:text-dark-text-primary mb-2">
                        Книги не знайдені
                    </h3>
                    <p class="text-light-text-secondary dark:text-dark-text-secondary">
                        У цього автора поки немає книг у каталозі
                    </p>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
