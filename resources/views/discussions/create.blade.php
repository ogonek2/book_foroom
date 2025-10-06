@extends('layouts.app')

@section('title', 'Создать обсуждение')

@section('main')
    <div class="min-h-screen bg-light-bg dark:bg-dark-bg transition-colors duration-300">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-light-text-primary dark:text-dark-text-primary mb-2">Создать обсуждение</h1>
            <p class="text-light-text-secondary dark:text-dark-text-secondary">Поделитесь своими мыслями и начните обсуждение
                с сообществом</p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border border-light-border dark:border-dark-border shadow-xl">
            <form action="{{ route('discussions.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Title -->
                <div>
                    <label for="title"
                        class="block text-light-text-primary dark:text-dark-text-primary font-medium mb-3">Заголовок
                        обсуждения</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}"
                        placeholder="Введите заголовок обсуждения..."
                        class="w-full px-4 py-3 bg-light-bg-secondary dark:bg-gray-700 border border-light-border dark:border-dark-border rounded-xl text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors @error('title') border-red-500 @enderror"
                        required>
                    @error('title')
                        <p class="mt-2 text-red-500 dark:text-red-400 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div>
                    <label for="content"
                        class="block text-light-text-primary dark:text-dark-text-primary font-medium mb-3">Содержание
                        обсуждения</label>
                    <textarea id="content" name="content" style="display: none;"
                        placeholder="Опишите тему для обсуждения. Будьте конкретными и интересными...">{{ old('content') }}</textarea>
                    <div id="quill-editor"></div>
                    @error('content')
                        <p class="mt-2 text-red-500 dark:text-red-400 text-sm">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-light-text-tertiary dark:text-dark-text-tertiary text-sm">Минимум 10 символов</p>
                </div>

                <!-- Guidelines -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                    <h4 class="text-blue-700 dark:text-blue-300 font-medium mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        Правила создания обсуждений
                    </h4>
                    <ul class="text-blue-600 dark:text-blue-400 text-sm space-y-1">
                        <li>• Будьте вежливы и уважительны к другим участникам</li>
                        <li>• Создавайте содержательные и интересные обсуждения</li>
                        <li>• Избегайте спама и повторяющихся тем</li>
                        <li>• Используйте понятные и информативные заголовки</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-6">
                    <a href="{{ route('discussions.index') }}"
                        class="text-light-text-secondary dark:text-dark-text-secondary hover:text-brand-500 dark:hover:text-brand-400 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Назад к обсуждениям
                    </a>
                    <div class="flex space-x-4">
                        <button type="button" onclick="history.back()"
                            class="px-6 py-3 bg-gray-500 dark:bg-gray-600 text-white rounded-xl font-medium hover:bg-gray-600 dark:hover:bg-gray-700 transition-colors">
                            Отмена
                        </button>
                        <button type="submit"
                            class="bg-gradient-to-r from-brand-500 to-accent-500 text-white px-8 py-3 rounded-xl font-bold hover:from-brand-600 hover:to-accent-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Создать обсуждение
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <style>
            .ql-editor {
                min-height: 300px;
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
            
            #quill-editor {
                overflow: hidden;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
        <script>
            // Initialize Quill editor
            document.addEventListener('DOMContentLoaded', function() {
                const toolbarOptions = [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['clean'],
                    ['link', 'image']
                ];
                
                const quill = new Quill('#quill-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: toolbarOptions
                    },
                });
                
                // Update hidden textarea when content changes
                quill.on('text-change', function() {
                    const contentInput = document.querySelector('textarea[name="content"]');
                    if (contentInput) {
                        contentInput.value = quill.root.innerHTML;
                    }
                });
                
                // Update hidden textarea when form is submitted (backup)
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        const contentInput = document.querySelector('textarea[name="content"]');
                        if (contentInput) {
                            contentInput.value = quill.root.innerHTML;
                        }
                    });
                }
                
                // Set initial content if exists
                const initialContent = document.querySelector('textarea[name="content"]').value;
                if (initialContent) {
                    quill.root.innerHTML = initialContent;
                }
            });
        </script>
    @endpush
@endsection
