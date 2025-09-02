@extends('layouts.app')

@section('title', 'Поиск')

@section('main')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Поиск</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Найдите книги и обсуждения</p>
    </div>

    <!-- Search Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('search') }}" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" 
                           name="q" 
                           value="{{ $query }}"
                           placeholder="Поиск по книгам, авторам, темам..."
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-lg">
                </div>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    Найти
                </button>
            </form>
        </div>
    </div>

    @if($query)
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                Результаты поиска для "{{ $query }}"
            </h2>
            <p class="text-gray-600 dark:text-gray-400">{{ $results->count() }} результатов</p>
        </div>

        @if($results->count() > 0)
            <div class="space-y-6">
                @foreach($results as $result)
                    @if($result instanceof \App\Models\Book)
                        <!-- Book Result -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <img src="{{ $result->cover_image ?: '/images/book-placeholder.jpg' }}" 
                                         alt="{{ $result->title }}" 
                                         class="w-16 h-24 object-cover rounded-lg">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            Книга
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $result->category->name }}</span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                        <a href="{{ route('books.show', $result->slug) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            {{ $result->title }}
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-2">{{ $result->author }}</p>
                                    @if($result->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ Str::limit($result->description, 150) }}</p>
                                    @endif
                                    <div class="flex items-center mt-2">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $result->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="ml-1 text-sm text-gray-600 dark:text-gray-400">{{ $result->rating }}</span>
                                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-500">({{ $result->reviews_count }} отзывов)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($result instanceof \App\Models\Topic)
                        <!-- Topic Result -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ substr($result->user->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Обсуждение
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $result->category->name }}</span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                        <a href="{{ route('forum.topics.show', $result->slug) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            {{ $result->title }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">{{ Str::limit($result->content, 150) }}</p>
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                                        <span>{{ $result->user->name }}</span>
                                        <span>•</span>
                                        <span>{{ $result->created_at->diffForHumans() }}</span>
                                        <span>•</span>
                                        <span>{{ $result->replies_count }} ответов</span>
                                        <span>•</span>
                                        <span>{{ $result->views_count }} просмотров</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Ничего не найдено</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Попробуйте изменить поисковый запрос.</p>
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Введите поисковый запрос</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Найдите интересные книги и обсуждения.</p>
        </div>
    @endif
</div>
@endsection
