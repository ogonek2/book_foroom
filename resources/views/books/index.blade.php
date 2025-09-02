@extends('layouts.app')

@section('title', 'Каталог книг')

@section('main')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Каталог книг</h1>
        <p class="text-gray-600 dark:text-gray-400">Найдите интересные книги для чтения</p>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Filters -->
            <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-6 sticky top-24">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Фильтры</h3>
                <form method="GET" action="{{ route('books.index') }}" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Поиск</label>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Название, автор..."
                               class="w-full px-4 py-3 border-0 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-gray-600 transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Категория</label>
                        <select name="category" 
                                class="w-full px-4 py-3 border-0 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-gray-600 transition-all duration-200">
                            <option value="">Все категории</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Сортировка</label>
                        <select name="sort" 
                                class="w-full px-4 py-3 border-0 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-gray-600 transition-all duration-200">
                            <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>По рейтингу</option>
                            <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>По названию</option>
                            <option value="author" {{ request('sort') === 'author' ? 'selected' : '' }}>По автору</option>
                            <option value="year" {{ request('sort') === 'year' ? 'selected' : '' }}>По году</option>
                            <option value="reviews" {{ request('sort') === 'reviews' ? 'selected' : '' }}>По отзывам</option>
                        </select>
                    </div>
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-4 py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-105">
                        Применить фильтры
                    </button>
                </form>
            </div>
        </div>

        <!-- Books Grid -->
        <div class="lg:col-span-3">
            @if($books->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($books as $book)
                        <div class="group bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 overflow-hidden hover:shadow-xl transition-all duration-300 hover:scale-105">
                            <div class="p-6">
                                <div class="flex space-x-4">
                                    <div class="flex-shrink-0">
                                        <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=200&h=300&fit=crop&crop=center' }}" 
                                             alt="{{ $book->title }}" 
                                             class="w-20 h-28 object-cover rounded-lg shadow-md group-hover:shadow-lg transition-all duration-300">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors mb-1">
                                            <a href="{{ route('books.show', $book->slug) }}">{{ $book->title }}</a>
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-2">{{ $book->author }}</p>
                                        <div class="flex items-center mb-2">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $book->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400 font-medium">{{ $book->rating }}</span>
                                            <span class="ml-1 text-sm text-gray-500 dark:text-gray-500">({{ $book->reviews_count }})</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $book->category->color }}20; color: {{ $book->category->color }}">
                                                {{ $book->category->name }}
                                            </span>
                                            @if($book->publication_year)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $book->publication_year }}</span>
                                            @endif
                                        </div>
                                    </div>
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
                    <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Книги не найдены</h3>
                    <p class="text-gray-500 dark:text-gray-400">Попробуйте изменить параметры поиска</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection