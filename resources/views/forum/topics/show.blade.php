@extends('layouts.app')

@section('title', $topic->title . ' - Форум')

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
                <a href="{{ route('forum.categories.show', $topic->category->slug) }}" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">{{ $topic->category->name }}</a>
            </li>
            <li class="flex items-center">
                <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gray-900 dark:text-white truncate">{{ $topic->title }}</span>
            </li>
        </ol>
    </nav>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Topic Header -->
            <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-4">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $topic->title }}</h1>
                            @if($topic->is_pinned)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    Закреплено
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium text-white">{{ substr($topic->user->name, 0, 1) }}</span>
                                </div>
                                <span>Автор: {{ $topic->user->name }}</span>
                            </div>
                            <span>•</span>
                            <span>{{ $topic->created_at->format('d.m.Y в H:i') }}</span>
                            <span>•</span>
                            <span>{{ $topic->views_count }} просмотров</span>
                            <span>•</span>
                            <span>{{ $topic->replies_count }} ответов</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" style="background-color: {{ $topic->category->color }}20; color: {{ $topic->category->color }}">
                            {{ $topic->category->name }}
                        </span>
                    </div>
                </div>
                
                <div class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300">
                    {!! nl2br(e($topic->content)) !!}
                </div>
            </div>

            <!-- Posts -->
            <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30">
                <div class="p-8 border-b border-gray-200/20 dark:border-gray-700/20">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Ответы ({{ $posts->total() }})</h2>
                </div>
                
                @if($posts->count() > 0)
                    <div class="divide-y divide-gray-200/20 dark:divide-gray-700/20">
                        @foreach($posts as $post)
                            <div class="p-8">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-md">
                                            <span class="text-sm font-medium text-white">{{ substr($post->user->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-3">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $post->user->name }}</h4>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                                            @if($post->is_solution)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Решение
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="prose prose-sm max-w-none text-gray-700 dark:text-gray-300 mb-4">
                                            {!! nl2br(e($post->content)) !!}
                                        </div>
                                        
                                        <div class="flex items-center space-x-4">
                                            <button class="flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V18m-7-8a2 2 0 01-2-2V5a2 2 0 012-2h2.343M11 7h6a2 2 0 012 2v6a2 2 0 01-2 2h-6m-5 0a2 2 0 01-2-2V9a2 2 0 012-2h2.343M5 7h6a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2z"/>
                                                </svg>
                                                <span>{{ $post->likes_count }}</span>
                                            </button>
                                            
                                            @if(auth()->check() && auth()->id() === $post->user_id)
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('forum.posts.edit', $post) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                        Редактировать
                                                    </a>
                                                    <form action="{{ route('forum.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="(function(e) { e.preventDefault(); (async function() { const confirmed = await confirm('Ви впевнені, що хочете видалити цей пост?', 'Підтвердження', 'warning'); if (confirmed) { e.target.submit(); } })(); })(event); return false;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                                            Удалить
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="p-6 border-t border-gray-200/20 dark:border-gray-700/20">
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Пока нет ответов</h3>
                        <p class="text-gray-500 dark:text-gray-400">Станьте первым, кто ответит на эту тему</p>
                    </div>
                @endif
            </div>

            <!-- Reply Form -->
            @auth
                <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Ответить</h3>
                    <form action="{{ route('forum.posts.store', $topic) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <textarea name="content" 
                                      rows="6"
                                      class="w-full px-4 py-3 border-0 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white dark:focus:bg-gray-600 transition-all duration-200"
                                      placeholder="Напишите ваш ответ..."
                                      required></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-8 py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-105">
                                Отправить ответ
                            </button>
                        </div>
                    </form>
                </div>
            @endauth
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Topic Info -->
            <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Информация о теме</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Автор:</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $topic->user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Создана:</span>
                        <span class="text-gray-900 dark:text-white">{{ $topic->created_at->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Просмотров:</span>
                        <span class="text-gray-900 dark:text-white">{{ $topic->views_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Ответов:</span>
                        <span class="text-gray-900 dark:text-white">{{ $topic->replies_count }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @auth
                <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/30 dark:border-gray-700/30 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Действия</h3>
                    <div class="space-y-3">
                        @if(auth()->id() === $topic->user_id)
                            <a href="{{ route('forum.topics.edit', $topic) }}" 
                               class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                Редактировать тему
                            </a>
                        @endif
                        <a href="{{ route('forum.categories.show', $topic->category->slug) }}" 
                           class="block w-full text-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors">
                            Вернуться к категории
                        </a>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection