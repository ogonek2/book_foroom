@extends('profile.private.main')

@section('profile-content')
    <div class="flex-1">
        <!-- Favorites Header -->
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Избранные</h2>
                    <p class="text-gray-600 dark:text-gray-400">Рецензии и цитаты, которые вы добавили в избранное</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-400 mb-1">{{ $favoriteQuotes->total() }}</div>
                    <div class="text-sm text-gray-300">Избранных цитат</div>
                </div>
                
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-400 mb-1">{{ $favoriteReviews->total() }}</div>
                    <div class="text-sm text-gray-300">Избранных рецензий</div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl mb-8">
            <div class="flex space-x-4 mb-6 border-b border-white/10">
                <button onclick="showTab('quotes')" id="tab-quotes" 
                    class="px-4 py-2 font-medium text-gray-900 dark:text-white border-b-2 border-yellow-500 transition-colors">
                    Цитаты ({{ $favoriteQuotes->total() }})
                </button>
                <button onclick="showTab('reviews')" id="tab-reviews" 
                    class="px-4 py-2 font-medium text-gray-600 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white transition-colors">
                    Рецензии ({{ $favoriteReviews->total() }})
                </button>
            </div>

            <!-- Quotes Tab -->
            <div id="content-quotes" class="tab-content">
                @if($favoriteQuotes->count() > 0)
                    <div class="space-y-6">
                        @foreach($favoriteQuotes as $quote)
                            <div class="bg-white/5 rounded-xl p-6 hover:bg-white/10 transition-all duration-200">
                                <div class="flex items-start space-x-4">
                                    <!-- Quote Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Quote Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between mb-3">
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                                    <a href="{{ route('books.show', $quote->book->slug) }}" 
                                                       class="hover:text-orange-500 transition-colors">
                                                        {{ $quote->book->title }}
                                                    </a>
                                                </h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $quote->book->author->first_name ?? $quote->book->author ?? 'Не указан' }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Quote Text -->
                                        <div class="relative mb-4">
                                            <div class="absolute -top-2 -left-2 text-4xl text-orange-500/30 font-serif">"</div>
                                            <div class="absolute -bottom-4 -right-2 text-4xl text-orange-500/30 font-serif">"</div>
                                            
                                            <blockquote class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300 pl-6 pr-6 italic">
                                                {{ $quote->content }}
                                            </blockquote>
                                        </div>

                                        <!-- Footer -->
                                        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 pl-6">
                                            <div class="flex items-center space-x-4">
                                                <span>{{ $quote->created_at->format('d.m.Y H:i') }}</span>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <a href="{{ route('books.show', $quote->book->slug) }}" 
                                                   class="text-orange-500 hover:text-orange-600 transition-colors">
                                                    Перейти к книге →
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($favoriteQuotes->hasPages())
                        <div class="mt-6">
                            {{ $favoriteQuotes->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">
                            <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Немає избранних цитат</h3>
                        <p class="text-gray-500 mb-6">Додайте цитати до избранного, щоб вони з'явились тут</p>
                    </div>
                @endif
            </div>

            <!-- Reviews Tab -->
            <div id="content-reviews" class="tab-content hidden">
                @if($favoriteReviews->count() > 0)
                    <div class="space-y-6">
                        @foreach($favoriteReviews as $review)
                            <div class="bg-white/5 rounded-xl p-6 hover:bg-white/10 transition-all duration-200">
                                <div class="flex items-start space-x-4">
                                    <!-- Book Cover -->
                                    <div class="flex-shrink-0">
                                        <div class="w-16 h-24">
                                            <img src="{{ $review->book->cover_image }}" 
                                                 alt="{{ $review->book->title }}"
                                                 class="w-full h-full object-cover rounded-lg shadow-md">
                                        </div>
                                    </div>

                                    <!-- Review Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between mb-3">
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                                    <a href="{{ route('books.reviews.show', [$review->book->slug, $review->id]) }}" 
                                                       class="hover:text-orange-500 transition-colors">
                                                        {{ $review->book->title }}
                                                    </a>
                                                </h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $review->book->author->first_name ?? $review->book->author ?? 'Не указан' }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Rating -->
                                        @if($review->rating)
                                            <div class="flex items-center space-x-2 mb-3">
                                                <div class="flex items-center space-x-1">
                                                    @for($i = 1; $i <= 10; $i++)
                                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @endfor
                                                </div>
                                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $review->rating }}/10</span>
                                            </div>
                                        @endif

                                        <!-- Review Text -->
                                        <div class="prose prose-sm max-w-none text-gray-700 dark:text-gray-300 mb-4">
                                            {!! Str::limit($review->content, 300) !!}
                                            @if(strlen($review->content) > 300)
                                                <a href="{{ route('books.reviews.show', [$review->book->slug, $review->id]) }}" 
                                                   class="text-orange-500 hover:text-orange-600 font-medium">
                                                    Читати повністю →
                                                </a>
                                            @endif
                                        </div>

                                        <!-- Footer -->
                                        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                            <div class="flex items-center space-x-4">
                                                <span>{{ $review->created_at->format('d.m.Y H:i') }}</span>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <a href="{{ route('books.reviews.show', [$review->book->slug, $review->id]) }}" 
                                                   class="text-orange-500 hover:text-orange-600 transition-colors">
                                                    Перейти к рецензии →
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($favoriteReviews->hasPages())
                        <div class="mt-6">
                            {{ $favoriteReviews->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">
                            <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Немає избранних рецензій</h3>
                        <p class="text-gray-500 mb-6">Додайте рецензії до избранного, щоб вони з'явились тут</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function showTab(tab) {
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Remove active class from all tabs
                document.querySelectorAll('[id^="tab-"]').forEach(tabBtn => {
                    tabBtn.classList.remove('text-gray-900', 'dark:text-white', 'border-yellow-500');
                    tabBtn.classList.add('text-gray-600', 'dark:text-gray-400', 'border-transparent');
                });
                
                // Show selected tab content
                document.getElementById('content-' + tab).classList.remove('hidden');
                
                // Add active class to selected tab
                const activeTab = document.getElementById('tab-' + tab);
                activeTab.classList.remove('text-gray-600', 'dark:text-gray-400', 'border-transparent');
                activeTab.classList.add('text-gray-900', 'dark:text-white', 'border-yellow-500');
            }
        </script>
    @endpush
@endsection

