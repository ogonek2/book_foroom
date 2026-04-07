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
    <div>
        <div>
            <div class="flex gap-4 flex-col md:flex-row lg:flex-row">
                <!-- Sticky Sidebar with Book Info -->
                @if ($book)
                    <div class="md:max-w-[220px] lg:max-w-[220px] w-full">
                        <div class="sticky top-8">
                            <div class="flex gap-4 flex-row md:flex-col lg:flex-col">
                                <!-- Book Cover -->
                                <div class="relative w-full max-w-[120px] md:max-w-full overflow-hidden rounded-xl">
                                    <img src="{{ $book->cover_image_display }}" data-fallback="bookCover"
                                        alt="{{ $book->title }}" class="w-full object-cover" style="aspect-ratio: 2 / 3;">
                                </div>

                                <!-- Book Info -->
                                <div class="pb-4 w-full">
                                    <a href="{{ route('books.show', $book->slug) }}" class="block group">
                                        <h3
                                            class="text-lg font-bold text-gray-900 dark:text-white mb-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors line-clamp-2">
                                            {{ $book->title }}
                                        </h3>
                                    </a>

                                    @if ($book->author)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                            <span class="font-medium">Автор:</span> {{ $book->author }}
                                        </p>
                                    @endif

                                    @if ($book->rating)
                                        <div class="flex items-center mb-3">
                                            <div class="flex items-center">
                                                <i class="fa-solid fa-star text-yellow-400"></i>
                                            </div>
                                            <span class="ml-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                {{ number_format($book->rating, 1) }}/10
                                            </span>
                                        </div>
                                    @endif

                                    <div class="flex-1">
                                        <a href="{{ route('books.show', $book->slug) }}"
                                            class="w-full inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                                            <i class="fas fa-book-open mr-2"></i> До книги
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- Main Content -->
                <div class="w-full">
                    <!-- Header -->
                    <div class="mb-6">
                        <div class="flex flex-col mb-4">
                            <a href="{{ route('profile.show', ['tab' => 'drafts']) }}" class="text-sm text-indigo-600 dark:text-indigo-400 mb-4">
                                <i class='fa-solid fa-arrow-left'></i>
                                Назад до чернеток
                            </a>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Редагувати чернетку рецензії
                                </h2>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="review-glass rounded-2xl p-4 sm:p-5">
                        <form action="{{ route('books.reviews.update', [$book, $review]) }}" method="POST"
                            id="edit-review-form">
                            @csrf
                            @method('PUT')

                            <!-- Review Type -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Тип відгуку
                                </label>
                                <select name="review_type"
                                    class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="review"
                                        {{ ($review->review_type ?? 'review') === 'review' ? 'selected' : '' }}>Рецензія
                                    </option>
                                    <option value="opinion"
                                        {{ ($review->review_type ?? 'review') === 'opinion' ? 'selected' : '' }}>Відгук
                                    </option>
                                </select>
                            </div>

                            <!-- Rating -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Оцінка
                                </label>
                                <div class="flex flex-wrap gap-2 items-center" id="rating-stars">
                                    @for ($i = 1; $i <= 10; $i++)
                                        <button type="button" data-rating="{{ $i }}"
                                            class="text-3xl transition-colors hover:scale-110 {{ $review->rating && $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}">
                                            ★
                                        </button>
                                    @endfor
                                    @if ($review->rating)
                                        <button type="button" id="clear-rating"
                                            class="ml-2 px-3 py-1 text-sm text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                            Очистити
                                        </button>
                                    @endif
                                </div>
                                <input type="hidden" name="rating" id="rating-input" value="{{ $review->rating ?? '' }}">
                                <input type="hidden" name="rating_cleared" id="rating-cleared-input" value="0">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Оберіть оцінку від 1 до 10 або
                                    залиште порожнім</p>
                            </div>

                            <!-- Opinion Type -->
                            <div class="mb-6" id="opinion-type-container">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Тип думки
                                </label>
                                <div class="grid grid-cols-2 lg:grid-cols-3 gap-1">
                                    @foreach (['positive' => 'Позитивна', 'neutral' => 'Нейтральна', 'negative' => 'Негативна'] as $value => $label)
                                        <button type="button" data-opinion="{{ $value }}"
                                            class="px-4 py-3 rounded-xl border transition-all {{ ($review->opinion_type ?? 'positive') === $value ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-purple-300' }}">
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="opinion_type" id="opinion-type-input"
                                    value="{{ $review->opinion_type ?? 'positive' }}">
                            </div>

                            <!-- Book Type -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Тип книги
                                </label>
                                <div class="grid grid-cols-2 lg:grid-cols-3 gap-1">
                                    @foreach (['paper' => 'Паперова', 'electronic' => 'Електронна', 'audio' => 'Аудіо'] as $value => $label)
                                        <button type="button" data-book-type="{{ $value }}"
                                            class="px-4 py-3 rounded-xl border transition-all {{ ($review->book_type ?? 'paper') === $value ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-purple-300' }}">
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="book_type" id="book-type-input"
                                    value="{{ $review->book_type ?? 'paper' }}">
                            </div>

                            <!-- Language -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Мова читання
                                </label>
                                <input type="hidden" name="language" id="language-input"
                                    value="{{ $review->language ?? 'uk' }}">
                                <button type="button" id="language-picker-button"
                                    class="review-input text-left flex items-center justify-between">
                                    <span id="language-picker-label">Оберіть мову</span>
                                    <i class="fas fa-chevron-down text-slate-400"></i>
                                </button>
                            </div>

                            <!-- Content -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Ваша думка <span class="text-red-500">*</span>
                                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400 ml-2"
                                        id="content-counter-review">
                                        (<span
                                            id="content-length-review">{{ mb_strlen(old('content', $review->content ?? '')) }}</span>
                                        /
                                        <span id="content-max-review">15000</span> символів)
                                    </span>
                                </label>
                                <textarea name="content" id="content-textarea-review" required>{{ old('content', $review->content ?? '') }}</textarea>
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
                                    <input type="checkbox" name="contains_spoiler" value="1"
                                        {{ old('contains_spoiler', $review->contains_spoiler ?? false) ? 'checked' : '' }}
                                        class="w-4 h-4 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 dark:focus:ring-purple-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Містить спойлер</span>
                                </label>
                            </div>

                            <input type="hidden" name="is_draft" value="{{ old('is_draft', $review->is_draft ?? true) ? '1' : '0' }}" id="is-draft-checkbox">

                            <!-- Submit Buttons -->
                            <div class="flex flex-wrap gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="inline-flex bg-slate-100 dark:bg-slate-800 rounded-xl p-1 gap-1">
                                    <button type="button" data-mode="draft"
                                        class="flex items-center justify-center gap-2 px-5 py-3 rounded-lg text-sm font-medium transition-all duration-200 action-tab active bg-white dark:bg-slate-700 text-orange-500 dark:text-orange-400 shadow-sm">
                                        <span>Чернетка</span>
                                    </button>
                                    <button type="button" data-mode="publish"
                                        class="flex items-center justify-center gap-2 px-5 py-3 rounded-lg text-sm font-medium transition-all duration-200 action-tab text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                                        <span>Опублікувати</span>
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('profile.show', ['tab' => 'drafts']) }}"
                                        class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        Скасувати
                                    </a>
                                    <button type="submit" id="submit-btn"
                                        class="px-6 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-orange-500 dark:text-orange-400 rounded-xl font-medium transition-colors">
                                        Зберегти чернетку
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .review-glass {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(148, 163, 184, 0.25);
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12);
        }

        .dark .review-glass {
            background: rgba(11, 18, 37, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.10);
            box-shadow: 0 16px 40px rgba(2, 6, 23, 0.45);
        }

        .ql-editor {
            min-height: 200px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }

        .ql-toolbar {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
            border-bottom: none;
            background-color: rgba(248, 250, 252, 0.95);
        }

        .ql-container {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-top: none;
            border-left: none;
            border-right: none;
            border-bottom: none;
            background-color: transparent;
        }

        .ql-editor {
            color: rgb(15 23 42);
        }

        .ql-toolbar .ql-stroke {
            stroke: rgb(71 85 105);
        }

        .ql-toolbar .ql-fill {
            fill: rgb(71 85 105);
        }

        .ql-toolbar .ql-picker-label {
            color: rgb(71 85 105);
        }

        .ql-toolbar .ql-picker-options {
            background-color: rgb(255 255 255 / 0.98);
            border-color: rgb(203 213 225 / 0.9);
            color: rgb(15 23 42);
        }

        .ql-toolbar .ql-picker-item {
            color: rgb(15 23 42);
        }

        .ql-toolbar button:hover,
        .ql-toolbar .ql-picker-label:hover,
        .ql-toolbar .ql-picker-item:hover {
            color: rgb(30 41 59);
            background-color: rgb(226 232 240 / 0.7);
        }

        .dark .ql-toolbar {
            background-color: rgba(30, 41, 59, 0.72);
            border-color: transparent;
            color: #f9fafb;
        }

        .dark .ql-container {
            background-color: transparent;
            border-color: transparent;
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
            border: none;
            border-radius: 0;
        }

        .review-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid rgb(71 85 105 / 0.45);
            background: rgb(255 255 255 / 0.95);
            color: rgb(15 23 42);
            font-size: 0.875rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .dark .review-input {
            background: rgb(15 23 42 / 0.55);
            color: rgb(241 245 249);
            border-color: rgb(71 85 105 / 0.45);
        }

        .review-input:focus {
            border-color: rgb(99 102 241 / 0.75);
            box-shadow: 0 0 0 3px rgb(99 102 241 / 0.15);
        }

        .lang-modal-backdrop {
            background: rgba(2, 6, 23, 0.55);
            backdrop-filter: blur(4px);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/iso-639-1@3.1.5/build/index.js"></script>
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
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    ['link'],
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
                    const allowedTags = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'a'];
                    const allowedAttributes = {
                        'a': ['href', 'title']
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
                                if (!allowedAttributes[tagName] || !allowedAttributes[tagName].includes(attr
                                        .name)) {
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

                            // Remove images (not allowed in reviews)
                            if (node.tagName.toLowerCase() === 'img') {
                                node.remove();
                            }
                        }
                    }

                    nodesToRemove.forEach(n => n.remove());

                    return temp.innerHTML;
                }

                // Hide original textarea once Quill is ready (progressive enhancement)
                const originalTextarea = document.getElementById('content-textarea-review');
                if (originalTextarea) {
                    originalTextarea.style.display = 'none';
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
                const ratingStars = document.querySelectorAll('#rating-stars button[data-rating]');
                const ratingInput = document.getElementById('rating-input');
                const clearRatingBtn = document.getElementById('clear-rating');

                if (ratingStars.length > 0 && ratingInput) {
                    ratingStars.forEach(star => {
                        star.addEventListener('click', function() {
                            const rating = parseInt(this.getAttribute('data-rating'));
                            ratingInput.value = rating;

                            // Показуємо кнопку очищення
                            if (clearRatingBtn) {
                                clearRatingBtn.style.display = 'inline-block';
                            }

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

                // Кнопка очищення оцінки
                if (clearRatingBtn) {
                    const ratingClearedInput = document.getElementById('rating-cleared-input');
                    clearRatingBtn.addEventListener('click', function() {
                        ratingInput.value = '';
                        if (ratingClearedInput) {
                            ratingClearedInput.value = '1';
                        }

                        // Ховаємо кнопку очищення
                        this.style.display = 'none';

                        // Скидаємо всі зірки
                        ratingStars.forEach(star => {
                            star.classList.remove('text-yellow-400');
                            star.classList.add('text-gray-300', 'dark:text-gray-600');
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
                                b.classList.remove('border-purple-500', 'bg-purple-50',
                                    'dark:bg-purple-900/20', 'text-purple-700',
                                    'dark:text-purple-300');
                                b.classList.add('border-gray-300', 'dark:border-gray-600',
                                    'text-gray-700', 'dark:text-gray-300');
                            });

                            this.classList.remove('border-gray-300', 'dark:border-gray-600',
                                'text-gray-700', 'dark:text-gray-300');
                            this.classList.add('border-purple-500', 'bg-purple-50',
                                'dark:bg-purple-900/20', 'text-purple-700', 'dark:text-purple-300');
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
                                b.classList.remove('border-purple-500', 'bg-purple-50',
                                    'dark:bg-purple-900/20', 'text-purple-700',
                                    'dark:text-purple-300');
                                b.classList.add('border-gray-300', 'dark:border-gray-600',
                                    'text-gray-700', 'dark:text-gray-300');
                            });

                            this.classList.remove('border-gray-300', 'dark:border-gray-600',
                                'text-gray-700', 'dark:text-gray-300');
                            this.classList.add('border-purple-500', 'bg-purple-50',
                                'dark:bg-purple-900/20', 'text-purple-700', 'dark:text-purple-300');
                        });
                    });
                }

                // Review type select - update character limits (opinion type доступний для всіх)
                const reviewTypeSelect = document.querySelector('select[name="review_type"]');
                const contentLength = document.getElementById('content-length-review');
                const contentMax = document.getElementById('content-max-review');
                const contentMin = document.getElementById('content-min-review');

                function updateCharacterLimits() {
                    if (!reviewTypeSelect || !contentMax || !contentMin) return;
                    const reviewType = reviewTypeSelect.value;
                    if (reviewType === 'opinion') {
                        contentMax.textContent = '1000';
                        contentMin.textContent = 'Мінімум: 25 символів';
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
                    const min = reviewType === 'opinion' ? 25 : 800;

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

                if (reviewTypeSelect) {
                    reviewTypeSelect.addEventListener('change', function() {
                        updateCharacterLimits();
                    });

                    // Ініціалізуємо ліміти при завантаженні сторінки
                    updateCharacterLimits();
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
                    const isDraftInput = document.getElementById('is-draft-checkbox');
                    const submitBtn = document.getElementById('submit-btn');
                    const actionTabs = form.querySelectorAll('.action-tab');
                    let actionMode = isDraftInput && isDraftInput.value === '0' ? 'publish' : 'draft';

                    const syncActionUi = () => {
                        actionTabs.forEach((tab) => {
                            if (tab.dataset.mode === actionMode) {
                                tab.classList.add('active');
                                if (actionMode === 'draft') {
                                    tab.classList.add('bg-white', 'dark:bg-slate-700', 'text-orange-500', 'dark:text-orange-400', 'shadow-sm');
                                    tab.classList.remove('bg-gradient-to-r', 'from-indigo-500', 'to-purple-600', 'text-white');
                                } else {
                                    tab.classList.add('bg-gradient-to-r', 'from-indigo-500', 'to-purple-600', 'text-white', 'shadow-md');
                                    tab.classList.remove('bg-white', 'dark:bg-slate-700', 'text-orange-500', 'dark:text-orange-400');
                                }
                            } else {
                                tab.classList.remove('active', 'bg-white', 'dark:bg-slate-700', 'text-orange-500', 'dark:text-orange-400', 'shadow-sm', 'bg-gradient-to-r', 'from-indigo-500', 'to-purple-600', 'text-white', 'shadow-md');
                                tab.classList.add('text-slate-600', 'dark:text-slate-400');
                            }
                        });

                        if (submitBtn) {
                            if (actionMode === 'draft') {
                                submitBtn.textContent = 'Зберегти чернетку';
                                submitBtn.classList.remove('bg-gradient-to-r', 'from-indigo-500', 'to-purple-600', 'hover:from-indigo-600', 'hover:to-purple-700', 'text-white');
                                submitBtn.classList.add('bg-slate-100', 'dark:bg-slate-800', 'hover:bg-slate-200', 'dark:hover:bg-slate-700', 'text-orange-500', 'dark:text-orange-400');
                            } else {
                                submitBtn.textContent = 'Опублікувати';
                                submitBtn.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'hover:bg-slate-200', 'dark:hover:bg-slate-700', 'text-orange-500', 'dark:text-orange-400');
                                submitBtn.classList.add('bg-gradient-to-r', 'from-indigo-500', 'to-purple-600', 'hover:from-indigo-600', 'hover:to-purple-700', 'text-white');
                            }
                        }

                        if (isDraftInput) {
                            isDraftInput.value = actionMode === 'draft' ? '1' : '0';
                        }
                    };

                    actionTabs.forEach((tab) => {
                        tab.addEventListener('click', () => {
                            actionMode = tab.dataset.mode === 'publish' ? 'publish' : 'draft';
                            syncActionUi();
                        });
                    });

                    syncActionUi();
                }
            });
        })();
        // Кастомний вибір мови (popup + пошук + всі ISO мови)
        const languageInput = document.getElementById('language-input');
        const languageButton = document.getElementById('language-picker-button');
        const languageLabel = document.getElementById('language-picker-label');
        if (languageInput && languageButton && languageLabel) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-[220] hidden';
            modal.innerHTML = `
            <div class="absolute inset-0 lang-modal-backdrop"></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-xl review-glass rounded-2xl p-4">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="text-sm font-bold text-slate-900 dark:text-white">Оберіть мову читання</div>
                        <button type="button" id="lang-modal-close" class="review-input !w-auto !py-1.5 !px-2.5">✕</button>
                    </div>
                    <input id="lang-modal-search" type="text" class="review-input" placeholder="Пошук мови...">
                    <div id="lang-modal-list" class="mt-3 max-h-[50vh] overflow-auto space-y-1 pr-1"></div>
                </div>
            </div>
        `;
            document.body.appendChild(modal);

            const closeBtn = modal.querySelector('#lang-modal-close');
            const searchInput = modal.querySelector('#lang-modal-search');
            const list = modal.querySelector('#lang-modal-list');
            const backdrop = modal.querySelector('.lang-modal-backdrop');

            const options = (window.ISO6391 && window.ISO6391.getAllCodes) ?
                window.ISO6391.getAllCodes()
                .filter((code) => window.ISO6391.validate(code))
                .map((code) => ({
                    code,
                    name: window.ISO6391.getNativeName(code) || window.ISO6391.getName(code) || code.toUpperCase(),
                }))
                .sort((a, b) => a.name.localeCompare(b.name, 'uk')) :
                [{
                    code: 'uk',
                    name: 'Українська'
                }, {
                    code: 'en',
                    name: 'English'
                }];

            const getLabel = () => {
                const found = options.find((l) => l.code === languageInput.value);
                return found ? found.name : 'Оберіть мову';
            };

            const renderList = (query = '') => {
                const q = String(query || '').toLowerCase().trim();
                const filtered = q ?
                    options.filter((lang) => lang.name.toLowerCase().includes(q) || lang.code.toLowerCase().includes(
                    q)) :
                    options;
                list.innerHTML = filtered.map((lang) => `
                <button type="button" data-code="${lang.code}" class="w-full text-left rounded-lg px-3 py-2 text-sm transition ${languageInput.value === lang.code ? 'bg-indigo-500 text-white' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/10'}">${lang.name}</button>
            `).join('');
                list.querySelectorAll('button[data-code]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        languageInput.value = btn.getAttribute('data-code');
                        languageLabel.textContent = getLabel();
                        modal.classList.add('hidden');
                        searchInput.value = '';
                    });
                });
            };

            languageLabel.textContent = getLabel();
            renderList();
            languageButton.addEventListener('click', () => {
                modal.classList.remove('hidden');
                searchInput.focus();
            });
            backdrop.addEventListener('click', () => modal.classList.add('hidden'));
            closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
            searchInput.addEventListener('input', () => renderList(searchInput.value));
        }
    </script>
@endpush
