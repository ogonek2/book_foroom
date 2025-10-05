@extends('profile.layout')

@section('profile-content')
<div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white">Мої добірки</h2>
            @if (auth()->check() && auth()->user()->id === $user->id)
                <button onclick="openCreateLibraryModal()"
                    class="px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all">
                    <i class="fas fa-plus mr-2"></i>Створити добірку
                </button>
            @endif
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if ($libraries->count() > 0)
            <!-- Tabs Navigation -->
            <div class="mb-8">
                <div class="flex flex-wrap gap-2">
                    @foreach ($libraries as $library)
                        <button onclick="switchLibrary({{ $library->id }})"
                            class="library-tab px-6 py-3 text-sm font-medium rounded-md transition-all duration-200 {{ $selectedLibrary && $selectedLibrary->id === $library->id ? 'bg-orange-500 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white' }}"
                            data-library-id="{{ $library->id }}">
                            <div class="flex items-center space-x-2">
                                <span>{{ $library->name }}</span>
                                <span class="bg-gray-600 text-gray-300 text-xs px-2 py-1 rounded-full">
                                    {{ $library->books_count }}
                                </span>
                                @if ($library->is_private)
                                    <i class="fas fa-lock text-xs"></i>
                                @else
                                    <i class="fas fa-globe text-xs"></i>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Library Info -->
            <div id="library-info" class="mb-6">
                @if ($selectedLibrary)
                    <div class="bg-gray-800 rounded-lg p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-white mb-2">{{ $selectedLibrary->name }}</h3>
                                @if ($selectedLibrary->description)
                                    <p class="text-gray-400 mb-4">{{ $selectedLibrary->description }}</p>
                                @endif
                                <div class="flex items-center space-x-4 text-sm text-gray-400">
                                    <span>
                                        <i class="fas fa-book mr-1"></i>
                                        {{ $selectedLibrary->books_count }}
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $selectedLibrary->created_at->format('d.m.Y') }}
                                    </span>
                                    @if ($selectedLibrary->is_private)
                                        <span
                                            class="bg-red-500/20 text-red-400 text-xs font-medium px-4 py-2 rounded-full border border-red-500/30">
                                            <i class="fas fa-lock mr-1"></i>Приватна
                                        </span>
                                    @else
                                        <span
                                            class="bg-green-500/20 text-green-400 text-xs font-medium px-4 py-2 rounded-full border border-green-500/30">
                                            <i class="fas fa-globe mr-1"></i>Публічна
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if (auth()->check() && auth()->user()->id === $user->id)
                                <div class="flex space-x-2">
                                    <button
                                        onclick="openEditLibraryModal({{ $selectedLibrary->id }}, '{{ $selectedLibrary->name }}', '{{ $selectedLibrary->description }}', {{ $selectedLibrary->is_private ? 'true' : 'false' }})"
                                        class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded text-sm font-medium transition duration-200">
                                        <i class="fas fa-edit mr-1"></i>Редагувати
                                    </button>
                                    <form action="{{ route('libraries.destroy', $selectedLibrary) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Ви впевнені, що хочете видалити цю добірку?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-indigo-600 text-white py-2 px-3 rounded text-sm font-medium transition duration-200">
                                            <i class="fas fa-trash mr-1"></i>Видалити
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Books Grid -->
            <div id="books-container">
                @if ($books->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($books as $book)
                            <div
                                class="bg-gray-800 rounded-lg relative overflow-hidden shadow-md hover:shadow-lg transition duration-200">
                                <div class="flex flex-col h-full">
                                    <div class="aspect-[3/4]">
                                        <img src="{{ $book->cover_image }}" alt="{{ $book->title }}"
                                            class="w-full h-full object-cover">
                                    </div>

                                    <div class="p-4 flex flex-col justify-between h-full">
                                        <div>
                                            <h3 class="text-lg font-semibold text-white mb-2 line-clamp-2 p-0">
                                                <a href="{{ route('books.show', $book->slug) }}"
                                                    class="hover:text-orange-400 transition duration-200">
                                                    {{ $book->title }}
                                                </a>
                                            </h3>

                                            <p class="text-gray-400 text-sm mb-3">
                                                {{ $book->author->first_name ?? ($book->author ?? 'Не указан') }}</p>
                                        </div>

                                        @if (auth()->check() && auth()->user()->id === $user->id)
                                            <div>
                                                <form
                                                    action="{{ route('libraries.removeBook', [$selectedLibrary, $book]) }}"
                                                    method="POST" onsubmit="return confirm('Удалить книгу из библиотеки?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="bg-indigo-600 text-white py-2 px-3 rounded text-sm font-medium transition duration-200">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if ($books->hasPages())
                        <div class="mt-8">
                            {{ $books->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12 bg-gray-800 rounded-lg">
                        <div class="text-gray-400 text-6xl mb-4">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Бібліотека порожня</h3>
                        <p class="text-gray-500 mb-6">В цій добірці поки немає книг</p>
                        @if (auth()->check() && auth()->user()->id === $user->id)
                            <a href="{{ route('books.index') }}"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all">
                                <i class="fas fa-plus mr-2"></i>Додати книги
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @else
    <div class="text-center py-12 bg-gray-800 rounded-lg">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                    </path>
        </svg>
        <h3 class="text-xl font-semibold text-gray-300 mb-2">Ще немає добірок</h3>
        <p class="text-gray-500 mb-6">Створюйте добірки книг за темами</p>
                @if (auth()->check() && auth()->user()->id === $user->id)
                    <button onclick="openCreateLibraryModal()"
                        class="px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all">
                        <i class="fas fa-plus mr-2"></i>Створити добірку
        </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Modal для создания библиотеки -->
    @if (auth()->check() && auth()->user()->id === $user->id)
        <div id="createLibraryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-white">Створити добірку</h3>
                        <button onclick="closeCreateLibraryModal()" class="text-gray-400 hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form action="{{ route('libraries.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-gray-300 text-sm font-medium mb-2">Назва</label>
                            <input type="text" name="name" required
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-orange-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-300 text-sm font-medium mb-2">Опис (необов'язково)</label>
                            <textarea name="description" rows="3"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-orange-500"></textarea>
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_private" value="1" class="mr-2">
                                <span class="text-gray-300">Приватна добірка</span>
                            </label>
                        </div>

                        <div class="flex space-x-3">
                            <button type="submit"
                                class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                                Створити
                            </button>
                            <button type="button" onclick="closeCreateLibraryModal()"
                                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                                Скасувати
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal для редактирования библиотеки -->
        <div id="editLibraryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-white">Редагувати добірку</h3>
                        <button onclick="closeEditLibraryModal()" class="text-gray-400 hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="editLibraryForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-gray-300 text-sm font-medium mb-2">Назва</label>
                            <input type="text" name="name" id="editName" required
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-orange-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-300 text-sm font-medium mb-2">Опис (необов'язково)</label>
                            <textarea name="description" id="editDescription" rows="3"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-orange-500"></textarea>
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_private" id="editIsPrivate" value="1"
                                    class="mr-2">
                                <span class="text-gray-300">Приватна добірка</span>
                            </label>
                        </div>

                        <div class="flex space-x-3">
                            <button type="submit"
                                class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                                Зберегти
                            </button>
                            <button type="button" onclick="closeEditLibraryModal()"
                                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                                Скасувати
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function openCreateLibraryModal() {
                document.getElementById('createLibraryModal').classList.remove('hidden');
            }

            function closeCreateLibraryModal() {
                document.getElementById('createLibraryModal').classList.add('hidden');
            }

            function openEditLibraryModal(id, name, description, isPrivate) {
                document.getElementById('editLibraryForm').action = `/libraries/${id}`;
                document.getElementById('editName').value = name;
                document.getElementById('editDescription').value = description || '';
                document.getElementById('editIsPrivate').checked = isPrivate;
                document.getElementById('editLibraryModal').classList.remove('hidden');
            }

            function closeEditLibraryModal() {
                document.getElementById('editLibraryModal').classList.add('hidden');
            }

            // Функция переключения между библиотеками
            function switchLibrary(libraryId) {
                // Показываем индикатор загрузки
                const booksContainer = document.getElementById('books-container');
                booksContainer.innerHTML = `
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500"></div>
            <span class="ml-3 text-gray-400">Завантаження...</span>
        </div>
    `;

                // Обновляем активную вкладку
                document.querySelectorAll('.library-tab').forEach(tab => {
                    tab.classList.remove('bg-orange-500', 'text-white');
                    tab.classList.add('bg-gray-700', 'text-gray-300');
                });

                const activeTab = document.querySelector(`[data-library-id="${libraryId}"]`);
                if (activeTab) {
                    activeTab.classList.remove('bg-gray-700', 'text-gray-300');
                    activeTab.classList.add('bg-orange-500', 'text-white');
                }

                // Загружаем данные библиотеки
                fetch(`/profile/{{ $user->username }}/collections/${libraryId}/books`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            booksContainer.innerHTML = `
                    <div class="text-center py-12 bg-gray-800 rounded-lg">
                        <div class="text-red-400 text-6xl mb-4">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-300 mb-2">Помилка</h3>
                        <p class="text-gray-500">${data.error}</p>
                    </div>
                `;
                            return;
                        }

                        // Обновляем информацию о библиотеке
                        updateLibraryInfo(data.library);

                        // Обновляем список книг
                        updateBooksGrid(data.books, libraryId);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        booksContainer.innerHTML = `
                <div class="text-center py-12 bg-gray-800 rounded-lg">
                    <div class="text-red-400 text-6xl mb-4">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-300 mb-2">Помилка завантаження</h3>
                    <p class="text-gray-500">Не вдалося завантажити дані</p>
                </div>
            `;
                    });
            }

            // Обновление информации о библиотеке
            function updateLibraryInfo(library) {
                const libraryInfo = document.getElementById('library-info');
                const isOwner = {{ auth()->check() && auth()->user()->id === $user->id ? 'true' : 'false' }};
                const csrfToken = '{{ csrf_token() }}';
                const privacyIcon = library.is_private ? 'fa-lock' : 'fa-globe';
                const privacyText = library.is_private ? 'Приватна' : 'Публічна';
                const privacyClass = library.is_private ? 'bg-red-500/20 text-red-400 border-red-500/30' :
                    'bg-green-500/20 text-green-400 border-green-500/30';

                const actionButtons = isOwner ? `
        <div class="flex space-x-2">
            <button onclick="openEditLibraryModal(${library.id}, '${library.name}', '${library.description || ''}', ${library.is_private})" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded text-sm font-medium transition duration-200">
                <i class="fas fa-edit mr-1"></i>Редагувати
            </button>
            <form action="/libraries/${library.id}" method="POST" class="inline" onsubmit="return confirm('Ви впевнені, що хочете видалити цю добірку?')">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="bg-indigo-600 text-white py-2 px-3 rounded text-sm font-medium transition duration-200">
                    <i class="fas fa-trash mr-1"></i>Видалити
                </button>
            </form>
        </div>
    ` : '';

                libraryInfo.innerHTML = `
        <div class="bg-gray-800 rounded-lg p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-white mb-2">${library.name}</h3>
                    ${library.description ? `<p class="text-gray-400 mb-4">${library.description}</p>` : ''}
                    <div class="flex items-center space-x-4 text-sm text-gray-400">
                        <span>
                            <i class="fas fa-book mr-1"></i>
                            ${library.books_count}
                        </span>
                        <span>
                            <i class="fas fa-calendar mr-1"></i>
                            ${new Date(library.created_at).toLocaleDateString('uk-UA')}
                        </span>
                        <span class="bg-red-500/20 text-red-400 text-xs font-medium px-4 py-2 rounded-full border border-red-500/30">
                            <i class="fas ${privacyIcon} mr-1"></i>${privacyText}
                        </span>
                    </div>
                </div>
                ${actionButtons}
            </div>
        </div>
    `;
            }

            // Обновление сетки книг
            function updateBooksGrid(books, libraryId) {
                const booksContainer = document.getElementById('books-container');
                const isOwner = {{ auth()->check() && auth()->user()->id === $user->id ? 'true' : 'false' }};
                const booksIndexUrl = '{{ route('books.index') }}';
                const csrfToken = '{{ csrf_token() }}';

                if (books.length === 0) {
                    const addBooksButton = isOwner ? `
            <a href="${booksIndexUrl}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all">
                <i class="fas fa-plus mr-2"></i>Додати книги
            </a>
        ` : '';

                    booksContainer.innerHTML = `
            <div class="text-center py-12 bg-gray-800 rounded-lg">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-300 mb-2">Бібліотека порожня</h3>
                <p class="text-gray-500 mb-6">В цій добірці поки немає книг</p>
                ${addBooksButton}
            </div>
        `;
                    return;
                }

                let booksHTML = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">';

                books.forEach(book => {
                    const authorName = book.author ? (book.author.first_name || book.author) : 'Не указан';
                    const rating = book.rating ? `
            <div class="flex items-center mb-3">
                <div class="flex space-x-1">
                    ${Array.from({length: 5}, (_, i) => ` <
                        svg class = "w-4 h-4 ${i < book.rating/2 ? 'text-yellow-400' : 'text-gray-600'}"
                    fill = "currentColor"
                    viewBox = "0 0 20 20" >
                        <
                        path d =
                        "M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" >
                        <
                        /path> < /
                        svg >
                        `).join('')}
                </div>
                <span class="text-gray-400 text-sm ml-2">${parseFloat(book.rating).toFixed(1)}/10</span>
            </div>
        `: '';

                    const removeButton = isOwner ? `
            <div>
                <form action="/libraries/${libraryId}/books/${book.id}" method="POST" onsubmit="return confirm('Удалить книгу из библиотеки?')">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="bg-indigo-600 text-white py-2 px-3 rounded text-sm font-medium transition duration-200">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        ` : '';

                    booksHTML += `
            <div class="bg-gray-800 rounded-lg relative overflow-hidden shadow-md hover:shadow-lg transition duration-200">
                <div class="flex flex-col h-full">
                    <div class="aspect-[3/4]">
                        <img src="${book.cover_image || '/images/no-cover.png'}" 
                             alt="${book.title}" 
                             class="w-full h-full object-cover">
                    </div>
                    
                    <div class="p-4 flex flex-col justify-between h-full">
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-2 line-clamp-2 p-0">
                                <a href="/books/${book.slug}" class="hover:text-orange-400 transition duration-200">
                                    ${book.title}
                                </a>
                            </h3>
                            
                            <p class="text-gray-400 text-sm mb-3">${authorName}</p>
                        </div>
                        
                        ${removeButton}
                    </div>
                </div>
            </div>
        `;
                });

                booksHTML += '</div>';
                booksContainer.innerHTML = booksHTML;
            }

            // Закрытие модальных окон при клике вне их
            document.addEventListener('click', function(event) {
                const createModal = document.getElementById('createLibraryModal');
                const editModal = document.getElementById('editLibraryModal');

                if (event.target === createModal) {
                    closeCreateLibraryModal();
                }
                if (event.target === editModal) {
                    closeEditLibraryModal();
                }
            });
        </script>
    @endif
@endsection
