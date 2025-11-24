@extends('layouts.app')

@section('title', 'Редагувати чернетку рецензії')

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
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Редагувати чернетку рецензії</h2>
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
        <form action="{{ route('books.reviews.update', [$book, $review]) }}" method="POST" id="edit-review-form">
            @csrf
            @method('PUT')

            <!-- Review Type -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Тип відгуку
                </label>
                <select name="review_type" 
                        class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="review" {{ ($review->review_type ?? 'review') === 'review' ? 'selected' : '' }}>Рецензія</option>
                    <option value="opinion" {{ ($review->review_type ?? 'review') === 'opinion' ? 'selected' : '' }}>Відгук</option>
                </select>
            </div>

            <!-- Rating -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Оцінка <span class="text-gray-500 text-xs">(необов'язково для чернетки)</span>
                </label>
                <div class="flex space-x-2" id="rating-stars">
                    @for($i = 1; $i <= 10; $i++)
                    <button type="button" 
                            data-rating="{{ $i }}"
                            class="text-3xl transition-colors hover:scale-110 {{ $review->rating && $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}">
                        ★
                    </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating-input" value="{{ $review->rating ?? '' }}">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Оберіть оцінку від 1 до 10</p>
            </div>

            <!-- Opinion Type -->
            <div class="mb-6" id="opinion-type-container" style="display: {{ ($review->review_type ?? 'review') === 'opinion' ? 'block' : 'none' }};">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Тип думки
                </label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['positive' => 'Позитивна', 'neutral' => 'Нейтральна', 'negative' => 'Негативна'] as $value => $label)
                    <button type="button" 
                            data-opinion="{{ $value }}"
                            class="px-4 py-3 rounded-xl border transition-all {{ ($review->opinion_type ?? 'positive') === $value ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-purple-300' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="opinion_type" id="opinion-type-input" value="{{ $review->opinion_type ?? 'positive' }}">
            </div>

            <!-- Book Type -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Тип книги
                </label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['paper' => 'Паперова', 'electronic' => 'Електронна', 'audio' => 'Аудіо'] as $value => $label)
                    <button type="button" 
                            data-book-type="{{ $value }}"
                            class="px-4 py-3 rounded-xl border transition-all {{ ($review->book_type ?? 'paper') === $value ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-purple-300' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="book_type" id="book-type-input" value="{{ $review->book_type ?? 'paper' }}">
            </div>

            <!-- Language -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Мова рецензії
                </label>
                <select name="language" 
                        class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="uk" {{ ($review->language ?? 'uk') === 'uk' ? 'selected' : '' }}>Українська</option>
                    <option value="en" {{ ($review->language ?? 'uk') === 'en' ? 'selected' : '' }}>English</option>
                    <option value="de" {{ ($review->language ?? 'uk') === 'de' ? 'selected' : '' }}>Німецька</option>
                    <option value="other" {{ ($review->language ?? 'uk') === 'other' ? 'selected' : '' }}>Інша</option>
                </select>
            </div>

            <!-- Content -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Ваша думка <span class="text-red-500">*</span>
                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400 ml-2" id="content-counter-review">
                        (<span id="content-length-review">{{ mb_strlen(old('content', $review->content ?? '')) }}</span> / 
                        <span id="content-max-review">15000</span> символів)
                    </span>
                </label>
                <textarea name="content" 
                          id="content-textarea-review"
                          style="display: none;"
                          required>{{ old('content', $review->content ?? '') }}</textarea>
                <div id="quill-editor-review"></div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    <span id="content-min-review">Мінімум: 800 символів</span>
                </div>
                @error('content')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Spoiler Checkbox -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="contains_spoiler" 
                           value="1"
                           {{ old('contains_spoiler', $review->contains_spoiler ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Містить спойлер</span>
                </label>
            </div>

            <!-- Draft Checkbox -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="is_draft" 
                           value="1"
                           id="is-draft-checkbox"
                           {{ old('is_draft', $review->is_draft ?? true) ? 'checked' : '' }}
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

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 200px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 14px;
        line-height: 1.6;
    }
    
    .ql-toolbar {
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
        border-bottom: none;
    }
    
    .ql-container {
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        border-top: none;
    }
    
    .dark .ql-toolbar {
        background-color: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }
    
    .dark .ql-container {
        background-color: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }
    
    .dark .ql-editor {
        color: #f9fafb;
    }
    
    .dark .ql-stroke {
        stroke: #f9fafb;
    }
    
    .dark .ql-fill {
        fill: #f9fafb;
    }
    
    .dark .ql-picker-label {
        color: #f9fafb;
    }
    
    .dark .ql-picker-options {
        background-color: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }
    
    .dark .ql-picker-item {
        color: #f9fafb;
    }
    
    .dark .ql-picker-item:hover {
        background-color: #4b5563;
    }
    
    #quill-editor-review {
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
(function() {
    let quillReview = null;
    
    // Wait for Quill to be loaded
    function initQuillEditor() {
        if (typeof Quill === 'undefined') {
            console.log('Waiting for Quill to load...');
            setTimeout(initQuillEditor, 100);
            return;
        }
        
        const editorElement = document.getElementById('quill-editor-review');
        if (!editorElement) {
            console.error('Editor element #quill-editor-review not found');
            return;
        }
        
        // Check if already initialized
        if (editorElement.classList.contains('ql-container')) {
            console.log('Quill already initialized');
            quillReview = Quill.find(editorElement);
            return;
        }
        
        // Initialize Quill editor for review content
        const toolbarOptions = [
            ['bold', 'italic', 'underline'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'image'],
            ['clean']
        ];
        
        try {
            quillReview = new Quill('#quill-editor-review', {
                theme: 'snow',
                modules: {
                    toolbar: toolbarOptions
                },
            });
        } catch (error) {
            console.error('Error initializing Quill:', error);
            return;
        }
        
        // Sanitize HTML function (defined inside initQuillEditor to have access to quillReview)
        function sanitizeHTML(html) {
        const temp = document.createElement('div');
        temp.innerHTML = html;
        
        // Remove script tags and event handlers
        const scripts = temp.querySelectorAll('script');
        scripts.forEach(script => script.remove());
        
        // Remove style tags
        const styles = temp.querySelectorAll('style');
        styles.forEach(style => style.remove());
        
        // Remove inline styles
        const allElements = temp.querySelectorAll('*');
        allElements.forEach(el => {
            el.removeAttribute('style');
            el.removeAttribute('onclick');
            el.removeAttribute('onerror');
            el.removeAttribute('onload');
        });
        
        // Only allow safe tags
        const allowedTags = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'a', 'img'];
        const allowedAttributes = {
            'a': ['href', 'title'],
            'img': ['src', 'alt', 'title']
        };
        
        const walker = document.createTreeWalker(
            temp,
            NodeFilter.SHOW_ELEMENT,
            null,
            false
        );
        
        const nodesToRemove = [];
        let node;
        
        while (node = walker.nextNode()) {
            if (!allowedTags.includes(node.tagName.toLowerCase())) {
                nodesToRemove.push(node);
            } else {
                // Remove disallowed attributes
                Array.from(node.attributes).forEach(attr => {
                    const tagName = node.tagName.toLowerCase();
                    if (!allowedAttributes[tagName] || !allowedAttributes[tagName].includes(attr.name)) {
                        node.removeAttribute(attr.name);
                    }
                });
                
                // Validate links
                if (node.tagName.toLowerCase() === 'a') {
                    const href = node.getAttribute('href');
                    if (href && !href.match(/^(https?:\/\/|\/)/)) {
                        node.removeAttribute('href');
                    }
                }
                
                // Validate images
                if (node.tagName.toLowerCase() === 'img') {
                    const src = node.getAttribute('src');
                    if (src && !src.match(/^(https?:\/\/|\/|data:image)/)) {
                        node.remove();
                    }
                }
            }
        }
        
        nodesToRemove.forEach(n => n.remove());
        
        return temp.innerHTML;
    }
    
    // Update hidden textarea when content changes
    quillReview.on('text-change', function() {
        const contentInput = document.getElementById('content-textarea-review');
        if (contentInput) {
            const html = quillReview.root.innerHTML;
            const sanitized = sanitizeHTML(html);
            contentInput.value = sanitized;
        }
        updateCharacterCount();
    });
    
    // Set initial content if exists
    const initialContent = document.getElementById('content-textarea-review').value;
    if (initialContent) {
        quillReview.root.innerHTML = initialContent;
    }
    
    // Update hidden textarea before form submit
    const form = document.getElementById('edit-review-form');
    if (form) {
        form.addEventListener('submit', function() {
            const contentInput = document.getElementById('content-textarea-review');
            if (contentInput) {
                const html = quillReview.root.innerHTML;
                const sanitized = sanitizeHTML(html);
                contentInput.value = sanitized;
            }
        });
    }
        // Rating stars (inside initQuillEditor, but doesn't depend on Quill)
        const ratingStars = document.querySelectorAll('#rating-stars button');
        const ratingInput = document.getElementById('rating-input');
        
        if (ratingStars.length > 0 && ratingInput) {
            ratingStars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    ratingInput.value = rating;
                    
                    ratingStars.forEach((s, index) => {
                        if (index + 1 <= rating) {
                            s.classList.remove('text-gray-300', 'dark:text-gray-600');
                            s.classList.add('text-yellow-400');
                        } else {
                            s.classList.remove('text-yellow-400');
                            s.classList.add('text-gray-300', 'dark:text-gray-600');
                        }
                    });
                });
            });
        }

        // Opinion type buttons (inside initQuillEditor, but doesn't depend on Quill)
        const opinionButtons = document.querySelectorAll('[data-opinion]');
        const opinionInput = document.getElementById('opinion-type-input');
        
        if (opinionButtons.length > 0 && opinionInput) {
            opinionButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const opinion = this.getAttribute('data-opinion');
                    opinionInput.value = opinion;
                    
                    opinionButtons.forEach(b => {
                        b.classList.remove('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20', 'text-purple-700', 'dark:text-purple-300');
                        b.classList.add('border-gray-300', 'dark:border-gray-600', 'text-gray-700', 'dark:text-gray-300');
                    });
                    
                    this.classList.remove('border-gray-300', 'dark:border-gray-600', 'text-gray-700', 'dark:text-gray-300');
                    this.classList.add('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20', 'text-purple-700', 'dark:text-purple-300');
                });
            });
        }

        // Book type buttons (inside initQuillEditor, but doesn't depend on Quill)
        const bookTypeButtons = document.querySelectorAll('[data-book-type]');
        const bookTypeInput = document.getElementById('book-type-input');
        
        if (bookTypeButtons.length > 0 && bookTypeInput) {
            bookTypeButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const bookType = this.getAttribute('data-book-type');
                    bookTypeInput.value = bookType;
                    
                    bookTypeButtons.forEach(b => {
                        b.classList.remove('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20', 'text-purple-700', 'dark:text-purple-300');
                        b.classList.add('border-gray-300', 'dark:border-gray-600', 'text-gray-700', 'dark:text-gray-300');
                    });
                    
                    this.classList.remove('border-gray-300', 'dark:border-gray-600', 'text-gray-700', 'dark:text-gray-300');
                    this.classList.add('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20', 'text-purple-700', 'dark:text-purple-300');
                });
            });
        }

        // Review type select - show/hide opinion type and update character limits
        const reviewTypeSelect = document.querySelector('select[name="review_type"]');
        const opinionTypeContainer = document.getElementById('opinion-type-container');
        const contentLength = document.getElementById('content-length-review');
        const contentMax = document.getElementById('content-max-review');
        const contentMin = document.getElementById('content-min-review');
        
        function updateCharacterLimits() {
            if (!reviewTypeSelect || !contentMax || !contentMin) return;
            const reviewType = reviewTypeSelect.value;
            if (reviewType === 'opinion') {
                contentMax.textContent = '1000';
                contentMin.textContent = 'Мінімум: 100 символів';
            } else {
                contentMax.textContent = '15000';
                contentMin.textContent = 'Мінімум: 800 символів';
            }
            updateCharacterCount();
        }
        
        function updateCharacterCount() {
            if (!quillReview || !contentLength || !reviewTypeSelect) return;
            const text = quillReview.getText();
            const length = text.length;
            contentLength.textContent = length;
            const reviewType = reviewTypeSelect.value;
            const max = reviewType === 'opinion' ? 1000 : 15000;
            const min = reviewType === 'opinion' ? 100 : 800;
            
            if (length < min) {
                contentLength.classList.add('text-red-500', 'font-semibold');
                contentLength.classList.remove('text-gray-500');
            } else if (length > max) {
                contentLength.classList.add('text-red-500', 'font-semibold');
                contentLength.classList.remove('text-gray-500');
            } else {
                contentLength.classList.remove('text-red-500', 'font-semibold');
                contentLength.classList.add('text-gray-500');
            }
        }
        
        if (reviewTypeSelect && opinionTypeContainer) {
            reviewTypeSelect.addEventListener('change', function() {
                if (this.value === 'opinion') {
                    opinionTypeContainer.style.display = 'block';
                } else {
                    opinionTypeContainer.style.display = 'none';
                }
                updateCharacterLimits();
            });
        }
        
        // Character counter for content (using Quill)
        if (quillReview) {
            updateCharacterCount();
            quillReview.on('text-change', updateCharacterCount);
        }
    }
    
    // Start initialization when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQuillEditor);
    } else {
        initQuillEditor();
    }
    
    // Form submit handler (outside initQuillEditor, as it doesn't depend on Quill)
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('edit-review-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const submitButton = document.activeElement;
                const isDraftCheckbox = document.getElementById('is-draft-checkbox');
                
                if (submitButton && submitButton.name === 'action') {
                    if (submitButton.value === 'publish') {
                        // При публикации снимаем флаг черновика
                        if (isDraftCheckbox) isDraftCheckbox.checked = false;
                    } else if (submitButton.value === 'save') {
                        // При сохранении как черновик ставим флаг
                        if (isDraftCheckbox) isDraftCheckbox.checked = true;
                    }
                }
            });
        }
    });
})();
</script>
@endpush

