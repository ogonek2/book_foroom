<template>
    <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4" style="z-index: 9999;">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ isEditMode ? 'Редагувати рецензію' : 'Додати рецензію' }}
                </h2>
                <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleFormSubmit" class="p-6 space-y-6">
                <!-- Review Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Тип відгуку
                    </label>
                    <select v-model="reviewType" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="review">Рецензія</option>
                        <option value="opinion">Відгук</option>
                    </select>
                </div>

                <!-- Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Оцінка
                    </label>
                    <div class="flex space-x-2">
                        <button v-for="star in 10" :key="star" 
                                @click="rating = star"
                                type="button"
                                class="text-3xl transition-colors"
                                :class="star <= rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'">
                            ★
                        </button>
                    </div>
                </div>

                <!-- Opinion Type -->
                <div v-if="reviewType === 'opinion'">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Тип думки
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <button v-for="type in ['positive', 'neutral', 'negative']" :key="type"
                                @click="opinionType = type"
                                type="button"
                                class="px-4 py-3 rounded-xl border transition-all"
                                :class="opinionType === type 
                                    ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' 
                                    : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-indigo-300'">
                            {{ type === 'positive' ? 'Позитивна' : type === 'neutral' ? 'Нейтральна' : 'Негативна' }}
                        </button>
                    </div>
                </div>

                <!-- Book Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Тип книги
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <button v-for="type in ['paper', 'electronic', 'audio']" :key="type"
                                @click="bookType = type"
                                type="button"
                                class="px-4 py-3 rounded-xl border transition-all"
                                :class="bookType === type 
                                    ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' 
                                    : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-indigo-300'">
                            {{ type === 'paper' ? 'Паперова' : type === 'electronic' ? 'Електронна' : 'Аудіо' }}
                        </button>
                    </div>
                </div>

                <!-- Language -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Мова рецензії
                    </label>
                    <select v-model="language" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        <option value="uk">Українська</option>
                        <option value="en">English</option>
                        <option value="de">Німецька</option>
                        <option value="other">Інша</option>
                    </select>
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Ваша думка
                        <span class="text-xs font-normal text-gray-500 dark:text-gray-400 ml-2">
                            (<span :class="getContentLengthClass()">{{ contentLength }}</span> / 
                            <span>{{ maxContentLength }}</span> символів)
                        </span>
                    </label>
                    <div ref="quillEditor" id="quill-editor-review-modal"></div>
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Мінімум: {{ minContentLength }} символів
                    </div>
                </div>

                <!-- Spoiler Checkbox -->
                <div class="flex items-center">
                    <input v-model="containsSpoiler" 
                           type="checkbox" 
                           id="spoiler"
                           class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <label for="spoiler" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                        Містить спойлер
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex space-x-4 pt-4">
                    <button type="button" 
                            @click="closeModal"
                            class="flex-1 px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Скасувати
                    </button>
                    <button type="button" 
                            @click="saveAsDraft"
                            :disabled="isSubmitting || !content.trim()"
                            class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <span v-if="isSubmitting">Збереження...</span>
                        <span v-else>Зберегти чернетку</span>
                    </button>
                    <button type="submit" 
                            :disabled="isSubmitting || !content.trim() || rating === 0"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:from-indigo-600 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        <span v-if="isSubmitting">Відправка...</span>
                        <span v-else>Опублікувати</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'AddReviewModal',
    props: {
        bookSlug: {
            type: String,
            required: false,
            default: ''
        }
    },
    data() {
        return {
            isVisible: false,
            isEditMode: false,
            editReviewId: null,
            reviewType: 'review',
            rating: 0,
            opinionType: 'positive',
            bookType: 'paper',
            language: 'uk',
            content: '',
            containsSpoiler: false,
            isSubmitting: false,
            currentBookSlug: this.bookSlug ? String(this.bookSlug).trim().replace(/[^a-zA-Z0-9_-]/g, '') : '',
            isDraft: false, // Флаг для отслеживания статуса черновика
            quillInstance: null // Quill editor instance
        };
    },
    computed: {
        contentLength() {
            if (this.quillInstance) {
                return this.quillInstance.getText().length;
            }
            return this.content ? this.content.length : 0;
        },
        minContentLength() {
            return this.reviewType === 'opinion' ? 100 : 800;
        },
        maxContentLength() {
            return this.reviewType === 'opinion' ? 1000 : 15000;
        }
    },
    watch: {
        bookSlug(newVal) {
            // Очищаем bookSlug от недопустимых символов
            if (newVal) {
                this.currentBookSlug = String(newVal).trim().replace(/[^a-zA-Z0-9_-]/g, '');
            } else {
                this.currentBookSlug = '';
            }
        },
        reviewType() {
            // Обновляем лимиты при изменении типа рецензии
            this.$forceUpdate();
        }
    },
    mounted() {
        // Quill will be initialized when modal is shown
    },
    methods: {
        getContentLengthClass() {
            const length = this.contentLength;
            const min = this.minContentLength;
            const max = this.maxContentLength;
            
            if (length < min || length > max) {
                return 'text-red-500 font-semibold';
            }
            return 'text-gray-500';
        },
        show() {
            this.isVisible = true;
            this.isEditMode = false;
            this.$nextTick(() => {
                this.initQuill();
            });
        },
        showWithData(reviewData, bookSlug = null) {
            try {
                // Валидация входных данных
                if (!reviewData || typeof reviewData !== 'object') {
                    console.error('Invalid reviewData:', reviewData);
                    this.$emit('show-notification', 'Помилка: Некоректні дані рецензії', 'error');
                    return;
                }
                
                // Очистка и валидация bookSlug - более строгая очистка
                let cleanBookSlug = '';
                if (bookSlug) {
                    // Удаляем все символы кроме букв, цифр, дефисов и подчеркиваний
                    cleanBookSlug = String(bookSlug).trim().replace(/[^a-zA-Z0-9_-]/g, '');
                } else if (reviewData && reviewData.book_slug) {
                    cleanBookSlug = String(reviewData.book_slug).trim().replace(/[^a-zA-Z0-9_-]/g, '');
                } else if (this.bookSlug) {
                    cleanBookSlug = String(this.bookSlug).trim().replace(/[^a-zA-Z0-9_-]/g, '');
                } else if (this.currentBookSlug) {
                    cleanBookSlug = String(this.currentBookSlug).trim().replace(/[^a-zA-Z0-9_-]/g, '');
                }
                
                if (!cleanBookSlug) {
                    console.error('Invalid bookSlug:', bookSlug, reviewData.book_slug);
                    this.$emit('show-notification', 'Помилка: Не вказано книгу', 'error');
                    return;
                }
                
                // Устанавливаем данные
                this.isVisible = true;
                this.isEditMode = true;
                this.editReviewId = reviewData.id ? parseInt(reviewData.id) : null;
                this.reviewType = reviewData.review_type || 'review';
                this.rating = reviewData.rating ? parseInt(reviewData.rating) : 0;
                this.opinionType = reviewData.opinion_type || 'positive';
                this.bookType = reviewData.book_type || 'paper';
                this.language = reviewData.language || 'uk';
                this.content = reviewData.content ? String(reviewData.content) : '';
                // Initialize Quill editor after setting data
                this.$nextTick(() => {
                    this.initQuill();
                });
                this.containsSpoiler = reviewData.contains_spoiler === true || reviewData.contains_spoiler === 'true' || reviewData.contains_spoiler === 1;
                this.isDraft = reviewData.is_draft === true || reviewData.is_draft === 'true' || reviewData.is_draft === 1;
                this.currentBookSlug = cleanBookSlug;
                
                console.log('Modal opened with data:', {
                    editReviewId: this.editReviewId,
                    bookSlug: this.currentBookSlug,
                    isDraft: this.isDraft
                });
            } catch (error) {
                console.error('Error in showWithData:', error);
                console.error('Error details:', {
                    reviewData,
                    bookSlug,
                    errorMessage: error.message,
                    errorStack: error.stack
                });
                this.$emit('show-notification', 'Помилка при відкритті форми редагування: ' + error.message, 'error');
            }
        },
        hide() {
            this.isVisible = false;
            this.resetForm();
        },
        closeModal() {
            this.isVisible = false;
            this.resetForm();
        },
        resetForm() {
            this.isEditMode = false;
            this.editReviewId = null;
            this.reviewType = 'review';
            this.rating = 0;
            this.opinionType = 'positive';
            this.bookType = 'paper';
            this.language = 'uk';
            this.content = '';
            this.containsSpoiler = false;
            this.isSubmitting = false;
            this.isDraft = false;
            // Clear Quill editor
            if (this.quillInstance) {
                this.quillInstance.root.innerHTML = '';
            }
        },
        saveAsDraft() {
            this.submitReview(true);
        },
        async submitReview(isDraft = false) {
            // Убеждаемся, что isDraft - это boolean, а не объект события
            if (typeof isDraft !== 'boolean') {
                isDraft = false; // По умолчанию публикуем
            }
            
            // Get content from Quill editor
            if (this.quillInstance) {
                this.content = this.sanitizeHTML(this.quillInstance.root.innerHTML);
            }
            
            // Check text length (not HTML)
            const textLength = this.quillInstance ? this.quillInstance.getText().length : this.content.length;
            if (textLength === 0 || !this.content.trim()) {
                this.$emit('show-notification', 'Будь ласка, введіть текст рецензії', 'error');
                return;
            }

            // Проверяем, что при публикации есть рейтинг
            // Если публикуем (не черновик), rating обязателен и должен быть > 0
            if (!isDraft) {
                if (!this.rating || this.rating === 0) {
                    this.$emit('show-notification', 'Будь ласка, поставте оцінку перед публікацією', 'error');
                    return;
                }
            }

            this.isSubmitting = true;

            try {
                const bookSlug = (this.currentBookSlug || this.bookSlug || '').trim();
                
                if (!bookSlug) {
                    this.$emit('show-notification', 'Помилка: Не вказано книгу', 'error');
                    this.isSubmitting = false;
                    return;
                }
                
                // Laravel route model binding автоматически обрабатывает slug
                const url = this.isEditMode 
                    ? `/books/${bookSlug}/reviews/${this.editReviewId}` 
                    : `/books/${bookSlug}/reviews`;
                
                const method = this.isEditMode ? 'put' : 'post';
                
                // Подготавливаем данные для отправки
                const requestData = {
                    review_type: this.reviewType,
                    opinion_type: this.opinionType,
                    book_type: this.bookType,
                    language: this.language,
                    content: this.content,
                    contains_spoiler: this.containsSpoiler ? true : false,
                    is_draft: isDraft ? true : false // Явно преобразуем в boolean
                };
                
                // Для черновиков rating может быть 0 или null, для публикации - обязателен
                if (isDraft) {
                    // Для черновика отправляем rating только если он установлен
                    if (this.rating && this.rating > 0) {
                        requestData.rating = parseInt(this.rating);
                    }
                } else {
                    // При публикации rating обязателен - убеждаемся, что это число
                    requestData.rating = parseInt(this.rating);
                    if (isNaN(requestData.rating) || requestData.rating < 1 || requestData.rating > 10) {
                        this.$emit('show-notification', 'Будь ласка, встановіть коректну оцінку (від 1 до 10)', 'error');
                        this.isSubmitting = false;
                        return;
                    }
                }
                
                console.log('Submitting review:', {
                    url,
                    method,
                    isDraft,
                    rating: this.rating,
                    requestData: requestData
                });
                
                const response = await axios[method](url, requestData, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.data.success) {
                    if (this.isEditMode) {
                        this.$emit('review-updated', response.data.review);
                        this.$emit('show-notification', isDraft ? 'Чернетку успішно оновлено!' : 'Рецензію успішно оновлено!', 'success');
                    } else {
                        this.$emit('review-added', response.data.review);
                        this.$emit('show-notification', isDraft ? 'Чернетку збережено!' : 'Рецензія успішно додана!', 'success');
                    }
                    this.closeModal();
                    if (!isDraft) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        setTimeout(() => {
                            window.location.href = '/profile?tab=drafts';
                        }, 1000);
                    }
                } else {
                    this.$emit('show-notification', response.data.message || 'Помилка при збереженні рецензії', 'error');
                }
            } catch (error) {
                console.error('Error submitting review:', error);
                console.error('Error response:', error.response?.data);
                
                let errorMessage = 'Помилка при додаванні рецензії';
                
                if (error.response?.status === 422) {
                    // Ошибка валидации
                    const errors = error.response.data.errors || {};
                    const errorMessages = [];
                    
                    for (const field in errors) {
                        if (errors[field]) {
                            errorMessages.push(errors[field].join(', '));
                        }
                    }
                    
                    if (errorMessages.length > 0) {
                        errorMessage = 'Помилки валідації: ' + errorMessages.join('; ');
                    } else if (error.response.data.message) {
                        errorMessage = error.response.data.message;
                    }
                } else if (error.response?.data?.message) {
                    errorMessage = error.response.data.message;
                }
                
                this.$emit('show-notification', errorMessage, 'error');
            } finally {
                this.isSubmitting = false;
            }
        },
        async saveAsDraft() {
            await this.submitReview(true);
        },
        handleFormSubmit(event) {
            // Предотвращаем стандартную отправку формы
            event.preventDefault();
            // При отправке формы (кнопка "Опублікувати") публикуем, не сохраняем как черновик
            this.submitReview(false);
        },
        initQuill() {
            this.$nextTick(() => {
                if (typeof Quill === 'undefined') {
                    console.error('Quill is not loaded');
                    return;
                }
                
                // Destroy existing instance if any
                if (this.quillInstance) {
                    const editorElement = document.getElementById('quill-editor-review-modal');
                    if (editorElement) {
                        editorElement.innerHTML = '';
                    }
                }
                
                const toolbarOptions = [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ];
                
                this.quillInstance = new Quill('#quill-editor-review-modal', {
                    theme: 'snow',
                    modules: {
                        toolbar: toolbarOptions
                    },
                });
                
                // Set initial content if exists (before setting up event listener)
                if (this.content) {
                    this.quillInstance.root.innerHTML = this.content;
                }
                
                // Update content when editor changes
                this.quillInstance.on('text-change', () => {
                    this.content = this.sanitizeHTML(this.quillInstance.root.innerHTML);
                    this.$forceUpdate();
                });
            });
        },
        sanitizeHTML(html) {
            const temp = document.createElement('div');
            temp.innerHTML = html;
            
            // Remove script tags and event handlers
            const scripts = temp.querySelectorAll('script');
            scripts.forEach(script => script.remove());
            
            // Remove style tags
            const styles = temp.querySelectorAll('style');
            styles.forEach(style => style.remove());
            
            // Remove inline styles and event handlers
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
            
            const nodesToRemove = [];
            const walker = document.createTreeWalker(
                temp,
                NodeFilter.SHOW_ELEMENT,
                null,
                false
            );
            
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
    }
};
</script>
