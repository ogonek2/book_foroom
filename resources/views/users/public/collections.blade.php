@extends('users.public.main')

@section('title', $user->name . ' - Добірки')

@section('profile-content')
    <div class="flex-1">
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 shadow-2xl">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Добірки користувача</h2>

            @if ($libraries->count() > 0)
                <!-- Collections List -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    @foreach ($libraries as $library)
                        <div class="bg-white/5 rounded-xl p-6 hover:bg-white/10 transition-all group cursor-pointer"
                            onclick="loadLibraryBooks({{ $library->id }})">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-gray-900 dark:text-white font-semibold group-hover:text-purple-600 dark:group-hover:text-purple-200 transition-colors">
                                    {{ $library->name }}
                                </h3>
                                <span class="bg-purple-500/20 text-purple-300 px-2 py-1 rounded-full text-xs">
                                    {{ $library->books_count }} книг
                                </span>
                            </div>

                            @if ($library->description)
                                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                                    {{ Str::limit($library->description, 100) }}
                                </p>
                            @endif

                             <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                 <span>{{ $library->created_at->diffForHumans() }}</span>
                                 <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded-full text-xs">Публічна</span>
                             </div>
                        </div>
                    @endforeach
                </div>

                <!-- Selected Library Books -->
                <div id="library-books-section" class="mt-8">
                    @if ($selectedLibrary)
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                                Книги з колекції "{{ $selectedLibrary->name }}"
                            </h3>

                            @if ($books->count() > 0)
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                                    @foreach ($books as $book)
                                        <div class="group cursor-pointer">
                                            <div
                                                class="aspect-[3/4] bg-white/5 rounded-xl overflow-hidden mb-3 group-hover:bg-white/10 transition-all">
                                                @if ($book->cover_image)
                                                    <img src="{{ $book->cover_image }}" alt="{{ $book->title }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    <div
                                                        class="w-full h-full bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center">
                                                        <svg class="w-12 h-12 text-white" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>

                                            <h4
                                                class="text-gray-900 dark:text-white font-medium text-sm text-center group-hover:text-purple-600 dark:group-hover:text-purple-200 transition-colors">
                                                {{ Str::limit($book->title, 25) }}
                                            </h4>
                                            <p class="text-gray-600 dark:text-gray-400 text-xs text-center mt-1">
                                                {{ Str::limit($book->author, 20) }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Pagination -->
                                <div class="mt-8">
                                    {{ $books->links() }}
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <p class="text-gray-600 dark:text-gray-400">У цій колекції поки немає книг</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-600 dark:text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">Ще немає добірок</h3>
                    <p class="text-gray-600 dark:text-gray-400">Користувач ще не створив жодної колекції книг</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function loadLibraryBooks(libraryId) {
            // Реализация загрузки книг для выбранной библиотеки
            // Можно добавить AJAX запрос для динамической загрузки
            console.log('Loading books for library:', libraryId);
        }
    </script>
@endsection
