@extends('layouts.app')

@section('title', 'Категории')

@section('main')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Категории</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Выберите категорию для просмотра книг</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($categories as $category)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: {{ $category->color }}">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <a href="{{ route('categories.show', $category->slug) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    {{ $category->name }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $category->books_count }} книг • {{ $category->topics_count }} тем</p>
                        </div>
                    </div>
                    
                    @if($category->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $category->description }}</p>
                    @endif
                    
                    <div class="flex items-center justify-between">
                        <a href="{{ route('categories.show', $category->slug) }}" 
                           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                            Посмотреть книги →
                        </a>
                        <a href="{{ route('forum.categories.show', $category->slug) }}" 
                           class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 text-sm font-medium">
                            Обсуждения →
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($categories->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Нет категорий</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Пока нет доступных категорий.</p>
        </div>
    @endif
</div>
@endsection
