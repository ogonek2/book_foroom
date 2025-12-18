<template>
    <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4" @click.self="closeModal" style="z-index: 9999;">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ isEditMode ? 'Редагувати цитату' : 'Додати цитату' }}</h3>
                    <button @click="closeModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Form -->
            <div class="p-6 space-y-6">
                <!-- Quote Text -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Текст цитати
                        <span class="text-xs font-normal text-slate-500 dark:text-slate-400 ml-2">
                            (<span :class="getContentLengthClass()">{{ quoteContentLength }}</span> / 
                            <span>500</span> символів)
                        </span>
                    </label>
                    <textarea v-model="quoteContent"
                              rows="4"
                              class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none transition-colors"
                              placeholder="Введіть текст цитати..."></textarea>
                    <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Мінімум: 20 символів, Максимум: 500 символів
                    </div>
                </div>

                <!-- Page Number -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                        Номер сторінки (необов'язково)
                    </label>
                    <input v-model="pageNumber"
                           type="number"
                           min="1"
                           class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors"
                           placeholder="Наприклад: 42">
                </div>

                <!-- Public/Private Toggle -->
                <div class="flex items-center space-x-3">
                    <input v-model="isPublic"
                           type="checkbox"
                           id="is-public"
                           class="w-5 h-5 text-brand-500 border-slate-300 rounded focus:ring-brand-500">
                    <label for="is-public" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                        Зробити цитату публічною
                    </label>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-slate-200 dark:border-slate-700 flex justify-end space-x-3">
                <button @click="closeModal"
                        class="px-6 py-3 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors font-medium">
                    Скасувати
                </button>
                <button @click="saveAsDraft"
                        :disabled="isSubmitting || !quoteContent.trim()"
                        class="px-6 py-3 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ isSubmitting ? 'Збереження...' : (isEditMode ? 'Оновити чернетку' : 'Зберегти чернетку') }}
                </button>
                <button @click="submitQuote"
                        :disabled="isSubmitting || !quoteContent.trim()"
                        class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-3 rounded-xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ isSubmitting ? (isEditMode ? 'Оновлення...' : 'Додавання...') : (isEditMode ? 'Оновити цитату' : 'Додати цитату') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'AddQuoteModal',
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
            editQuoteId: null,
            quoteContent: '',
            pageNumber: null,
            isPublic: true,
            isSubmitting: false,
            currentBookSlug: this.bookSlug ? String(this.bookSlug).trim().replace(/[^a-zA-Z0-9_-]/g, '') : '',
            isDraft: false // Флаг для отслеживания статуса черновика
        };
    },
    computed: {
        quoteContentLength() {
            return this.quoteContent ? this.quoteContent.length : 0;
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
        }
    },
    mounted() {
        // Move modal to body when mounted
        this.moveToBody();
        
        // Close modal on Escape key
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                this.closeModal();
            }
        };
        document.addEventListener('keydown', handleEscape);
        this.$once('hook:beforeDestroy', () => {
            document.removeEventListener('keydown', handleEscape);
            this.removeFromBody();
        });
    },
    methods: {
        show() {
            this.isVisible = true;
            this.isEditMode = false;
        },
        showWithData(quoteData, bookSlug = null) {
            try {
                // Валидация входных данных
                if (!quoteData || typeof quoteData !== 'object') {
                    console.error('Invalid quoteData:', quoteData);
                    this.$emit('show-notification', 'Помилка: Некоректні дані цитати', 'error');
                    return;
                }
                
                // Очистка и валидация bookSlug - более строгая очистка
                let cleanBookSlug = '';
                if (bookSlug) {
                    // Удаляем все символы кроме букв, цифр, дефисов и подчеркиваний
                    cleanBookSlug = String(bookSlug).trim().replace(/[^a-zA-Z0-9_-]/g, '');
                } else if (quoteData && quoteData.book_slug) {
                    cleanBookSlug = String(quoteData.book_slug).trim().replace(/[^a-zA-Z0-9_-]/g, '');
                } else if (this.bookSlug) {
                    cleanBookSlug = String(this.bookSlug).trim().replace(/[^a-zA-Z0-9_-]/g, '');
                } else if (this.currentBookSlug) {
                    cleanBookSlug = String(this.currentBookSlug).trim().replace(/[^a-zA-Z0-9_-]/g, '');
                }
                
                if (!cleanBookSlug) {
                    console.error('Invalid bookSlug:', bookSlug, quoteData.book_slug);
                    this.$emit('show-notification', 'Помилка: Не вказано книгу', 'error');
                    return;
                }
                
                // Устанавливаем данные
                this.isVisible = true;
                this.isEditMode = true;
                this.editQuoteId = quoteData.id ? parseInt(quoteData.id) : null;
                this.quoteContent = quoteData.content ? String(quoteData.content) : '';
                this.pageNumber = quoteData.page_number ? parseInt(quoteData.page_number) : null;
                this.isPublic = quoteData.is_public !== false && quoteData.is_public !== 'false' && quoteData.is_public !== 0;
                this.isDraft = quoteData.is_draft === true || quoteData.is_draft === 'true' || quoteData.is_draft === 1;
                this.currentBookSlug = cleanBookSlug;
                
                console.log('Quote modal opened with data:', {
                    editQuoteId: this.editQuoteId,
                    bookSlug: this.currentBookSlug,
                    isDraft: this.isDraft
                });
            } catch (error) {
                console.error('Error in showWithData:', error);
                console.error('Error details:', {
                    quoteData,
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
            this.editQuoteId = null;
            this.quoteContent = '';
            this.pageNumber = null;
            this.isPublic = true;
            this.isDraft = false;
        },
        async submitQuote(isDraft = false) {
            if (!this.quoteContent.trim()) {
                this.$emit('show-notification', 'Будь ласка, введіть текст цитати.', 'error');
                return;
            }
            
            // Для редактирования черновика используем сохраненный статус, если не указан явно
            if (this.isEditMode && this.isDraft && !isDraft) {
                // Если редактируем черновик и не указано явно опубликовать, остаемся черновиком
                isDraft = true;
            }
            
            this.isSubmitting = true;
            try {
                const bookSlug = this.currentBookSlug || this.bookSlug;
                const url = this.isEditMode 
                    ? `/books/${bookSlug}/quotes/${this.editQuoteId}` 
                    : `/books/${bookSlug}/quotes`;
                
                const method = this.isEditMode ? 'put' : 'post';
                
                const response = await axios[method](url, {
                    content: this.quoteContent,
                    page_number: this.pageNumber,
                    is_public: this.isPublic,
                    is_draft: isDraft
                }, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                });
                
                if (response.data.success) {
                    if (this.isEditMode) {
                        this.$emit('quote-updated', response.data.quote);
                        this.$emit('show-notification', isDraft ? 'Чернетку оновлено!' : 'Цитату успішно оновлено!', 'success');
                    } else {
                        this.$emit('quote-added', response.data.quote);
                        this.$emit('show-notification', isDraft ? 'Чернетку збережено!' : 'Цитату успішно додано!', 'success');
                    }
                    // Просто закрываем модалку, обновление списка происходит через события
                    this.closeModal();
                } else {
                    this.$emit('show-notification', response.data.message || 'Помилка при додаванні цитати.', 'error');
                }
            } catch (error) {
                console.error('Error submitting quote:', error);
                this.$emit('show-notification', error.response?.data?.message || 'Помилка при додаванні цитати.', 'error');
            } finally {
                this.isSubmitting = false;
            }
        },
        async saveAsDraft() {
            await this.submitQuote(true);
        },
        moveToBody() {
            // Move the modal element to body
            const modalElement = this.$el;
            if (modalElement && modalElement.parentNode !== document.body) {
                document.body.appendChild(modalElement);
            }
        },
        removeFromBody() {
            // Remove modal from body when component is destroyed
            const modalElement = this.$el;
            if (modalElement && modalElement.parentNode === document.body) {
                document.body.removeChild(modalElement);
            }
        },
        getContentLengthClass() {
            const length = this.quoteContentLength;
            const min = 20;
            const max = 500;
            
            if (length < min || length > max) {
                return 'text-red-500 font-semibold';
            }
            return 'text-slate-500';
        }
    }
};
</script>

<style scoped>
/* Modal is positioned fixed and should be on top of everything */
</style>

<style>
/* Global styles to ensure modal is always on top */
.fixed.inset-0 {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
}
</style>

