@extends('users.public.main')

@section('title', $user->name . ' - Цитати')

@push('styles')
<style>
    /* Прибираємо стандартну стрілочку з Tailwind для наших select */
    #bookFilter,
    #sortBy {
        background-image: none !important;
        background-position: initial !important;
        background-repeat: initial !important;
        background-size: initial !important;
        -webkit-print-color-adjust: unset !important;
        print-color-adjust: unset !important;
    }
</style>
@endpush

@section('profile-content')
    <div class="flex-1">
        <!-- Quotes Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Цитати користувача</h2>
                    <p class="text-gray-600 dark:text-gray-400">Збережені цитати з прочитаних книг</p>
                </div>
            </div>

            <!-- Quotes Stats -->
            <div class="grid grid-cols-3 gap-2 lg:gap-4">
                @php
                    $allQuotesForStats = $user->quotes()->where('is_public', true)->with('book')->get();
                    $totalQuotes = $allQuotesForStats->count();
                    $totalLikes = $allQuotesForStats->sum('likes_count');
                    $averageLength = $allQuotesForStats->avg('length');
                @endphp

                <div class="bg-white dark:bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-blue-400 mb-1">{{ $totalQuotes }}</div>
                    <div class="text-sm text-black dark:text-gray-300">Усього цитат</div>
                </div>
                
                <div class="bg-white dark:bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-green-400 mb-1">{{ $averageLength ? number_format($averageLength) : 0 }}</div>
                    <div class="text-sm text-black dark:text-gray-300 ">Середня довжина</div>
                </div>
                
                <div class="bg-white dark:bg-white/5 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-purple-400 mb-1">{{ $totalLikes }}</div>
                    <div class="text-sm text-black dark:text-gray-300">Вподобань отримано</div>
                </div>
            </div>
        </div>

        <!-- Quotes Content -->
        <div>
            <div class="flex flex-col mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Список цитат</h3>
                <!-- Filter and Sort -->
                <div class="flex items-center space-x-4 mt-2">
                    <div class="relative">
                        <select id="bookFilter" onchange="filterByBook(this.value)" 
                                class="px-3 py-2 pr-8 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500 appearance-none cursor-pointer">
                            <option value="" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">Всі книги</option>
                            @php
                                $booksForFilter = $user->quotes()->where('is_public', true)->with('book')->get()->pluck('book')->unique('id');
                            @endphp
                            @foreach($booksForFilter as $book)
                                <option value="{{ $book->id }}" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">{{ $book->title }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <select id="sortBy" onchange="sortQuotes(this.value)" 
                                class="px-3 py-2 pr-8 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-orange-500 appearance-none cursor-pointer">
                            <option value="created_at" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">Дата додавання</option>
                            <option value="book_title" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">Назва книги</option>
                            <option value="likes_count" class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">Кількість лайків</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quotes List -->
            <div id="quotesList" class="space-y-6">
                @php
                    $quotes = $user->quotes()
                        ->where('is_public', true)
                        ->with('book.author')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
                @endphp

                @if($quotes->count() > 0)
                    @foreach($quotes as $quote)
                        <div class="bg-white/5 rounded-xl p-6 hover:bg-white/10 transition-all duration-200 group" 
                             data-book-id="{{ $quote->book_id }}">
                            <div class="flex items-start space-x-4">
                                <!-- Quote Content -->
                                <div class="flex-1 min-w-0">
                                    <!-- Header -->
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 break-words" 
                                                style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
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
                                    <div class="relative">
                                        <!-- Quote Marks -->
                                        <div class="absolute -top-2 -left-2 text-4xl text-orange-500/30 font-serif">"</div>
                                        <div class="absolute -bottom-4 -right-2 text-4xl text-orange-500/30 font-serif">"</div>
                                        
                                        <blockquote class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300 mb-4 pl-6 pr-6 italic break-words" 
                                                    style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                                            {{ $quote->content }}
                                        </blockquote>
                                    </div>

                                    <!-- Quote Context -->
                                    @if($quote->context)
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-4 pl-6 break-words" 
                                             style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
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
                                            <button onclick="shareQuote({{ $quote->id }}, '{{ $quote->book->slug }}')" 
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
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Ще немає цитат</h3>
                        <p class="text-gray-500 mb-6">Користувач ще не додав жодної цитати</p>
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

            async function shareQuote(quoteId, bookSlug) {
                try {
                    // Используем shareContent из app.js если доступен
                    if (window.shareContent) {
                        const quoteElement = document.querySelector(`[onclick*="shareQuote(${quoteId}"]`);
                        const quoteText = quoteElement ? quoteElement.closest('.bg-white\\/5').querySelector('blockquote')?.textContent?.trim() : '';
                        const userName = '{{ $user->name }}';
                        
                        await window.shareContent({
                            title: 'Цитата з книги',
                            text: quoteText ? `"${quoteText}" — ${userName}` : `Цитата від ${userName}`,
                            url: `${window.location.origin}/books/${bookSlug}#quote-${quoteId}`
                        });
                        return;
                    }

                    // Fallback к нативному шарингу или буферу обмена
                    const url = `${window.location.origin}/books/${bookSlug}#quote-${quoteId}`;
                    if (navigator.share) {
                        await navigator.share({
                            title: 'Цитата з книги',
                            text: `Цитата від {{ $user->name }}`,
                            url: url
                        });
                    } else {
                        await navigator.clipboard.writeText(url);
                        if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
                            window.alertModalInstance.$refs.modal.alert('Посилання скопійовано в буфер обміну!', 'Успіх', 'success');
                        } else {
                            alert('Посилання скопійовано в буфер обміну!');
                        }
                    }
                } catch (error) {
                    console.error('Error sharing quote:', error);
                    if (error.name !== 'AbortError') {
                        const url = `${window.location.origin}/books/${bookSlug}#quote-${quoteId}`;
                        if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
                            window.alertModalInstance.$refs.modal.alert(`Посилання: ${url}`, 'Посилання', 'info');
                        } else {
                            alert(`Посилання: ${url}`);
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
