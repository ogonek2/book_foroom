@extends('layouts.app')

@section('title', $category->name . ' - Форум')

@section('main')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('home') }}" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Главная</a></li>
            <li class="flex items-center">
                <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <a href="{{ route('forum.index') }}" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Форум</a>
            </li>
            <li class="flex items-center">
                <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gray-900 dark:text-white">{{ $category->name }}</span>
            </li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-8 mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg" style="background-color: {{ $category->color }}">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $category->description }}</p>
                @endif
            </div>
        </div>
        
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                <span>{{ $topics->total() }} тем</span>
                <span>{{ $category->topics_count }} обсуждений</span>
            </div>
            @auth
                <a href="{{ route('forum.topics.create', $category->slug) }}" 
                   class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-105">
                    Создать тему
                </a>
            @endauth
        </div>
    </div>

    <!-- Topics List -->
    <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 overflow-hidden">
        @if($topics->count() > 0)
            <div class="divide-y divide-gray-200/20 dark:divide-gray-700/20">
                @foreach($topics as $topic)
                    <div class="p-6 hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-all duration-300 group">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                                    <span class="text-sm font-medium text-white">{{ substr($topic->user->name, 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors mb-1">
                                            <a href="{{ route('forum.topics.show', $topic->slug) }}">{{ $topic->title }}</a>
                                        </h3>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span>Автор: {{ $topic->user->name }}</span>
                                            <span>•</span>
                                            <span>{{ $topic->created_at->diffForHumans() }}</span>
                                            @if($topic->is_pinned)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    Закреплено
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            <span>{{ $topic->replies_count }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>{{ $topic->views_count }}</span>
                                        </div>
                                    </div>
                                </div>
                                @if($topic->last_activity_at)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Последняя активность: {{ $topic->last_activity_at->diffForHumans() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="p-6 border-t border-gray-200/20 dark:border-gray-700/20">
                {{ $topics->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Пока нет тем</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Станьте первым, кто создаст тему в этой категории</p>
                @auth
                    <a href="{{ route('forum.topics.create', $category->slug) }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-105">
                        Создать первую тему
                    </a>
                @endauth
            </div>
        @endif
    </div>
</div>
@endsection