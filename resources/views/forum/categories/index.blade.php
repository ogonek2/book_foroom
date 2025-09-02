@extends('layouts.app')

@section('title', 'Форум - Категории')

@section('main')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Форум</h1>
        <p class="text-gray-600 dark:text-gray-400">Обсуждайте книги и делитесь мнениями</p>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($categories as $category)
            <div class="group bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 overflow-hidden hover:shadow-xl transition-all duration-300 hover:scale-105">
                <div class="p-8">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300" style="background-color: {{ $category->color }}">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                <a href="{{ route('forum.categories.show', $category->slug) }}">{{ $category->name }}</a>
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $category->topics_count }} тем</p>
                        </div>
                    </div>
                    
                    @if($category->description)
                        <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed mb-4">{{ $category->description }}</p>
                    @endif
                    
                    <div class="flex items-center justify-between">
                        <a href="{{ route('forum.categories.show', $category->slug) }}" 
                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white text-sm font-medium rounded-lg transition-all duration-200 transform hover:scale-105">
                            Перейти
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $category->topics_count }} обсуждений
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($categories->count() === 0)
        <div class="text-center py-12">
            <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Категории не найдены</h3>
            <p class="text-gray-500 dark:text-gray-400">Пока нет доступных категорий форума</p>
        </div>
    @endif
</div>
@endsection