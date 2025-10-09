@extends('profile.private.main')

@section('profile-content')
    <div class="flex-1">
        <!-- Quotes Header -->
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Мої цитати</h2>
                    <p class="text-gray-600 dark:text-gray-400">Збережені цитати з прочитаних книг</p>
                </div>
                <!-- Add Quote Button -->
                <button onclick="addNewQuote()" 
                        class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg font-medium transition-all duration-300 backdrop-blur-sm">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Додати цитату
                </button>
            </div>

            <!-- Quotes Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @php
                    $quotes = $user->quotes()->with('book')->get();
                    $totalQuotes = $quotes->count();
                    $totalLikes = $quotes->sum('likes_count');
                    $averageLength = $quotes->avg('length');
                @endphp

                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-blue-400 mb-1">{{ $totalQuotes }}</div>
                    <div class="text-sm text-gray-300">Всього цитат</div>
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

        <!-- Quotes Content -->
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Список цитат</h3>
                <!-- Filter and Sort -->
                <div class="flex items-center space-x-4">
                    <select id="bookFilter" onchange="filterByBook(this.value)" 
                            class="px-3 py-2 bg-white/20 text-white rounded-lg border border-white/30 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Всі книги</option>
                        @php
                            $books = $user->quotes()->with('book')->get()->pluck('book')->unique('id');
                        @endphp
                        @foreach($books as $book)
                            <option value="{{ $book->id }}">{{ $book->title }}</option>
                        @endforeach
                    </select>
                    
                    <select id="sortBy" onchange="sortQuotes(this.value)" 
                            class="px-3 py-2 bg-white/20 text-white rounded-lg border border-white/30 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="created_at">Дата додавання</option>
                        <option value="book_title">Назва книги</option>
                        <option value="likes_count">Кількість лайків</option>
                    </select>
                </div>
            </div>

            <!-- Quotes List -->
            <div id="quotesList" class="space-y-6">
                @php
                    $quotes = $user->quotes()
                        ->with('book.author')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
                @endphp

                @if($quotes->count() > 0)
                    @foreach($quotes as $quote)
                        <div class="bg-white/5 rounded-xl p-6 hover:bg-white/10 transition-all duration-200 group" 
                             data-book-id="{{ $quote->book_id }}">
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
                                    <!-- Header -->
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

                                        <!-- Actions -->
                                        <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button onclick="editQuote({{ $quote->id }})" 
                                                    class="p-2 text-gray-400 hover:text-white transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button onclick="deleteQuote({{ $quote->id }})" 
                                                    class="p-2 text-gray-400 hover:text-red-400 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Quote Text -->
                                    <div class="relative">
                                        <!-- Quote Marks -->
                                        <div class="absolute -top-2 -left-2 text-4xl text-orange-500/30 font-serif">"</div>
                                        <div class="absolute -bottom-4 -right-2 text-4xl text-orange-500/30 font-serif">"</div>
                                        
                                        <blockquote class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300 mb-4 pl-6 pr-6 italic">
                                            {{ $quote->content }}
                                        </blockquote>
                                    </div>

                                    <!-- Quote Context -->
                                    @if($quote->context)
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4 pl-6">
                                            <strong>Контекст:</strong> {{ $quote->context }}
                                        </div>
                                    @endif

                                    <!-- Footer -->
                                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 pl-6">
                                        <div class="flex items-center space-x-4">
                                            <span>{{ $quote->created_at->format('d.m.Y H:i') }}</span>
                                            @if($quote->updated_at != $quote->created_at)
                                                <span>(редаговано {{ $quote->updated_at->format('d.m.Y H:i') }})</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <span class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>{{ $quote->likes_count }}</span>
                                            </span>
                                            <button onclick="shareQuote({{ $quote->id }})" 
                                                    class="flex items-center space-x-1 text-gray-500 hover:text-white transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                                                </svg>
                                                <span>Поділитись</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">
                            <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Немає цитат</h3>
                        <p class="text-gray-500 mb-6">Додайте цитати з прочитаних книг</p>
                        <a href="{{ route('books.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Додати цитату
                        </a>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if($quotes->hasPages())
                <div class="mt-6">
                    {{ $quotes->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function addNewQuote() {
                // TODO: Implement add quote modal
                console.log('Add new quote');
            }

            function filterByBook(bookId) {
                const quotes = document.querySelectorAll('#quotesList > div[data-book-id]');
                quotes.forEach(quote => {
                    if (!bookId || quote.dataset.bookId === bookId) {
                        quote.style.display = 'block';
                    } else {
                        quote.style.display = 'none';
                    }
                });
            }

            function sortQuotes(sortBy) {
                // TODO: Implement client-side sorting or AJAX request
                console.log('Sort quotes by:', sortBy);
            }

            function editQuote(quoteId) {
                // TODO: Implement edit quote modal
                console.log('Edit quote:', quoteId);
            }

            function deleteQuote(quoteId) {
                if (confirm('Ви впевнені, що хочете видалити цю цитату? Цю дію неможливо скасувати.')) {
                    // TODO: Implement delete quote
                    console.log('Delete quote:', quoteId);
                }
            }

            function shareQuote(quoteId) {
                // TODO: Implement share quote functionality
                console.log('Share quote:', quoteId);
            }
        </script>
    @endpush
@endsection
