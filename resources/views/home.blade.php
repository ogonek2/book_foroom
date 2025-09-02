@extends('layouts.app')

@section('title', 'Головна - Книжковий форум')

@section('main')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-[#F6762E] to-[#F78F54] rounded-3xl p-12 text-white mb-12">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative max-w-4xl z-10">
                <div class="animate-fade-in-up">
                    <h1 class="text-6xl font-black mb-6 bg-gradient-to-r from-white via-orange-100 to-yellow-100 bg-clip-text text-transparent leading-tight">
                        Ласкаво просимо до BookForum
                    </h1>
                    <p class="text-xl text-orange-100 mb-8 leading-relaxed font-medium">
                        Відкривайте нові книги, діліться думками та знаходьте однодумців у світі літератури
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('books.index') }}" class="group bg-white/20 backdrop-blur-sm text-white px-8 py-4 rounded-2xl font-bold hover:bg-white/30 transition-all duration-300 border border-white/20 hover:border-white/40 shadow-2xl hover:shadow-3xl transform hover:scale-105 hover:-translate-y-1">
                            <span class="flex items-center gap-3">
                                📚 Переглянути книги
                                <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </span>
                        </a>
                        <a href="{{ route('forum.index') }}" class="group bg-white text-[#F6762E] px-8 py-4 rounded-2xl font-bold hover:bg-gray-50 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:scale-105 hover:-translate-y-1">
                            <span class="flex items-center gap-3">
                                💬 Приєднатися до обговорення
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

        <!-- Main Content -->
        <div class="space-y-12">
            <!-- Featured Books Slider -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Рекомендовані книги</h2>
                        <a href="{{ route('books.index') }}" class="flex items-center gap-2 text-[#FF843E] hover:text-[#F6762E] transition-colors font-semibold">
                            Перейти до всіх
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if(isset($featuredBooks) && $featuredBooks->count() > 0)
                        <div class="flex gap-6 overflow-x-auto pb-4 scrollbar-hide">
                            @foreach($featuredBooks as $book)
                                <div class="flex-shrink-0 w-48 group cursor-pointer">
                                    <div class="relative">
                                        <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=200&h=300&fit=crop&crop=center' }}" 
                                             alt="{{ $book->title }}" 
                                             class="w-full h-64 object-cover rounded-lg shadow-md group-hover:shadow-lg transition-all duration-300">
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 rounded-lg"></div>
                                    </div>
                                    <div class="mt-3">
                                        <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-[#FF843E] transition-colors line-clamp-2">
                                            {{ $book->title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $book->author }}</p>
                                        <div class="flex items-center mt-2">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $book->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="ml-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $book->rating }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Поки немає рекомендованих книг</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Коментарі</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @if(isset($recentComments) && $recentComments->count() > 0)
                            @foreach($recentComments as $comment)
                                <div class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <div class="w-10 h-10 bg-gradient-to-br from-[#F6762E] to-[#F78F54] rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-bold text-white">{{ substr($comment->user->name, 0, 1) }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $comment->user->name }}</span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">{{ Str::limit($comment->content, 150) }}</p>
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-[#FF843E]/10 text-[#FF843E]">
                                                {{ $comment->book->title }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">Поки немає коментарів</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Articles Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Статті</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        @if(isset($recentArticles) && $recentArticles->count() > 0)
                            @foreach($recentArticles as $article)
                                <div class="flex items-start space-x-4">
                                    <div class="w-10 h-10 bg-gradient-to-br from-[#F6762E] to-[#F78F54] rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-bold text-white">{{ substr($article->author->name, 0, 1) }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $article->author->name }}</span>
                                            <button class="text-[#FF843E] hover:text-[#F6762E] transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                                </svg>
                                            </button>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $article->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h3 class="font-bold text-gray-900 dark:text-white mb-2 line-clamp-2">{{ $article->title }}</h3>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700">
                                                {{ $article->category }}
                                            </span>
                                            <span>{{ $article->views }} переглядів</span>
                                            <span>{{ $article->comments_count }} коментарів</span>
                                            <span>{{ $article->likes_count }} лайків</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">Поки немає статей</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Collections Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Колекції</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        @if(isset($recentCollections) && $recentCollections->count() > 0)
                            @foreach($recentCollections as $collection)
                                <div class="flex items-start space-x-4">
                                    <div class="w-10 h-10 bg-gradient-to-br from-[#F6762E] to-[#F78F54] rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-bold text-white">{{ substr($collection->author->name, 0, 1) }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $collection->author->name }}</span>
                                            <button class="text-[#FF843E] hover:text-[#F6762E] transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                                </svg>
                                            </button>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $collection->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ $collection->title }}</h3>
                                        <div class="flex items-center space-x-2 mb-3">
                                            @foreach($collection->books->take(4) as $book)
                                                <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=100&h=150&fit=crop&crop=center' }}" 
                                                     alt="{{ $book->title }}" 
                                                     class="w-12 h-16 object-cover rounded">
                                            @endforeach
                                            @if($collection->books->count() > 4)
                                                <div class="w-12 h-16 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-400">+{{ $collection->books->count() - 4 }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span>{{ $collection->comments_count }} коментарів</span>
                                            <span>{{ $collection->likes_count }} лайків</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">Поки немає колекцій</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Calendar Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Календар</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @if(isset($upcomingReleases) && $upcomingReleases->count() > 0)
                            @foreach($upcomingReleases as $release)
                                <div class="flex items-center space-x-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <img src="{{ $release->book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=100&h=150&fit=crop&crop=center' }}" 
                                         alt="{{ $release->book->title }}" 
                                         class="w-12 h-16 object-cover rounded">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $release->book->title }}</h3>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            <span>{{ $release->duration }}</span>
                                            <span>{{ $release->episode_count }} епізодів</span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $release->release_date->format('d.m') }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">Поки немає майбутніх релізів</p>
                            </div>
                        @endif
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

/* Hide scrollbar for horizontal scroll */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom gradient for brand colors */
.bg-brand-gradient {
    background: linear-gradient(135deg, #F6762E 0%, #F78F54 100%);
}

.text-brand {
    color: #FF843E;
}

.hover\:text-brand:hover {
    color: #F6762E;
}
</style>
@endpush
@endsection