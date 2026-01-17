@extends('layouts.app')

@section('title', '#' . $hashtag->name . ' - Рецензії - Книжковий форум')

@section('main')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('home') }}" class="text-sm text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                <i class="fas fa-home"></i> Головна
            </a>
            <span class="text-slate-400">/</span>
            <span class="text-sm font-semibold text-slate-900 dark:text-white">#{{ $hashtag->name }}</span>
        </div>
        
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-8 text-white">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-hashtag text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold mb-2">#{{ $hashtag->name }}</h1>
                    <p class="text-indigo-100">
                        {{ $hashtag->usage_count }} {{ $hashtag->usage_count == 1 ? 'рецензія' : ($hashtag->usage_count < 5 ? 'рецензії' : 'рецензій') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    @if($reviews->count() > 0)
        <div class="space-y-6">
            @foreach($reviews as $review)
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 p-6 hover:shadow-xl transition-all duration-300">
                    <!-- Review Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center space-x-4 mb-3">
                                @include('partials.user-mini-header', [
                                    'user' => $review->isGuest() ? null : $review->user,
                                    'timestamp' => $review->created_at->diffForHumans(),
                                    'showGuest' => $review->isGuest()
                                ])
                            </div>
                            
                            <!-- Book Info -->
                            @if($review->book)
                            <div class="mb-3">
                                <a href="{{ route('books.show', $review->book->slug) }}" 
                                   class="text-lg font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                                    {{ $review->book->title }}
                                </a>
                                @if($review->book->author)
                                <p class="text-sm text-slate-600 dark:text-slate-400">
                                    {{ $review->book->author }}
                                </p>
                                @endif
                            </div>
                            @endif

                            <!-- Rating -->
                            @if($review->rating)
                            <div class="flex items-center gap-2 mb-3">
                                <div class="flex items-center bg-yellow-100 dark:bg-yellow-900/30 px-3 py-1 rounded-full">
                                    <i class="fas fa-star text-yellow-500 dark:text-yellow-400 text-sm mr-1"></i>
                                    <span class="text-yellow-700 dark:text-yellow-300 font-medium text-sm">{{ $review->rating }}/10</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Review Content Preview -->
                    <div class="mb-4">
                        <div class="text-slate-700 dark:text-slate-300 leading-relaxed line-clamp-3">
                            {!! \App\Helpers\HtmlSanitizer::sanitize(substr(strip_tags($review->content), 0, 300)) !!}...
                        </div>
                    </div>

                    <!-- Hashtags -->
                    @if($review->hashtags->count() > 0)
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($review->hashtags as $tag)
                            <a href="{{ route('hashtags.show', $tag->slug) }}" 
                               class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full text-sm font-medium hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition-colors">
                                <i class="fas fa-hashtag text-xs"></i>
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-700">
                        <a href="{{ route('books.reviews.show', [$review->book, $review]) }}" 
                           class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                            Читати повністю <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $reviews->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 p-12 text-center">
            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-hashtag text-2xl text-slate-400"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                Рецензій з цим хештегом поки немає
            </h3>
            <p class="text-slate-600 dark:text-slate-400 mb-6">
                Станьте першим, хто використає хештег #{{ $hashtag->name }} у своїй рецензії!
            </p>
            <a href="{{ route('books.index') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                <i class="fas fa-book"></i>
                Переглянути книги
            </a>
        </div>
    @endif
</div>
@endsection

