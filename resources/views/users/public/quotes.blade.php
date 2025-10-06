@extends('users.public.main')

@section('title', $user->name . ' - Цитати')

@section('profile-content')
    <div class="flex-1">
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Цитати користувача</h2>

            @if ($quotes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($quotes as $quote)
                        <div class="bg-white/5 rounded-xl p-6 hover:bg-white/10 transition-all group">
                            <!-- Quote Icon -->
                            <div
                                class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full mb-4 mx-auto">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V8a1 1 0 112 0v2.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>

                            <!-- Quote Content -->
                            <blockquote class="text-gray-700 dark:text-gray-300 italic text-center mb-4 leading-relaxed">
                                "{{ $quote->content }}"
                            </blockquote>

                            <!-- Book Info -->
                            @if ($quote->book)
                                <div class="text-center">
                                    <a href="{{ route('books.show', $quote->book->slug) }}"
                                        class="text-purple-600 dark:text-purple-300 hover:text-purple-700 dark:hover:text-purple-200 font-medium transition-colors">
                                        {{ $quote->book->title }}
                                    </a>
                                    @if ($quote->book->author)
                                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ $quote->book->author }}</p>
                                    @endif
                                </div>
                            @endif

                            <!-- Quote Footer -->
                            <div
                                class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mt-4 pt-4 border-t border-white/10">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                        </path>
                                    </svg>
                                    <span>{{ $quote->likes_count ?? 0 }}</span>
                                </div>
                                <div class="text-gray-500 dark:text-gray-500">
                                    {{ $quote->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $quotes->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-600 dark:text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Ще немає цитат</h3>
                    <p class="text-gray-600 dark:text-gray-400">Користувач ще не додав жодної цитати</p>
                </div>
            @endif
        </div>
    </div>
@endsection
