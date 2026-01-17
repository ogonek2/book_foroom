@extends('layouts.app')

@section('title', 'Редагувати обговорення - Книжковий форум')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
    <style>
        /* Reddit-style Editor Styles */
        .tiptap-editor {
            min-height: 7rem;
            max-height: calc(100vh - 12rem);
            background: white;
            color: rgb(15 23 42);
            font-size: 1rem;
            line-height: 1.5;
            outline: none;
            resize: vertical;
            overflow-y: auto;
            box-sizing: border-box;
            user-select: text;
            white-space: pre-wrap;
            word-break: break-word;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .tiptap-editor p.is-editor-empty:first-child::before {
            content: attr(data-placeholder);
            color: rgb(148 163 184);
            pointer-events: none;
            float: left;
            height: 0;
        }
        .ProseMirror {
            padding: 0.75rem 1rem;
        }

        .dark .tiptap-editor p.is-editor-empty:first-child::before {
            color: rgb(100 116 139);
        }

        /* Placeholder styles for TipTap */
        .tiptap-editor .is-empty::before {
            content: attr(data-placeholder);
            float: left;
            color: rgb(148 163 184);
            pointer-events: none;
            height: 0;
        }

        .dark .tiptap-editor .is-empty::before {
            color: rgb(100 116 139);
        }

        .dark .tiptap-editor {
            border-color: rgb(51 65 85);
            background: rgb(30 41 59);
            color: rgb(241 245 249);
        }

        .dark .tiptap-editor:empty:before {
            color: rgb(100 116 139);
        }

        .tiptap-editor:focus {
            border-color: rgb(99 102 241);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .dark .tiptap-editor:focus {
            border-color: rgb(99 102 241);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        /* Reddit-style paragraph */
        .tiptap-editor p {
            margin: 0.5rem 0;
            padding: 0;
            line-height: 1.5;
        }

        .tiptap-editor p:first-child {
            margin-top: 0;
        }

        .tiptap-editor p:last-child {
            margin-bottom: 0;
        }

        /* Lists */
        .tiptap-editor ul,
        .tiptap-editor ol {
            padding-left: 1.5rem;
            margin: 0.5rem 0;
        }

        .tiptap-editor ul {
            list-style-type: disc;
        }

        .tiptap-editor ol {
            list-style-type: decimal;
        }

        .tiptap-editor li {
            margin: 0.25rem 0;
        }

        .tiptap-editor li p {
            margin: 0;
        }

        /* Mention styles */
        .tiptap-editor .mention {
            background: linear-gradient(to right, rgb(99 102 241), rgb(139 92 246));
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
        }

        /* Hashtag styles */
        .tiptap-editor .hashtag {
            color: rgb(99 102 241);
            font-weight: 600;
            text-decoration: none;
        }

        .dark .tiptap-editor .hashtag {
            color: rgb(129 140 248);
        }

        .tiptap-editor .hashtag:hover {
            text-decoration: underline;
        }

        /* Reddit-style Toolbar */
        .tiptap-toolbar {
            display: flex;
            gap: 0.25rem;
            padding: 0.5rem;
            border-bottom: none;
            border-radius: 0.5rem 0.5rem 0 0;
            background: rgb(248 250 252);
            flex-wrap: wrap;
            align-items: center;
        }

        .dark .tiptap-toolbar {
            border-color: rgb(51 65 85);
            background: rgb(30 41 59);
        }

        .tiptap-toolbar button {
            padding: 0.375rem 0.5rem;
            border: none;
            background: transparent;
            color: rgb(100 116 139);
            cursor: pointer;
            border-radius: 0.25rem;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
        }

        .dark .tiptap-toolbar button {
            color: rgb(148 163 184);
        }

        .tiptap-toolbar button:hover {
            background: rgb(241 245 249);
            color: rgb(15 23 42);
        }

        .dark .tiptap-toolbar button:hover {
            background: rgb(51 65 85);
            color: rgb(241 245 249);
        }

        .tiptap-toolbar button.is-active {
            background: rgb(226 232 240);
            color: rgb(15 23 42);
        }

        .dark .tiptap-toolbar button.is-active {
            background: rgb(51 65 85);
            color: rgb(241 245 249);
        }

        .tiptap-toolbar .divider {
            width: 1px;
            height: 1.5rem;
            background: rgb(226 232 240);
            margin: 0 0.25rem;
        }

        .dark .tiptap-toolbar .divider {
            background: rgb(51 65 85);
        }

        /* Mention dropdown */
        .mention-dropdown {
            position: absolute;
            background: white;
            border: 1px solid rgb(226 232 240);
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            min-width: 200px;
        }

        .dark .mention-dropdown {
            background: rgb(30 41 59);
            border-color: rgb(51 65 85);
        }

        .mention-item {
            padding: 0.5rem 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .mention-item:hover,
        .mention-item.is-selected {
            background: rgb(241 245 249);
        }

        .dark .mention-item:hover,
        .dark .mention-item.is-selected {
            background: rgb(51 65 85);
        }

        .mention-item-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(to right, rgb(99 102 241), rgb(139 92 246));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .mention-item-info {
            flex: 1;
        }

        .mention-item-name {
            font-weight: 500;
            color: rgb(15 23 42);
        }

        .dark .mention-item-name {
            color: rgb(241 245 249);
        }

        .mention-item-username {
            font-size: 0.875rem;
            color: rgb(100 116 139);
        }

        .dark .mention-item-username {
            color: rgb(148 163 184);
        }
    </style>
@endpush

@section('main')
    <div class="w-full space-y-8">
        <!-- Breadcrumb Navigation -->
        <nav class="text-sm text-slate-500 dark:text-slate-400">
            <ol class="flex flex-wrap gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">Головна</a></li>
                <li>/</li>
                <li><a href="{{ route('discussions.index') }}"
                        class="hover:text-indigo-600 dark:hover:text-indigo-400">Обговорення</a></li>
                <li>/</li>
                <li><a href="{{ route('discussions.show', $discussion) }}"
                        class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ Str::limit($discussion->title, 30) }}</a></li>
                <li>/</li>
                <li class="font-semibold text-slate-900 dark:text-white">Редагувати обговорення</li>
            </ol>
        </nav>

        <div class="w-full">
            <!-- Form Card -->
            <div>
                <!-- Form Header -->
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Редагувати обговорення</h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Внесіть зміни до вашого обговорення</p>
        </div>

                <div>
                    <form id="discussion-form" action="{{ route('discussions.update', $discussion) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div>
                            <label for="title" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                                Заголовок обговорення <span class="text-red-500">*</span>
                                <span class="text-xs font-normal text-slate-500 dark:text-slate-400 ml-2"
                                    id="title-counter">
                                    (<span id="title-length">{{ mb_strlen(old('title', $discussion->title)) }}</span> /
                                    <span id="title-max">200</span> символів)
                                </span>
                            </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $discussion->title) }}"
                                placeholder="Введіть заголовок обговорення..." maxlength="200"
                                class="w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('title') border-red-500 @enderror"
                        required>
                    @error('title')
                        <p class="mt-2 text-red-500 dark:text-red-400 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                        <!-- Content Editor -->
                <div>
                            <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">
                                Зміст обговорення <span class="text-red-500">*</span>
                                <span class="text-xs font-normal text-slate-500 dark:text-slate-400 ml-2"
                                    id="content-counter">
                                    (<span id="content-length">0</span> /
                                    <span id="content-max">40000</span> символів)
                                </span>
                            </label>

                            <!-- TipTap Editor Component - uses contenteditable div, NOT textarea -->
                            <div id="discussion-editor-app">
                                <discussion-editor 
                                    v-model="editorContent"
                                    :model-value="editorContent"
                                    :placeholder="'Опишіть тему для обговорення. Використовуйте @ для згадки користувачів та # для хештегів...'"
                                    @input="updateContent" 
                                    @change="updateContent"
                                    @update:modelValue="updateContent">
                                </discussion-editor>
                            </div>

                            <!-- Single hidden textarea for form submission -->
                            <textarea id="content" name="content"
                                style="display: none !important; visibility: hidden !important; position: absolute !important; left: -9999px !important; width: 0 !important; height: 0 !important; opacity: 0 !important; pointer-events: none !important;">{{ old('content', $discussion->content) }}</textarea>

                            <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                Мінімум: 300 символів, Максимум: 40000 символів. Використовуйте <strong>@username</strong>
                                для згадки користувачів та <strong>#hashtag</strong> для тегів.
                            </div>

                    @error('content')
                        <p class="mt-2 text-red-500 dark:text-red-400 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                        <!-- Guidelines -->
                        <div
                            class="bg-blue-50/80 dark:bg-blue-900/20 border border-blue-200/60 dark:border-blue-800/60 rounded-xl p-4">
                            <h4 class="text-blue-700 dark:text-blue-300 font-medium mb-2 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i>
                                Правила редагування обговорень
                    </h4>
                            <ul class="text-blue-600 dark:text-blue-400 text-sm space-y-1">
                                <li>• Будьте ввічливими і уважні до інших учасників</li>
                                <li>• Створюйте цікаві і змістовні обговорення</li>
                                <li>• Використовуйте зрозумілі і інформативні заголовки</li>
                                <li>• Використовуйте @ для згадки користувачів та # для тегів</li>
                            </ul>
                    </div>

                        <!-- Close Discussion Toggle -->
                        <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700">
                            <div class="flex-1">
                                <label for="is_closed" class="block text-sm font-semibold text-slate-900 dark:text-white mb-1">
                                    Статус обговорення
                                </label>
                                <p class="text-xs text-slate-600 dark:text-slate-400">
                                    Закрите обговорення не дозволяє додавати нові відповіді
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_closed" id="is_closed" value="1" {{ old('is_closed', $discussion->is_closed) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                                <span class="ml-3 text-sm font-medium text-slate-900 dark:text-white" id="is_closed_label">
                                    {{ old('is_closed', $discussion->is_closed) ? 'Закрите' : 'Відкрите' }}
                                </span>
                            </label>
                        </div>

                        <!-- Form Actions -->
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                            <!-- Tab Switch -->
                            <div class="inline-flex bg-slate-100 dark:bg-slate-800 rounded-xl p-1 gap-1 mb-4">
                                <button type="button" id="action-draft"
                                    class="flex items-center justify-center gap-2 px-5 py-3 rounded-lg text-sm font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed action-tab"
                                    data-mode="draft">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    <span>Чернетка</span>
                                </button>
                                <button type="button" id="action-publish"
                                    class="flex items-center justify-center gap-2 px-5 py-3 rounded-lg text-sm font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed action-tab active"
                                    data-mode="publish">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Опублікувати</span>
                                </button>
                </div>

                            <!-- Submit Button -->
                            <button type="submit" id="submit-btn"
                                class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-200 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                                <span>Оновити обговорення</span>
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Set initial content as JavaScript variable before DOMContentLoaded
            window.discussionInitialContent = @json(old('content', $discussion->content));
            
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof window.Vue === 'undefined') {
                    console.error('Vue is not loaded');
                    return;
                }

                const contentInput = document.getElementById('content');
                let actionMode = 'publish';

                // Remove any duplicate textareas (TipTap should only use contenteditable)
                const allTextareas = document.querySelectorAll('textarea[name="content"]');
                if (allTextareas.length > 1) {
                    // Keep only the first one (our hidden form textarea)
                    for (let i = 1; i < allTextareas.length; i++) {
                        allTextareas[i].remove();
                    }
                }

                // Handle is_closed toggle
                const isClosedToggle = document.getElementById('is_closed');
                const isClosedLabel = document.getElementById('is_closed_label');
                if (isClosedToggle && isClosedLabel) {
                    isClosedToggle.addEventListener('change', function() {
                        isClosedLabel.textContent = this.checked ? 'Закрите' : 'Відкрите';
                    });
                }

                // Get initial content from discussion - try multiple sources
                let initialContent = '';
                
                // First, try to get from window variable (most reliable for HTML)
                if (window.discussionInitialContent) {
                    initialContent = window.discussionInitialContent;
                }
                
                // Fallback to textarea
                if (!initialContent && contentInput) {
                    initialContent = contentInput.value || '';
                }
                
                // Log for debugging
                console.log('Initial content:', initialContent ? 'Content found (' + initialContent.length + ' chars)' : 'Empty');

                // Initialize Vue app for editor
                const editorApp = new Vue({
                    el: '#discussion-editor-app',
                    data: {
                        editorContent: initialContent,
                    },
                    methods: {
                        updateContent(html) {
                            this.editorContent = html;
                            if (contentInput) {
                                contentInput.value = html;
                            }
                            updateContentCounter();
                        },
                    },
                });

                // Wait for Vue and editor to be fully initialized, then ensure content is set
                setTimeout(() => {
                    const editorComponent = editorApp.$children.find(child => child.$options.name === 'DiscussionEditor');
                    if (editorComponent && editorComponent.editor) {
                        const currentContent = editorComponent.editor.getHTML();
                        // If editor is empty but we have initial content, set it
                        if (initialContent && (currentContent === '<p></p>' || currentContent.trim() === '' || currentContent === '')) {
                            console.log('Setting initial content to editor');
                            editorComponent.editor.commands.setContent(initialContent, false);
                            editorApp.editorContent = initialContent;
                    if (contentInput) {
                                contentInput.value = initialContent;
                            }
                            updateContentCounter();
                        }
                    }
                }, 800);

                // Update content counter - get text from TipTap editor
                function updateContentCounter() {
                    // Wait for editor to be ready
                    setTimeout(() => {
                        const editorElement = document.querySelector('.tiptap-editor');
                        if (!editorElement) return;

                        // Get plain text from editor
                        const text = editorElement.innerText || editorElement.textContent || '';
                        const length = text.length;
                        const lengthEl = document.getElementById('content-length');

                        if (lengthEl) {
                            lengthEl.textContent = length;
                            const min = 300;
                            const max = 40000;

                            if (length < min || length > max) {
                                lengthEl.classList.add('text-red-500', 'font-semibold');
                                lengthEl.classList.remove('text-slate-500', 'dark:text-slate-400');
                            } else {
                                lengthEl.classList.remove('text-red-500', 'font-semibold');
                                lengthEl.classList.add('text-slate-500', 'dark:text-slate-400');
                            }
                        }
                    }, 100);
                }

                // Listen for editor updates
                let updateTimeout;
                document.addEventListener('input', (e) => {
                    if (e.target.closest('.tiptap-editor')) {
                        clearTimeout(updateTimeout);
                        updateTimeout = setTimeout(() => {
                            updateContentCounter();
                        }, 100);
                    }
                });

                // Initial counter update
                setTimeout(updateContentCounter, 500);

                // Title counter
                const titleInput = document.getElementById('title');
                const titleLength = document.getElementById('title-length');

                if (titleInput && titleLength) {
                    titleInput.addEventListener('input', () => {
                        const length = titleInput.value.length;
                        titleLength.textContent = length;
                        const max = 200;

                        if (length > max) {
                            titleLength.classList.add('text-red-500', 'font-semibold');
                            titleLength.classList.remove('text-slate-500', 'dark:text-slate-400');
                        } else {
                            titleLength.classList.remove('text-red-500', 'font-semibold');
                            titleLength.classList.add('text-slate-500', 'dark:text-slate-400');
                        }
                    });
                }
                
                // Action mode tabs
                const actionTabs = document.querySelectorAll('.action-tab');
                actionTabs.forEach(tab => {
                    tab.addEventListener('click', () => {
                        actionMode = tab.dataset.mode;
                        actionTabs.forEach(t => {
                            if (t.dataset.mode === actionMode) {
                                t.classList.add('active', 'bg-gradient-to-r', 'from-indigo-500', 'to-purple-600', 'text-white', 'shadow-md');
                                t.classList.remove('text-slate-600', 'dark:text-slate-400');
                            } else {
                                t.classList.remove('active', 'bg-gradient-to-r', 'from-indigo-500', 'to-purple-600', 'text-white', 'shadow-md');
                                t.classList.add('text-slate-600', 'dark:text-slate-400');
                            }
                        });

                        const submitBtn = document.getElementById('submit-btn');
                        if (submitBtn) {
                            if (actionMode === 'draft') {
                                submitBtn.classList.remove('bg-gradient-to-r', 'from-indigo-500', 'to-purple-600', 'hover:from-indigo-600', 'hover:to-purple-700');
                                submitBtn.classList.add('bg-slate-100', 'dark:bg-slate-800', 'hover:bg-slate-200', 'dark:hover:bg-slate-700', 'text-orange-500', 'dark:text-orange-400');
                                const span = submitBtn.querySelector('span');
                                if (span) {
                                    span.textContent = 'Зберегти чернетку';
                                }
                            } else {
                                submitBtn.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'hover:bg-slate-200', 'dark:hover:bg-slate-700', 'text-orange-500', 'dark:text-orange-400');
                                submitBtn.classList.add('bg-gradient-to-r', 'from-indigo-500', 'to-purple-600', 'hover:from-indigo-600', 'hover:to-purple-700', 'text-white');
                                const span = submitBtn.querySelector('span');
                                if (span) {
                                    span.textContent = 'Оновити обговорення';
                                }
                            }
                        }
                    });
                });

                // Form submission
                const form = document.getElementById('discussion-form');
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    
                    // Ensure content is updated
                    if (contentInput) {
                        const editorElement = document.querySelector('.tiptap-editor');
                        if (editorElement) {
                            // Get HTML from editor
                            contentInput.value = editorApp.editorContent;
                        }
                    }

                    // Validate content length (plain text, not HTML)
                    const editorElement = document.querySelector('.tiptap-editor');
                    if (editorElement) {
                        const text = editorElement.innerText || editorElement.textContent || '';
                        const textLength = text.trim().length;
                        const min = 300;
                        const max = 3500;

                        if (textLength < min) {
                            alert(`Текст обговорення повинен містити мінімум ${min} символів. Зараз: ${textLength}`, 'Помилка валідації', 'error');
                            return;
                        }

                        if (textLength > max) {
                            alert(`Текст обговорення повинен містити максимум ${max} символів. Зараз: ${textLength}`, 'Помилка валідації', 'error');
                            return;
                        }
                    }

                    // Validate title
                    const titleInput = document.getElementById('title');
                    if (titleInput && !titleInput.value.trim()) {
                        alert('Будь ласка, введіть заголовок обговорення', 'Помилка валідації', 'error');
                        titleInput.focus();
                        return;
                    }

                    // Add is_draft if needed
                    if (actionMode === 'draft') {
                        let draftInput = form.querySelector('input[name="is_draft"]');
                        if (!draftInput) {
                            draftInput = document.createElement('input');
                            draftInput.type = 'hidden';
                            draftInput.name = 'is_draft';
                            form.appendChild(draftInput);
                        }
                        draftInput.value = '1';
                    } else {
                        // Remove is_draft if publishing
                        const draftInput = form.querySelector('input[name="is_draft"]');
                        if (draftInput) {
                            draftInput.remove();
                        }
                    }

                    // Submit the form
                    form.submit();
                });
            });
        </script>
    @endpush
@endsection
