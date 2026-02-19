@extends('users.public.main')

@section('title', $user->name . ' - Рецензії')

@section('profile-content')
    <div class="flex-1">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Рецензії користувача</h2>

            @if ($reviews->count() > 0)
                <div class="space-y-6">
                    @foreach ($reviews as $review)
                        <div class="bg-white dark:bg-white/5 rounded-xl p-6 hover:bg-gray-50 dark:hover:bg-white/10 transition-all shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <a href="{{ route('books.show', $review->book->slug) }}"
                                        class="text-xl font-semibold text-black dark:text-white hover:text-purple-200 transition-colors break-words" 
                                        style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                                        {{ $review->book->title }}
                                    </a>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">{{ $review->book->author }}</p>
                                </div>

                                @if ($review->rating)
                                    <div class="flex items-center ml-4">
                                        <div class="flex space-x-1">
                                            <i class="fa-solid fa-star text-yellow-500"></i>
                                        </div>
                                        <span class="ml-2 text-gray-900 dark:text-white font-medium">{{ $review->rating }}/10</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Review Text -->
                                    @if($review->contains_spoiler)
                                        <!-- Spoiler Content -->
                                        <div class="relative mb-4">
                                            <div class="relative overflow-hidden rounded-lg py-8 px-4">
                                                <div class="prose prose-sm max-w-none text-gray-700 dark:text-gray-300 break-words blur-sm filter"
                                                     style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                                                    {!! Str::limit($review->content, 300) !!}
                                                </div>
                                                <!-- Semi-transparent overlay -->
                                                <div class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 backdrop-blur-sm flex items-center justify-center rounded-lg">
                                                    <div class="text-center px-4 py-6">
                                                        <p class="text-gray-900 dark:text-white text-base font-bold mb-4 drop-shadow-sm">Рецензія містить спойлер</p>
                                                        <a href="{{ route('books.reviews.show', [$review->book->slug, $review->id]) }}"
                                                           class="inline-block bg-indigo-600 dark:bg-indigo-500 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors shadow-md">
                                                            Читати
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="prose prose-sm max-w-none text-gray-700 dark:text-gray-300 mb-4 break-words" 
                                             style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                                            {!! Str::limit($review->content, 300) !!}
                                            <br><a href="{{ route('books.reviews.show', [$review->book->slug, $review->id]) }}" 
                                                   class="text-orange-500 hover:text-orange-600 font-medium">
                                                До рецензії →
                                            </a>
                                        </div>
                                    @endif

                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                            </path>
                                        </svg>
                                        <span>{{ $review->likes_count ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                            </path>
                                        </svg>
                                        <span>{{ $review->replies_count ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="text-gray-500 dark:text-gray-500">
                                    {{ $review->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-600 dark:text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Ще немає рецензій</h3>
                    <p class="text-gray-600 dark:text-gray-400">Користувач ще не написав жодної рецензії</p>
                </div>
            @endif
        </div>
    </div>
@endsection
