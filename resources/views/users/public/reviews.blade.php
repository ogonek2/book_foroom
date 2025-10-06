@extends('users.public.main')

@section('title', $user->name . ' - Рецензії')

@section('profile-content')
    <div class="flex-1">
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Рецензії користувача</h2>

            @if ($reviews->count() > 0)
                <div class="space-y-6">
                    @foreach ($reviews as $review)
                        <div class="bg-white/5 rounded-xl p-6 hover:bg-white/10 transition-all">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <a href="{{ route('books.show', $review->book->slug) }}"
                                        class="text-xl font-semibold text-black dark:text-white hover:text-purple-200 transition-colors">
                                        {{ $review->book->title }}
                                    </a>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">{{ $review->book->author }}</p>
                                </div>

                                @if ($review->rating)
                                    <div class="flex items-center ml-4">
                                        <div class="flex space-x-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-600' }}"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-gray-900 dark:text-white font-medium">{{ $review->rating }}/5</span>
                                    </div>
                                @endif
                            </div>

                            <div class="text-gray-700 dark:text-gray-300 leading-relaxed mb-4">
                                {{ $review->content }}
                            </div>

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
