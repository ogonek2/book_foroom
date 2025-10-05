@extends('layouts.app')

@section('title', $library->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $library->name }}</h1>
            @if($library->description)
                <p class="text-gray-600 mb-4">{{ $library->description }}</p>
            @endif
            <div class="flex items-center space-x-4 text-sm text-gray-500">
                <span>
                    <i class="fas fa-user mr-1"></i>
                    {{ $library->user->name }}
                </span>
                <span>
                    <i class="fas fa-book mr-1"></i>
                    {{ $books->total() }} {{ Str::plural('книга', $books->total()) }}
                </span>
                <span>
                    <i class="fas fa-calendar mr-1"></i>
                    {{ $library->created_at->format('d.m.Y') }}
                </span>
                @if($library->is_private)
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        <i class="fas fa-lock mr-1"></i>Приватная
                    </span>
                @else
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        <i class="fas fa-globe mr-1"></i>Публичная
                    </span>
                @endif
            </div>
        </div>
        
        @if($library->canBeEditedBy(auth()->user()))
            <div class="flex space-x-2">
                <a href="{{ route('libraries.edit', $library) }}" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Редактировать
                </a>
                <button onclick="openAddBookModal()" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                    <i class="fas fa-plus mr-2"></i>Добавить книгу
                </button>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Books Grid -->
    @if($books->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($books as $book)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition duration-200 border border-gray-200">
                    <div class="p-4">
                        <div class="aspect-w-3 aspect-h-4 mb-4">
                            <img src="{{ $book->cover_image_display }}" 
                                 alt="{{ $book->title }}" 
                                 class="w-full h-64 object-cover rounded-lg">
                        </div>
                        
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                            <a href="{{ route('books.show', $book->slug) }}" class="hover:text-blue-600 transition duration-200">
                                {{ $book->title }}
                            </a>
                        </h3>
                        
                        <p class="text-gray-600 text-sm mb-3">{{ $book->author->first_name ?? $book->author ?? 'Не указан' }}</p>
                        
                        @if($book->rating)
                            <div class="flex items-center mb-3">
                                <div class="flex space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $book->rating/2 ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-gray-600 text-sm ml-2">{{ number_format($book->rating, 1) }}/10</span>
                            </div>
                        @endif
                        
                        @if($library->canBeEditedBy(auth()->user()))
                            <form action="{{ route('libraries.removeBook', [$library, $book]) }}" method="POST" class="mt-3" onsubmit="return confirm('Удалить книгу из библиотеки?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded text-sm font-medium transition duration-200">
                                    <i class="fas fa-trash mr-1"></i>Удалить из библиотеки
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $books->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">
                <i class="fas fa-book-open"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Библиотека пуста</h3>
            <p class="text-gray-600 mb-6">В этой библиотеке пока нет книг</p>
            @if($library->canBeEditedBy(auth()->user()))
                <button onclick="openAddBookModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
                    <i class="fas fa-plus mr-2"></i>Добавить первую книгу
                </button>
            @endif
        </div>
    @endif
</div>

<!-- Modal для добавления книги -->
@if($library->canBeEditedBy(auth()->user()))
<div id="addBookModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Добавить книгу</h3>
                <button onclick="closeAddBookModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('libraries.addBook', $library) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Выберите книгу</label>
                    <select name="book_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">-- Выберите книгу --</option>
                        @php
                            $availableBooks = \App\Models\Book::whereNotIn('id', $library->books->pluck('id'))->orderBy('title')->get();
                        @endphp
                        @foreach($availableBooks as $book)
                            <option value="{{ $book->id }}">{{ $book->title }} - {{ $book->author->first_name ?? $book->author ?? 'Не указан' }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                        Добавить
                    </button>
                    <button type="button" onclick="closeAddBookModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                        Отмена
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddBookModal() {
    document.getElementById('addBookModal').classList.remove('hidden');
}

function closeAddBookModal() {
    document.getElementById('addBookModal').classList.add('hidden');
}

// Закрытие модального окна при клике вне его
document.addEventListener('click', function(event) {
    const modal = document.getElementById('addBookModal');
    if (event.target === modal) {
        closeAddBookModal();
    }
});
</script>
@endif
@endsection
