@extends('profile.private.main')

@section('profile-content')
    <div class="flex-1">
        <!-- Reviews Header -->
        <div class="mb-8">
            <div class="flex flex-col mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Мої рецензії</h2>
                    <p class="text-gray-600 dark:text-gray-400">Всі написані рецензії на книги</p>
                </div>
                <!-- Write Review Button -->
                <button onclick="writeNewReview()" 
                        class="px-4 py-2 w-fit mt-2 bg-white/20 hover:bg-white/30 text-white rounded-lg font-medium transition-all duration-300 backdrop-blur-sm">
                    <i class="fa-solid fa-plus"></i>
                    Написати рецензію
                </button>
            </div>

            <!-- Reviews Stats -->
            <div class="grid grid-cols-3 gap-2 lg:gap-4">
                @php
                    $reviews = $user->reviews()
                        ->with('book')
                        ->whereNull('parent_id')
                        ->where('is_draft', false)
                        ->get();
                    $totalReviews = $reviews->count();
                    $averageLength = $reviews->avg('length');
                    $totalLikes = $reviews->sum('likes_count');
                @endphp

                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-blue-400 mb-1">{{ $totalReviews }}</div>
                    <div class="text-sm text-gray-300">Всього рецензій</div>
                </div>
                
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-green-400 mb-1">{{ $averageLength ? number_format($averageLength) : 0 }}</div>
                    <div class="text-sm text-gray-300">Середня довжина</div>
                </div>
                
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-purple-400 mb-1">{{ $totalLikes }}</div>
                    <div class="text-sm text-gray-300">Лайків отримано</div>
                </div>
            </div>
        </div>

        <!-- Reviews Content -->
        <div>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Список рецензій</h3>
            </div>

            <!-- Reviews List -->
            <div id="reviewsList" class="space-y-6">
                @php
                    $reviews = $user->reviews()
                        ->with(['book.author', 'book.categories'])
                        ->whereNull('parent_id')
                        ->where('is_draft', false)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
                @endphp

                @if($reviews->count() > 0)
                    @foreach($reviews as $review)
                        <div class="bg-white/5 rounded-xl p-6 hover:bg-white/10 transition-all duration-200 group">
                            <div class="flex items-start space-x-4">
                                <!-- Review Content -->
                                <div class="flex-1 min-w-0">
                                    <!-- Header -->
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                                <a href="{{ route('books.show', $review->book->slug) }}" 
                                                   class="hover:text-orange-500 transition-colors">
                                                    {{ $review->book->title }}
                                                </a>
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $review->book->author->first_name ?? $review->book->author ?? 'Не указан' }}
                                            </p>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('books.reviews.edit', [$review->book->slug, $review->id]) }}" 
                                                    class="p-2 text-gray-400 hover:text-white transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <button onclick="deleteReview({{ $review->id }})" 
                                                    class="p-2 text-gray-400 hover:text-red-400 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
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
                                            <br><a href="{{ route('books.reviews.show', [$review->book->slug, $review->id]) }}" 
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
                                            <span class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>{{ $review->likes_count }}</span>
                                            </span>
                                            <span class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd" />
                                                </svg>
                                                <span>{{ $review->comments_count ?? 0 }}</span>
                                            </span>
                                        </div>
                                    </div>
                                    @if($review->updated_at != $review->created_at)
                                        <span class="text-sm text-gray-400"><i class="fa-solid fa-pen"></i> Редаговано {{ $review->updated_at->format('d.m.Y H:i') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">
                            <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Немає рецензій</h3>
                        <p class="text-gray-500 mb-6">Напишіть свою першу рецензію</p>
                        <a href="{{ route('books.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Написати рецензію
                        </a>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if($reviews->hasPages())
                <div class="mt-6">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function writeNewReview() {
                // Redirect to books page to select a book for review
                window.location.href = '{{ route("books.index") }}';
            }

            function sortReviews(sortBy) {
                // TODO: Implement client-side sorting or AJAX request
                console.log('Sort reviews by:', sortBy);
            }

            // Функция editReview больше не нужна, так как используется прямая ссылка

            async function deleteReview(reviewId) {
                const confirmed = await confirm('Ви впевнені, що хочете видалити цю рецензію? Цю дію неможливо скасувати.', 'Підтвердження', 'warning');
                if (confirmed) {
                    // TODO: Implement delete review
                    console.log('Delete review:', reviewId);
                }
            }
        </script>
    @endpush
@endsection
