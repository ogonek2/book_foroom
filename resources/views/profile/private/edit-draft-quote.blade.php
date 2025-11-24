@extends('layouts.app')

@section('title', 'Редагувати чернетку цитати')

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@section('main')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Header -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Редагувати чернетку цитати</h2>
                            @if($book)
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                <a href="{{ route('books.show', $book->slug) }}" class="text-blue-500 hover:text-blue-600 dark:text-blue-400">
                                    {{ $book->title }}
                                </a>
                            </p>
                            @endif
                        </div>
                        <a href="{{ route('profile.show', ['tab' => 'drafts']) }}" 
                           class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Назад до чернеток
                        </a>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
        <form action="{{ route('books.quotes.update', [$book, $quote]) }}" method="POST" id="edit-quote-form">
            @csrf
            @method('PUT')

            <!-- Quote Content -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Текст цитати <span class="text-red-500">*</span>
                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400 ml-2" id="content-counter-quote">
                        (<span id="content-length-quote">{{ mb_strlen(old('content', $quote->content ?? '')) }}</span> / 
                        <span id="content-max-quote">500</span> символів)
                    </span>
                </label>
                <textarea name="content" 
                          id="content-textarea-quote"
                          rows="6"
                          required
                          class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none italic"
                          placeholder="Введіть текст цитати...">{{ old('content', $quote->content ?? '') }}</textarea>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Мінімум: 20 символів, Максимум: 500 символів
                </div>
                @error('content')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Page Number -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Номер сторінки (необов'язково)
                </label>
                <input type="number" 
                       name="page_number" 
                       min="1"
                       value="{{ old('page_number', $quote->page_number ?? '') }}"
                       class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Наприклад: 42">
                @error('page_number')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Public/Private Toggle -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="is_public" 
                           value="1"
                           {{ old('is_public', $quote->is_public ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Зробити цитату публічною</span>
                </label>
            </div>

            <!-- Draft Checkbox -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="is_draft" 
                           value="1"
                           id="is-draft-checkbox"
                           {{ old('is_draft', $quote->is_draft ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Зберегти як чернетку</span>
                </label>
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-wrap gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" 
                        name="action" 
                        value="publish"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-xl font-medium transition-all transform hover:scale-105">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Опублікувати
                </button>
                <button type="submit" 
                        name="action" 
                        value="save"
                        class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium transition-colors">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Зберегти чернетку
                </button>
                <a href="{{ route('profile.show', ['tab' => 'drafts']) }}" 
                   class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Скасувати
                </a>
            </div>
        </form>
                </div>
            </div>

            <!-- Sticky Sidebar with Book Info -->
            @if($book)
            <div class="lg:col-span-1">
                <div class="sticky top-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <!-- Book Cover -->
                        <div class="p-4">
                            <a href="{{ route('books.show', $book->slug) }}" class="block group">
                                <div class="relative overflow-hidden rounded-xl shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:scale-105">
                                    <img src="{{ $book->cover_image ?: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=450&fit=crop&crop=center' }}"
                                         alt="{{ $book->title }}"
                                         class="w-full object-cover"
                                         style="aspect-ratio: 2 / 3;">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                                </div>
                            </a>
                        </div>

                        <!-- Book Info -->
                        <div class="px-4 pb-4">
                            <a href="{{ route('books.show', $book->slug) }}" class="block group">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors line-clamp-2">
                                    {{ $book->title }}
                                </h3>
                            </a>
                            
                            @if($book->author)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                <span class="font-medium">Автор:</span> {{ $book->author }}
                            </p>
                            @endif

                            @if($book->rating)
                            <div class="flex items-center mb-3">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($book->rating / 2))
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="ml-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    {{ number_format($book->rating, 1) }}/10
                                </span>
                            </div>
                            @endif

                            @if($book->publication_year)
                            <p class="text-xs text-gray-500 dark:text-gray-500 mb-3">
                                <span class="font-medium">Рік видання:</span> {{ $book->publication_year }}
                            </p>
                            @endif

                            @if($book->pages)
                            <p class="text-xs text-gray-500 dark:text-gray-500 mb-4">
                                <span class="font-medium">Сторінок:</span> {{ $book->pages }}
                            </p>
                            @endif

                            <a href="{{ route('books.show', $book->slug) }}" 
                               class="block w-full text-center px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg font-medium transition-all transform hover:scale-105">
                                Переглянути книгу
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for quote content
    const contentTextarea = document.getElementById('content-textarea-quote');
    const contentLength = document.getElementById('content-length-quote');
    
    function updateCharacterCount() {
        if (contentTextarea && contentLength) {
            const length = contentTextarea.value.length;
            contentLength.textContent = length;
            const min = 20;
            const max = 500;
            
            if (length < min) {
                contentLength.classList.add('text-red-500');
                contentLength.classList.remove('text-gray-500');
            } else if (length > max) {
                contentLength.classList.add('text-red-500');
                contentLength.classList.remove('text-gray-500');
            } else {
                contentLength.classList.remove('text-red-500');
                contentLength.classList.add('text-gray-500');
            }
        }
    }
    
    if (contentTextarea) {
        updateCharacterCount();
        contentTextarea.addEventListener('input', updateCharacterCount);
    }
    // Form submit handler
    const form = document.getElementById('edit-quote-form');
    const isDraftCheckbox = document.getElementById('is-draft-checkbox');
    
    form.addEventListener('submit', function(e) {
        const submitButton = document.activeElement;
        
        if (submitButton && submitButton.name === 'action') {
            if (submitButton.value === 'publish') {
                // При публикации снимаем флаг черновика
                isDraftCheckbox.checked = false;
            } else if (submitButton.value === 'save') {
                // При сохранении как черновик ставим флаг
                isDraftCheckbox.checked = true;
            }
        }
    });
});
</script>
@endpush

