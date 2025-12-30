@extends('profile.private.main')

@section('profile-content')
    <div class="flex-1">
        <!-- Drafts Header -->
        <div class="mb-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Мої чернетки</h2>
                <p class="text-gray-600 dark:text-gray-400">Збережені чернетки рецензій, цитат та обговорень</p>
            </div>

            <!-- Drafts Stats -->
            <div class="grid grid-cols-3 gap-2 lg:gap-4">
                <div class="bg-white rounded-xl dark:bg-gray-800 p-4 text-center">
                    <div class="text-2xl font-bold text-blue-400 mb-1">{{ isset($draftReviews) ? $draftReviews->count() : 0 }}</div>
                    <div class="text-sm text-gray-900 dark:text-white">Рецензій</div>
                </div>
                
                <div class="bg-white rounded-xl dark:bg-gray-800 p-4 text-center">
                    <div class="text-2xl font-bold text-green-400 mb-1">{{ isset($draftQuotes) ? $draftQuotes->count() : 0 }}</div>
                    <div class="text-sm text-gray-900 dark:text-white">Цитат</div>
                </div>
                
                <div class="bg-white rounded-xl dark:bg-gray-800 p-4 text-center">
                    <div class="text-2xl font-bold text-purple-400 mb-1">{{ isset($draftDiscussions) ? $draftDiscussions->count() : 0 }}</div>
                    <div class="text-sm text-gray-900 dark:text-white">Обговорень</div>
                </div>
            </div>
        </div>

        <!-- Draft Reviews -->
        @if(isset($draftReviews) && $draftReviews->count() > 0)
        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Чернетки рецензій</h3>
            <div class="space-y-4">
                @foreach($draftReviews as $review)
                    <div class="bg-white rounded-xl p-4 border border-white/10">
                        <div class="flex flex-col items-start justify-between">
                            <div class="flex w-full items-center justify-between space-x-2 mb-2">
                                @if($review->book)
                                <a href="{{ route('books.reviews.edit-draft', [$review->book->slug, $review->id]) }}" 
                                   class="text-sm font-medium text-yellow-500 underline">
                                    Редагувати
                                </a>
                                @endif
                                <button onclick="deleteDraft('review', {{ $review->id }})" 
                                        class="px-4 py-2 bg-red-500/20 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    @if($review->book)
                                        <a href="{{ route('books.show', $review->book->slug) }}" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                            {{ $review->book->title }}
                                        </a>
                                    @endif
                                    <span class="text-gray-500 dark:text-gray-400 text-sm">
                                        {{ $review->updated_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 line-clamp-2">{{ Str::limit($review->content, 150) }}</p>
                                @if($review->rating)
                                    <div class="mt-2 text-yellow-500">
                                        @for($i = 0; $i < $review->rating; $i++)
                                            ★
                                        @endfor
                                    </div>
                                @endif
                            </div>
                            <button onclick="publishDraft('review', {{ $review->id }})" 
                                class="px-4 py-2 bg-purple-500 mt-2 text-white rounded-lg text-sm font-medium transition-colors">
                                Опублікувати
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Draft Quotes -->
        @if(isset($draftQuotes) && $draftQuotes->count() > 0)
        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Чернетки цитат</h3>
            <div class="space-y-4">
                @foreach($draftQuotes as $quote)
                    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                        <div class="flex flex-col gap-2 items-start justify-between">
                            <div class="flex space-x-2 w-full justify-between items-center mb-2">
                                @if($quote->book)
                                <a href="{{ route('books.quotes.edit-draft', [$quote->book->slug, $quote->id]) }}" 
                                   class="text-sm font-medium text-yellow-500 underline">
                                    Редагувати
                                </a>
                                @endif
                                <button onclick="deleteDraft('quote', {{ $quote->id }})" 
                                        class="px-4 py-2 bg-red-500/20 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors">
                                    <i class="fas fa-xmark"></i>
                                </button>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    @if($quote->book)
                                        <a href="{{ route('books.show', $quote->book->slug) }}" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                            {{ $quote->book->title }}
                                        </a>
                                    @endif
                                    @if($quote->page_number)
                                        <span class="text-gray-500 dark:text-gray-400 text-sm">Сторінка {{ $quote->page_number }}</span>
                                    @endif
                                    <span class="text-gray-500 dark:text-gray-400 text-sm">
                                        {{ $quote->updated_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 italic line-clamp-2">"{{ Str::limit($quote->content, 150) }}"</p>
                            </div>
                            <button onclick="publishDraft('quote', {{ $quote->id }})" 
                                    class="px-4 py-2 bg-purple-500 mt-2 text-white rounded-lg text-sm font-medium transition-colors">
                                Опублікувати
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Draft Discussions -->
        @if(isset($draftDiscussions) && $draftDiscussions->count() > 0)
        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Чернетки обговорень</h3>
            <div class="space-y-4">
                @foreach($draftDiscussions as $discussion)
                    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                        <div class="flex flex-col items-start justify-between">
                            <div class="flex w-full items-center justify-between space-x-2 mb-2">
                                <a href="{{ route('discussions.edit', $discussion) }}" 
                                   class="text-sm font-medium text-yellow-500 underline">
                                    Редагувати
                                </a>
                                <button onclick="deleteDraft('discussion', {{ $discussion->id }})" 
                                        class="px-4 py-2 bg-red-500/20 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors">
                                    <i class="fas fa-xmark"></i>
                                </button>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $discussion->title }}</h4>
                                <p class="text-gray-700 dark:text-gray-300 line-clamp-2">{{ Str::limit(strip_tags($discussion->content), 150) }}</p>
                                <span class="text-gray-500 dark:text-gray-400 text-sm mt-2 block">
                                    {{ $discussion->updated_at->diffForHumans() }}
                                </span>
                            </div>
                            <button onclick="publishDraft('discussion', {{ $discussion->id }})" 
                                    class="px-4 py-2 bg-purple-500 mt-2 text-white rounded-lg text-sm font-medium transition-colors">
                                Опублікувати
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if((!isset($draftReviews) || $draftReviews->count() === 0) && (!isset($draftQuotes) || $draftQuotes->count() === 0) && (!isset($draftDiscussions) || $draftDiscussions->count() === 0))
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-12 border border-white/20 shadow-2xl text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Немає чернеток</h3>
            <p class="text-gray-600 dark:text-gray-400">Ви ще не зберегли жодної чернетки</p>
        </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        async function publishDraft(type, id) {
            const confirmed = await confirm('Ви впевнені, що хочете опублікувати цю чернетку?', 'Підтвердження', 'warning');
            if (!confirmed) {
                return;
            }

            const url = `/drafts/${type}/${id}/publish`;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Чернетку опубліковано!', 'Успіх', 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    alert('Помилка: ' + (data.message || 'Не вдалося опублікувати чернетку'), 'Помилка', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Помилка при публікації чернетки', 'Помилка', 'error');
            });
        }

        async function deleteDraft(type, id) {
            const confirmed = await confirm('Ви впевнені, що хочете видалити цю чернетку? Цю дію неможливо скасувати.', 'Підтвердження', 'warning');
            if (!confirmed) {
                return;
            }

            const url = `/drafts/${type}/${id}`;
            
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Чернетку видалено!', 'Успіх', 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    alert('Помилка: ' + (data.message || 'Не вдалося видалити чернетку'), 'Помилка', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Помилка при видаленні чернетки', 'Помилка', 'error');
            });
        }


    </script>
@endpush

