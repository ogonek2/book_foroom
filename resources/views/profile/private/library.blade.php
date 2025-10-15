@extends('profile.private.main')

@section('profile-content')
    <div class="flex-1">
        <!-- Library Header -->
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Моя бібліотека</h2>
                    <p class="text-gray-600 dark:text-gray-400">Книги з різними статусами читання</p>
                </div>
                <!-- Manage Library Button -->
                <button onclick="manageLibrary()" 
                        class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg font-medium transition-all duration-300 backdrop-blur-sm">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Керувати
                </button>
            </div>

            <!-- Reading Status Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $readingStatuses = [
                        'want_to_read' => ['title' => 'Хочу прочитати', 'count' => $user->bookReadingStatuses()->where('status', 'want_to_read')->count(), 'color' => 'blue'],
                        'reading' => ['title' => 'Читаю', 'count' => $user->bookReadingStatuses()->where('status', 'reading')->count(), 'color' => 'orange'],
                        'read' => ['title' => 'Прочитано', 'count' => $user->bookReadingStatuses()->where('status', 'read')->count(), 'color' => 'green'],
                        'abandoned' => ['title' => 'Закинуто', 'count' => $user->bookReadingStatuses()->where('status', 'abandoned')->count(), 'color' => 'red'],
                    ];
                @endphp

                @foreach($readingStatuses as $status => $data)
                    <div class="bg-white/5 rounded-xl p-4 text-center hover:bg-white/10 transition-all duration-200 cursor-pointer" 
                         onclick="filterByStatus('{{ $status }}')">
                        <div class="text-2xl font-bold text-{{ $data['color'] }}-400 mb-1">{{ $data['count'] }}</div>
                        <div class="text-sm text-gray-300">{{ $data['title'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Library Content -->
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Книги в бібліотеці</h3>
                <!-- Filter and Sort -->
                <div class="flex items-center space-x-4">
                    <select id="statusFilter" onchange="filterByStatus(this.value)" 
                            class="px-3 py-2 bg-white/20 text-white rounded-lg border border-white/30 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Всі статуси</option>
                        <option value="want_to_read">Хочу прочитати</option>
                        <option value="reading">Читаю</option>
                        <option value="read">Прочитано</option>
                        <option value="abandoned">Закинуто</option>
                    </select>
                    
                    <select id="sortBy" onchange="sortBooks(this.value)" 
                            class="px-3 py-2 bg-white/20 text-white rounded-lg border border-white/30 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="created_at">Дата додавання</option>
                        <option value="title">Назва</option>
                        <option value="rating">Оцінка</option>
                    </select>
                </div>
            </div>

            <!-- Books Grid -->
            <div id="booksGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @php
                    $books = $user->bookReadingStatuses()
                        ->with(['book.author', 'book.categories'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(12);
                @endphp

                @if($books->count() > 0)
                    @foreach($books as $readingStatus)
                        <div class="bg-white/5 rounded-xl p-4 hover:bg-white/10 transition-all duration-200 group" 
                             data-status="{{ $readingStatus->status }}">
                            <!-- Book Cover -->
                            <div class="aspect-[3/4] mb-3 relative">
                                <img src="{{ $readingStatus->book->cover_image }}" 
                                     alt="{{ $readingStatus->book->title }}"
                                     class="w-full h-full object-cover rounded-lg shadow-md">
                                
                                <!-- Status Badge -->
                                <div class="absolute top-2 right-2">
                                    @php
                                        $statusColors = [
                                            'want_to_read' => 'bg-blue-500',
                                            'reading' => 'bg-orange-500',
                                            'read' => 'bg-green-500',
                                            'abandoned' => 'bg-red-500',
                                        ];
                                        $statusLabels = [
                                            'want_to_read' => 'Хочу',
                                            'reading' => 'Читаю',
                                            'read' => 'Прочитано',
                                            'abandoned' => 'Закинуто',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium text-white rounded-full {{ $statusColors[$readingStatus->status] }}">
                                        {{ $statusLabels[$readingStatus->status] }}
                                    </span>
                                </div>

                                <!-- Rating -->
                                @if($readingStatus->rating)
                                    <div class="absolute bottom-2 left-2">
                                        <div class="flex items-center space-x-1 bg-black/50 backdrop-blur-sm rounded-full px-2 py-1">
                                            <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span class="text-xs font-medium text-white">{{ $readingStatus->rating }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Book Info -->
                            <div class="space-y-2">
                                <h4 class="font-semibold text-gray-900 dark:text-white line-clamp-2">
                                    <a href="{{ route('books.show', $readingStatus->book->slug) }}" 
                                       class="hover:text-orange-500 transition-colors">
                                        {{ $readingStatus->book->title }}
                                    </a>
                                </h4>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $readingStatus->book->author->first_name ?? $readingStatus->book->author ?? 'Не указан' }}
                                </p>

                                <!-- Actions -->
                                <div class="flex items-center justify-between pt-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $readingStatus->created_at->format('d.m.Y') }}
                                    </span>
                                    
                                    <div class="flex items-center space-x-2">
                                        <button onclick="editBookStatus({{ $readingStatus->id }})" 
                                                class="p-1 text-gray-400 hover:text-white transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button onclick="removeFromLibrary({{ $readingStatus->id }})" 
                                                class="p-1 text-gray-400 hover:text-red-400 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Empty State -->
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">
                            <svg class="w-24 h-24 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Бібліотека порожня</h3>
                        <p class="text-gray-500 mb-6">Додайте книги до своєї бібліотеки</p>
                        <a href="{{ route('books.index') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Додати книги
                        </a>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if($books->hasPages())
                <div class="mt-6">
                    {{ $books->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function manageLibrary() {
                // TODO: Implement library management modal
                console.log('Manage library');
            }

            function filterByStatus(status) {
                const books = document.querySelectorAll('#booksGrid > div[data-status]');
                books.forEach(book => {
                    if (!status || book.dataset.status === status) {
                        book.style.display = 'block';
                    } else {
                        book.style.display = 'none';
                    }
                });
            }

            function sortBooks(sortBy) {
                // TODO: Implement client-side sorting or AJAX request
                console.log('Sort by:', sortBy);
            }

            function editBookStatus(readingStatusId) {
                // TODO: Implement edit book status modal
                console.log('Edit book status:', readingStatusId);
            }

            function removeFromLibrary(readingStatusId) {
                if (confirm('Ви впевнені, що хочете видалити цю книгу з бібліотеки?')) {
                    // TODO: Implement remove from library
                    console.log('Remove from library:', readingStatusId);
                }
            }
        </script>
    @endpush
@endsection

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
