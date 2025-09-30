@extends('profile.layout')

@section('profile-content')
<!-- Recent Books Section -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-white mb-6">Останні прочитані книги</h2>
    
    @php
        $recentReadBooks = $user->readingStatuses()
            ->where('status', 'read')
            ->with('book')
            ->orderBy('finished_at', 'desc')
            ->limit(4)
            ->get();
    @endphp
    
    @if($recentReadBooks->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
            @foreach($recentReadBooks as $readingStatus)
                <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow flex align-middle p-4 space-x-4">
                    <div class="bg-gray-700">
                        @if($readingStatus->book->cover_image)
                            <img src="{{ $readingStatus->book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=450&fit=crop&crop=center' }}" 
                                 alt="{{ $readingStatus->book->title }}" 
                                 class="w-full h-full object-cover rounded-lg" style="aspect-ratio: 3/4; max-width: 100px;">
                        @else
                            <div class="flex items-center justify-center h-32 bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg" style="width: 100px;">
                                <div class="text-center p-2">
                                    <div class="text-white font-bold text-xs">{{ Str::limit($readingStatus->book->title, 15) }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="text-white font-semibold mb-2">{{ Str::limit($readingStatus->book->title, 40) }}</h3>
                        <p class="text-gray-400 text-sm mb-2">{{ $readingStatus->book->author }}</p>
                        
                        @if($readingStatus->rating)
                            <div class="flex items-center space-x-1 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $readingStatus->rating/2 ? 'text-yellow-400' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                                <span class="text-gray-300 text-sm ml-1">{{ $readingStatus->rating }}/10</span>
                            </div>
                        @endif
                        
                        <a href="{{ route('books.show', $readingStatus->book->slug) }}" 
                           class="inline-flex items-center text-sm bg-gradient-to-r from-orange-500 to-pink-500 text-white py-2 px-4 rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all duration-200">
                            Переглянути
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-300 mb-2">Ще немає прочитаних книг</h3>
            <p class="text-gray-500 mb-6">Додайте книги до своєї бібліотеки та відмічайте їх як прочитані</p>
            <a href="{{ route('books.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Переглянути книги
            </a>
        </div>
    @endif
</div>

<!-- User Reviews Section -->
<div class="mt-8">
    <h2 class="text-2xl font-bold text-white mb-6">Мої рецензії</h2>
    
    @php
        $userReviews = $user->reviews()
            ->with(['book'])
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
    @endphp
    
    @if($userReviews->count() > 0)
        <div class="space-y-4">
            @foreach($userReviews as $review)
                <div class="bg-gray-800 rounded-lg p-6 hover:shadow-xl transition-shadow">
                    <!-- Review Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <a href="{{ route('books.show', $review->book->slug) }}" class="text-xl font-semibold text-white hover:text-orange-400 transition-colors">
                                {{ $review->book->title }}
                            </a>
                            <p class="text-gray-400 text-sm mt-1">{{ $review->book->author }}</p>
                        </div>
                        
                        @if($review->rating)
                            <div class="flex items-center ml-4">
                                <div class="flex space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="ml-2 text-white font-medium">{{ $review->rating }}/5</span>
                            </div>
                        @endif
                    </div>

                    <!-- Review Content -->
                    <div class="text-gray-300 leading-relaxed mb-4">
                        {{ Str::limit($review->content, 250) }}
                        @if(strlen($review->content) > 250)
                            <a href="{{ route('books.reviews.show', [$review->book, $review]) }}" class="text-orange-400 hover:text-orange-300 ml-1">
                                Читати повністю
                            </a>
                        @endif
                    </div>

                    <!-- Review Footer -->
                    <div class="flex items-center justify-between text-sm text-gray-400">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span>{{ $review->likes_count ?? 0 }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>{{ $review->replies_count ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="text-gray-500">
                            {{ $review->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($user->reviews()->whereNull('parent_id')->count() > 3)
            <div class="mt-6 text-center">
                <a href="{{ route('profile.reviews', $user->username) }}" class="inline-flex items-center px-6 py-3 bg-gray-700 text-white rounded-lg font-medium hover:bg-gray-600 transition-all duration-200">
                    Всі рецензії ({{ $user->reviews()->whereNull('parent_id')->count() }})
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        @endif
    @else
        <div class="text-center py-12 bg-gray-800 rounded-lg">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-300 mb-2">Ще немає рецензій</h3>
            <p class="text-gray-500 mb-6">Поділіться своїми думками про прочитані книги</p>
            <a href="{{ route('books.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Написати рецензію
            </a>
        </div>
    @endif
</div>
@endsection
