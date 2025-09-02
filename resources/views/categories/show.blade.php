@extends('layouts.app')

@section('title', $category->name . ' - Книги')

@section('main')
<div class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('categories.index') }}" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400">
                    Категории
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-gray-500 md:ml-2 dark:text-gray-400">{{ $category->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-lg flex items-center justify-center" style="background-color: {{ $category->color }}">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $category->name }}</h1>
                    @if($category->description)
                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $category->description }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $books->total() }} книг</p>
                    <a href="{{ route('forum.categories.show', $category->slug) }}" 
                       class="text-sm text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300">
                        Обсуждения →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Books Grid -->
    @if($books->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($books as $book)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                    <div class="p-4">
                        <div class="aspect-w-3 aspect-h-4 mb-4">
                            <img src="{{ $book->cover_image ?: '/images/book-placeholder.jpg' }}" 
                                 alt="{{ $book->title }}" 
                                 class="w-full h-48 object-cover rounded-lg">
                        </div>
                        
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                            <a href="{{ route('books.show', $book->slug) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                {{ $book->title }}
                            </a>
                        </h3>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $book->author }}</p>
                        
                        <div class="flex items-center mb-2">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $book->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="ml-1 text-sm text-gray-600 dark:text-gray-400">{{ $book->rating }}</span>
                            <span class="ml-2 text-sm text-gray-500 dark:text-gray-500">({{ $book->reviews_count }})</span>
                        </div>
                        
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            @if($book->publication_year)
                                <span>{{ $book->publication_year }}</span>
                            @endif
                            @if($book->pages)
                                <span>{{ $book->pages }} стр.</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $books->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Нет книг</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">В этой категории пока нет книг.</p>
        </div>
    @endif
</div>
@endsection
