@extends('profile.private.main')

@section('profile-content')
    <div class="flex-1">
        <!-- Quotes Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Мої цитати</h2>
                    <p class="text-gray-600 dark:text-gray-400">Збережені цитати з прочитаних книг</p>
                </div>
            </div>

            <!-- Quotes Stats -->
            <div class="grid grid-cols-3 gap-2 lg:gap-4">
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
        <div>
            <div class="flex flex-col mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Список цитат</h3>
                <!-- Filter and Sort -->
                <div class="flex items-center space-x-4 mt-2">
                    <select id="bookFilter" onchange="filterByBook(this.value)" 
                            class="px-3 py-2 bg-white/20 light:text-gray-900 dark:text-white rounded-lg border border-white/30 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Всі книги</option>
                        @php
                            $books = $user->quotes()->with('book')->get()->pluck('book')->unique('id');
                        @endphp
                        @foreach($books as $book)
                            <option value="{{ $book->id }}">{{ $book->title }}</option>
                        @endforeach
                    </select>
                    
                    <select id="sortBy" onchange="sortQuotes(this.value)" 
                            class="px-3 py-2 bg-white/20 light:text-gray-900 dark:text-white rounded-lg border border-white/30 focus:outline-none focus:ring-2 focus:ring-orange-500">
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

                                        <!-- Actions -->
                                        <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button onclick="editQuote({{ $quote->id }}, '{{ $quote->book->slug }}', {{ json_encode([
                                                'content' => $quote->content,
                                                'page_number' => $quote->page_number,
                                                'is_public' => $quote->is_public
                                            ]) }})" 
                                                    class="p-2 text-gray-400 hover:text-blue-400 transition-colors"
                                                    title="Редагувати цитату">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button onclick="deleteQuote({{ $quote->id }}, '{{ $quote->book->slug }}')" 
                                                    class="p-2 text-gray-400 hover:text-red-400 transition-colors"
                                                    title="Видалити цитату">
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

    <!-- Edit Quote Modal -->
    <div id="editQuoteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Редагувати цитату</h3>
                    <button onclick="closeEditQuoteModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="editQuoteForm" onsubmit="submitEditQuote(event)">
                    <input type="hidden" id="editQuoteId" name="quote_id">
                    <input type="hidden" id="editQuoteBookSlug" name="book_slug">

                    <!-- Quote Content -->
                    <div class="mb-6">
                        <label for="editQuoteContent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Текст цитати <span class="text-red-500">*</span>
                        </label>
                        <textarea id="editQuoteContent" 
                                  name="content" 
                                  rows="4" 
                                  required
                                  minlength="20"
                                  maxlength="500"
                                  class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Мінімум: 20 символів, Максимум: 500 символів
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="editQuoteContentLength">0 / 500</p>
                    </div>

                    <!-- Page Number -->
                    <div class="mb-6">
                        <label for="editQuotePageNumber" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Номер сторінки (необов'язково)
                        </label>
                        <input type="number" 
                               id="editQuotePageNumber" 
                               name="page_number" 
                               min="1"
                               class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>

                    <!-- Public Status -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   id="editQuoteIsPublic" 
                                   name="is_public" 
                                   value="1"
                                   class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 dark:focus:ring-orange-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Публічна цитата</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Публічні цитати відображаються на вашому публічному профілі
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-4">
                        <button type="button" 
                                onclick="closeEditQuoteModal()"
                                class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Скасувати
                        </button>
                        <button type="submit" 
                                id="editQuoteSubmitBtn"
                                class="px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-xl font-medium hover:from-orange-600 hover:to-pink-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="editQuoteSubmitText">Зберегти</span>
                            <span id="editQuoteSubmitLoading" class="hidden">Збереження...</span>
                        </button>
                    </div>
                </form>
            </div>
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

            // Edit Quote Modal Functions
            function editQuote(quoteId, bookSlug, quoteData) {
                document.getElementById('editQuoteId').value = quoteId;
                document.getElementById('editQuoteBookSlug').value = bookSlug;
                document.getElementById('editQuoteContent').value = quoteData.content || '';
                document.getElementById('editQuotePageNumber').value = quoteData.page_number || '';
                document.getElementById('editQuoteIsPublic').checked = quoteData.is_public !== false;

                // Update character counter
                updateEditQuoteContentLength();

                // Show modal
                const modal = document.getElementById('editQuoteModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeEditQuoteModal() {
                const modal = document.getElementById('editQuoteModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                
                // Reset form
                document.getElementById('editQuoteForm').reset();
            }

            function updateEditQuoteContentLength() {
                const content = document.getElementById('editQuoteContent').value;
                const length = content.length;
                const lengthDisplay = document.getElementById('editQuoteContentLength');
                lengthDisplay.textContent = `${length} / 500`;
                
                if (length < 20 || length > 500) {
                    lengthDisplay.classList.add('text-red-500');
                    lengthDisplay.classList.remove('text-gray-500');
                } else {
                    lengthDisplay.classList.remove('text-red-500');
                    lengthDisplay.classList.add('text-gray-500');
                }
            }

            // Add event listener for content length
            document.addEventListener('DOMContentLoaded', function() {
                const contentTextarea = document.getElementById('editQuoteContent');
                if (contentTextarea) {
                    contentTextarea.addEventListener('input', updateEditQuoteContentLength);
                }
            });

            async function submitEditQuote(event) {
                event.preventDefault();

                const quoteId = document.getElementById('editQuoteId').value;
                const bookSlug = document.getElementById('editQuoteBookSlug').value;
                const content = document.getElementById('editQuoteContent').value.trim();
                const pageNumber = document.getElementById('editQuotePageNumber').value;
                const isPublic = document.getElementById('editQuoteIsPublic').checked;

                // Validate content length
                if (content.length < 20 || content.length > 500) {
                    if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
                        window.alertModalInstance.$refs.modal.alert('Цитата повинна містити від 20 до 500 символів.', 'Помилка валідації', 'error');
                    } else {
                        alert('Цитата повинна містити від 20 до 500 символів.');
                    }
                    return;
                }

                const submitBtn = document.getElementById('editQuoteSubmitBtn');
                const submitText = document.getElementById('editQuoteSubmitText');
                const submitLoading = document.getElementById('editQuoteSubmitLoading');

                submitBtn.disabled = true;
                submitText.classList.add('hidden');
                submitLoading.classList.remove('hidden');

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch(`/books/${bookSlug}/quotes/${quoteId}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            content: content,
                            page_number: pageNumber ? parseInt(pageNumber) : null,
                            is_public: isPublic
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Close modal
                        closeEditQuoteModal();

                        // Reload page to show updated quote
                        window.location.reload();
                    } else {
                        if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
                            window.alertModalInstance.$refs.modal.alert(data.message || 'Помилка при збереженні цитати.', 'Помилка', 'error');
                        } else {
                            alert(data.message || 'Помилка при збереженні цитати.');
                        }
                    }
                } catch (error) {
                    console.error('Error updating quote:', error);
                    if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
                        window.alertModalInstance.$refs.modal.alert('Помилка при збереженні цитати.', 'Помилка', 'error');
                    } else {
                        alert('Помилка при збереженні цитати.');
                    }
                } finally {
                    submitBtn.disabled = false;
                    submitText.classList.remove('hidden');
                    submitLoading.classList.add('hidden');
                }
            }

            async function deleteQuote(quoteId, bookSlug) {
                const confirmed = await confirm('Ви впевнені, що хочете видалити цю цитату? Цю дію неможливо скасувати.', 'Підтвердження', 'warning');
                if (!confirmed) {
                    return;
                }

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch(`/books/${bookSlug}/quotes/${quoteId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Удаляем элемент из DOM
                        const quoteElement = document.querySelector(`[onclick*="deleteQuote(${quoteId}"]`);
                        if (quoteElement) {
                            const quoteCard = quoteElement.closest('.bg-white\\/5');
                            if (quoteCard) {
                                quoteCard.style.transition = 'opacity 0.3s';
                                quoteCard.style.opacity = '0';
                                setTimeout(() => {
                                    quoteCard.remove();
                                }, 300);
                            }
                        }
                        
                        // Показываем уведомление
                        if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
                            window.alertModalInstance.$refs.modal.alert('Цитату видалено!', 'Успіх', 'success');
                        } else {
                            alert('Цитату видалено!');
                        }
                    } else {
                        if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
                            window.alertModalInstance.$refs.modal.alert(data.message || 'Помилка при видаленні цитати.', 'Помилка', 'error');
                        } else {
                            alert(data.message || 'Помилка при видаленні цитати.');
                        }
                    }
                } catch (error) {
                    console.error('Error deleting quote:', error);
                    if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
                        window.alertModalInstance.$refs.modal.alert('Помилка при видаленні цитати.', 'Помилка', 'error');
                    } else {
                        alert('Помилка при видаленні цитати.');
                    }
                }
            }

            async function shareQuote(quoteId, bookSlug) {
                try {
                    // Используем shareContent из app.js если доступен
                    if (window.shareContent) {
                        const quoteElement = document.querySelector(`[onclick*="shareQuote(${quoteId}"]`);
                        const quoteText = quoteElement ? quoteElement.closest('.bg-white\\/5').querySelector('blockquote')?.textContent?.trim() : '';
                        const userName = '{{ auth()->user()->name }}';
                        
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
                            text: `Цитата від {{ auth()->user()->name }}`,
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
