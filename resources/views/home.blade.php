@extends('layouts.app')

@section('title', 'Главная - Книжный форум')

@section('main')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 dark:from-slate-900 dark:via-slate-800 dark:to-indigo-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 rounded-3xl p-12 text-white mb-12">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative max-w-4xl z-10">
                <div class="animate-fade-in-up">
                    <h1 class="text-6xl font-black mb-6 bg-gradient-to-r from-white via-purple-100 to-pink-100 bg-clip-text text-transparent leading-tight">
                        Добро пожаловать в BookForum
                    </h1>
                    <p class="text-xl text-purple-100 mb-8 leading-relaxed font-medium">
                        Открывайте новые книги, делитесь мнениями и находите единомышленников в мире литературы
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('books.index') }}" class="group bg-white/20 backdrop-blur-sm text-white px-8 py-4 rounded-2xl font-bold hover:bg-white/30 transition-all duration-300 border border-white/20 hover:border-white/40 shadow-2xl hover:shadow-3xl transform hover:scale-105 hover:-translate-y-1">
                            <span class="flex items-center gap-3">
                                📚 Посмотреть книги
                                <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </span>
                        </a>
                        <a href="{{ route('forum.index') }}" class="group bg-white text-purple-600 px-8 py-4 rounded-2xl font-bold hover:bg-gray-50 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:scale-105 hover:-translate-y-1">
                            <span class="flex items-center gap-3">
                                💬 Присоединиться к обсуждению
                                <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Animated decorative elements -->
            <div class="absolute top-0 right-0 w-80 h-80 bg-white/10 rounded-full -translate-y-40 translate-x-40 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-60 h-60 bg-white/5 rounded-full translate-y-32 -translate-x-32 animate-bounce"></div>
            <div class="absolute top-1/2 right-1/4 w-40 h-40 bg-white/5 rounded-full animate-spin" style="animation-duration: 20s;"></div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <div class="group bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-6 shadow-xl border border-white/20 dark:border-slate-700/30 hover:shadow-2xl transition-all duration-500 hover:scale-105 hover:-translate-y-2">
                        <div class="flex items-center">
                            <div class="p-4 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl shadow-xl group-hover:scale-110 transition-transform duration-500">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-4xl font-black text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">{{ $stats['books'] ?? 0 }}</p>
                                <p class="text-slate-600 dark:text-slate-400 font-semibold">Книг в каталоге</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-6 shadow-xl border border-white/20 dark:border-slate-700/30 hover:shadow-2xl transition-all duration-500 hover:scale-105 hover:-translate-y-2">
                        <div class="flex items-center">
                            <div class="p-4 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl shadow-xl group-hover:scale-110 transition-transform duration-500">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-4xl font-black text-slate-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors duration-300">{{ $stats['reviews'] ?? 0 }}</p>
                                <p class="text-slate-600 dark:text-slate-400 font-semibold">Рецензий</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-6 shadow-xl border border-white/20 dark:border-slate-700/30 hover:shadow-2xl transition-all duration-500 hover:scale-105 hover:-translate-y-2">
                        <div class="flex items-center">
                            <div class="p-4 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl shadow-xl group-hover:scale-110 transition-transform duration-500">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-4xl font-black text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">{{ $stats['topics'] ?? 0 }}</p>
                                <p class="text-slate-600 dark:text-slate-400 font-semibold">Тем в форуме</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-6 shadow-xl border border-white/20 dark:border-slate-700/30 hover:shadow-2xl transition-all duration-500 hover:scale-105 hover:-translate-y-2">
                        <div class="flex items-center">
                            <div class="p-4 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl shadow-xl group-hover:scale-110 transition-transform duration-500">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-4xl font-black text-slate-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors duration-300">{{ $stats['users'] ?? 0 }}</p>
                                <p class="text-slate-600 dark:text-slate-400 font-semibold">Участников</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured Books Section -->
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 overflow-hidden">
                    <div class="p-8 border-b border-slate-200/30 dark:border-slate-700/30">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-3xl font-black text-slate-900 dark:text-white">Рекомендуемые книги</h2>
                                <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">Лучшие произведения для вашего чтения</p>
                            </div>
                            <a href="{{ route('books.index') }}" class="group bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-3 rounded-2xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                                Посмотреть все →
                            </a>
                        </div>
                    </div>
                    <div class="p-8">
                        @if(isset($featuredBooks) && $featuredBooks->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($featuredBooks as $book)
                                    <div class="group cursor-pointer bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 hover:bg-white/80 dark:hover:bg-slate-800/80 transition-all duration-500 border border-white/30 dark:border-slate-700/30 hover:shadow-2xl hover:scale-105 hover:-translate-y-2">
                                        <div class="flex space-x-4">
                                            <div class="flex-shrink-0">
                                                <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=200&h=300&fit=crop&crop=center' }}" 
                                                     alt="{{ $book->title }}" 
                                                     class="w-20 h-28 object-cover rounded-xl shadow-lg group-hover:shadow-2xl transition-all duration-500 group-hover:scale-105">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300 line-clamp-2">
                                                    {{ $book->title }}
                                                </h3>
                                                <p class="text-slate-600 dark:text-slate-400 font-semibold">{{ $book->author }}</p>
                                                <div class="flex items-center mt-3">
                                                    <div class="flex items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <svg class="w-4 h-4 {{ $i <= $book->rating ? 'text-yellow-400' : 'text-slate-300 dark:text-slate-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                        @endfor
                                                    </div>
                                                    <span class="ml-2 text-sm font-bold text-slate-900 dark:text-white">{{ $book->rating }}</span>
                                                    <span class="ml-2 text-xs text-slate-500 dark:text-slate-500">({{ $book->reviews_count }})</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="w-24 h-24 mx-auto mb-6 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <p class="text-slate-500 dark:text-slate-400 text-lg font-medium">Пока нет рекомендуемых книг</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Topics Section -->
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30">
                    <div class="p-8 border-b border-slate-200/30 dark:border-slate-700/30">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-3xl font-black text-slate-900 dark:text-white">Последние обсуждения</h2>
                                <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium">Актуальные темы форума</p>
                            </div>
                            <a href="{{ route('forum.index') }}" class="group bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-3 rounded-2xl font-bold hover:from-green-600 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                                Посмотреть все →
                            </a>
                        </div>
                    </div>
                    <div class="divide-y divide-slate-200/30 dark:divide-slate-700/30">
                        @if(isset($recentTopics) && $recentTopics->count() > 0)
                            @foreach($recentTopics as $topic)
                                <div class="p-6 hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-all duration-300 group">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                                <span class="text-lg font-bold text-white">{{ substr($topic->user->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <a href="{{ route('forum.topics.show', $topic->slug) }}" class="text-xl font-bold text-slate-900 dark:text-white hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-300">
                                                    {{ $topic->title }}
                                                </a>
                                                @if($topic->is_pinned)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                        Закреплено
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center text-sm text-slate-500 dark:text-slate-400 space-x-4 font-medium">
                                                <span>в {{ $topic->category->name }}</span>
                                                <span>•</span>
                                                <span>{{ $topic->user->name }}</span>
                                                <span>•</span>
                                                <span>{{ $topic->created_at->diffForHumans() }}</span>
                                                <span>•</span>
                                                <span>{{ $topic->replies_count }} ответов</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="p-16 text-center">
                                <div class="w-20 h-20 mx-auto mb-6 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                    </svg>
                                </div>
                                <p class="text-slate-500 dark:text-slate-400 text-lg font-medium">Пока нет обсуждений</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Categories Widget -->
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 p-8">
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-6">Категории</h3>
                    <div class="space-y-4">
                        @if(isset($categories) && $categories->count() > 0)
                            @foreach($categories as $category)
                                <a href="{{ route('categories.show', $category->slug) }}" class="group flex items-center space-x-4 p-4 rounded-2xl hover:bg-slate-100/50 dark:hover:bg-slate-700/50 transition-all duration-300 hover:scale-105">
                                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg" style="background-color: {{ $category->color }}">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">{{ $category->name }}</h4>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">{{ $category->topics_count }} тем</p>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 p-8">
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-6">Быстрые действия</h3>
                    <div class="space-y-4">
                        <a href="{{ route('books.index') }}" class="flex items-center space-x-4 p-4 rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="font-bold">Каталог книг</span>
                        </a>
                        <a href="{{ route('forum.index') }}" class="flex items-center space-x-4 p-4 rounded-2xl bg-gradient-to-r from-green-500 to-emerald-600 text-white hover:from-green-600 hover:to-emerald-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                            </svg>
                            <span class="font-bold">Форум</span>
                        </a>
                        <a href="{{ route('search') }}" class="flex items-center space-x-4 p-4 rounded-2xl bg-gradient-to-r from-purple-500 to-pink-600 text-white hover:from-purple-600 hover:to-pink-700 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span class="font-bold">Поиск</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.5);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.8);
}

/* Enhanced shadows */
.shadow-3xl {
    box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.25);
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush
@endsection