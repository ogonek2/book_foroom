<template>
    <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4" @click.self="closeModal" style="z-index: 9999;">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Додати цитату</h3>
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
                    </label>
                    <textarea v-model="quoteContent"
                              rows="4"
                              class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none transition-colors"
                              placeholder="Введіть текст цитати..."></textarea>
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
                <button @click="submitQuote"
                        :disabled="isSubmitting || !quoteContent.trim()"
                        class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-3 rounded-xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ isSubmitting ? 'Додавання...' : 'Додати цитату' }}
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
            required: true
        }
    },
    data() {
        return {
            isVisible: false,
            quoteContent: '',
            pageNumber: null,
            isPublic: true,
            isSubmitting: false,
        };
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
            this.quoteContent = '';
            this.pageNumber = null;
            this.isPublic = true;
        },
        async submitQuote() {
            if (!this.quoteContent.trim()) {
                this.$emit('show-notification', 'Будь ласка, введіть текст цитати.', 'error');
                return;
            }
            this.isSubmitting = true;
            try {
                const response = await axios.post(`/books/${this.bookSlug}/quotes`, {
                    content: this.quoteContent,
                    page_number: this.pageNumber,
                    is_public: this.isPublic
                });
                if (response.data.success) {
                    this.$emit('show-notification', 'Цитату успішно додано!', 'success');
                    this.$emit('quote-added', response.data.quote);
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

