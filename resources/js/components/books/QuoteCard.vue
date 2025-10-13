<template>
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700 hover:shadow-xl transition-all duration-300 h-full
        flex flex-col justify-between">
        <!-- User Info -->
        <div>
            <div class="flex items-center space-x-3 justify-between">
                <div class="flex items-center space-x-3">
                    <img v-if="quote.user && quote.user.avatar_display" :src="quote.user.avatar_display"
                        :alt="quote.user.name" class="w-8 h-8 rounded-full">
                    <div v-else
                        class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center text-white font-bold text-sm">
                        {{ (quote.user?.name || 'U').charAt(0).toUpperCase() }}
                    </div>
                    <div>
                        <div class="text-sm font-medium text-slate-900 dark:text-white">
                            {{ quote.user?.name || 'Користувач' }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            {{ formatDate(quote.created_at) }}
                        </div>
                    </div>
                </div>
                <!-- Quote Icon -->
                <div class="mb-4">
                    <svg width="32" height="14" viewBox="0 0 32 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M6.9682 0.131836C8.63086 0.131836 10.0929 0.533169 11.3542 1.33584C12.6729 2.08117 13.4755 3.05584 13.7622 4.25983C14.3355 6.38117 13.9342 8.44517 12.5582 10.4518C11.2395 12.4012 9.4622 13.2898 7.2262 13.1178C8.71686 12.2578 9.3762 11.1972 9.2042 9.93583C8.45886 10.1078 7.71353 10.1938 6.9682 10.1938C5.01886 10.1938 3.3562 9.7065 1.9802 8.73183C0.661531 7.75717 0.00219727 6.58184 0.00219727 5.20584C0.00219727 3.7725 0.661531 2.5685 1.9802 1.59383C3.3562 0.619168 5.01886 0.131836 6.9682 0.131836ZM30.8762 4.25983C31.4495 6.38117 31.0769 8.44517 29.7582 10.4518C28.4395 12.4012 26.6335 13.2898 24.3402 13.1178C25.8309 12.2578 26.4902 11.1972 26.3182 9.93583C25.5729 10.1078 24.8275 10.1938 24.0822 10.1938C22.1329 10.1938 20.4702 9.7065 19.0942 8.73183C17.7755 7.75717 17.1162 6.58184 17.1162 5.20584C17.1162 3.7725 17.7755 2.5685 19.0942 1.59383C20.4702 0.619168 22.1329 0.131836 24.0822 0.131836C25.7449 0.131836 27.2069 0.533169 28.4682 1.33584C29.7869 2.08117 30.5895 3.05584 30.8762 4.25983Z"
                            fill="#F97316" />
                    </svg>
                </div>
            </div>
            <!-- Quote Text -->
            <div v-if="!isEditing">
                <blockquote class="text-lg text-slate-700 dark:text-slate-300 leading-relaxed font-medium italic my-4">
                    "{{ quote.content }}"
                </blockquote>
            </div>
            
            <!-- Edit Form -->
            <div v-else class="my-4">
                <textarea v-model="editContent" 
                    class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"
                    rows="3" placeholder="Введіть цитату..."></textarea>
                <input v-model="editPageNumber" type="number" min="1" placeholder="Номер сторінки (необов'язково)"
                    class="w-full mt-2 p-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <div class="flex items-center space-x-2 mt-2">
                    <label class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <input v-model="editIsPublic" type="checkbox" class="mr-2">
                        Публічна цитата
                    </label>
                </div>
                <div class="flex space-x-2 mt-3">
                    <button @click="saveEdit" :disabled="isSaving"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 text-sm">
                        {{ isSaving ? 'Збереження...' : 'Зберегти' }}
                    </button>
                    <button @click="cancelEdit" :disabled="isSaving"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50 text-sm">
                        Скасувати
                    </button>
                </div>
            </div>
        </div>
        <div>

            <!-- Page Number -->
            <div v-if="quote.page_number" class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                Сторінка {{ quote.page_number }}
            </div>
            <!-- Actions -->
            <div class="flex items-center space-x-4">
                <!-- Like Button -->
                <button @click="toggleLike"
                    class="flex items-center space-x-1 text-slate-600 dark:text-slate-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                    <i :class="['fas fa-heart', isLiked ? 'text-red-500 dark:text-red-400' : '']"></i>
                    <span class="text-sm">{{ likesCount }}</span>
                </button>

                <!-- Share Button -->
                <button @click="shareQuote"
                    class="flex items-center space-x-1 text-slate-600 dark:text-slate-400 hover:text-brand-500 dark:hover:text-brand-400 transition-colors">
                    <i class="fas fa-share-alt"></i>
                </button>

                <!-- Edit Button (for owner) -->
                <button v-if="canEdit" @click="startEdit"
                    class="text-slate-600 dark:text-slate-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                    <i class="fas fa-edit text-sm"></i>
                </button>

                <!-- Delete Button (for owner) -->
                <button v-if="canDelete" @click="deleteQuote"
                    class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">
                    <i class="fas fa-trash text-sm"></i>
                </button>

                <!-- Report Button -->
                <report-button 
                    :reportable-type="'App\\Models\\Quote'"
                    :reportable-id="quote.id"
                    :content-preview="getContentPreview()"
                    :content-url="getContentUrl()">
                </report-button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'QuoteCard',
    props: {
        quote: {
            type: Object,
            required: true
        },
        bookSlug: {
            type: String,
            required: true
        },
        currentUserId: {
            type: Number,
            default: null
        }
    },
    data() {
        return {
            isLiked: this.quote.is_liked_by_current_user || false,
            likesCount: this.quote.likes_count || 0,
            isEditing: false,
            editContent: '',
            editPageNumber: null,
            editIsPublic: true,
            isSaving: false,
        };
    },
    computed: {
        canDelete() {
            return this.currentUserId && this.quote.user_id === this.currentUserId;
        },
        canEdit() {
            return this.currentUserId && this.quote.user_id === this.currentUserId;
        }
    },
    methods: {
        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);

            if (diff < 60) return 'щойно';
            if (diff < 3600) return `${Math.floor(diff / 60)} хв тому`;
            if (diff < 86400) return `${Math.floor(diff / 3600)} год тому`;
            if (diff < 604800) return `${Math.floor(diff / 86400)} дн тому`;

            return date.toLocaleDateString('uk-UA', {
                day: 'numeric',
                month: 'short',
                year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
            });
        },
        async toggleLike() {
            if (!this.currentUserId) {
                this.$emit('show-notification', 'Будь ласка, увійдіть, щоб поставити лайк.', 'error');
                return;
            }
            try {
                const response = await axios.post(`/books/${this.bookSlug}/quotes/${this.quote.id}/like`);
                if (response.data.success) {
                    this.isLiked = response.data.is_liked;
                    this.likesCount = response.data.likes_count;
                    this.$emit('like-toggled', { quoteId: this.quote.id, isLiked: this.isLiked, likesCount: this.likesCount });
                }
            } catch (error) {
                console.error('Error toggling like:', error);
                this.$emit('show-notification', 'Помилка при зміні лайка.', 'error');
            }
        },
        shareQuote() {
            const text = `"${this.quote.content}" — ${this.quote.user?.name || 'Користувач'}`;
            if (navigator.share) {
                navigator.share({
                    title: 'Цитата з книги',
                    text: text,
                    url: window.location.href
                }).catch(err => console.log('Error sharing:', err));
            } else {
                navigator.clipboard.writeText(text).then(() => {
                    this.$emit('show-notification', 'Цитату скопійовано!', 'success');
                });
            }
        },
        startEdit() {
            this.isEditing = true;
            this.editContent = this.quote.content;
            this.editPageNumber = this.quote.page_number;
            this.editIsPublic = this.quote.is_public;
        },
        cancelEdit() {
            this.isEditing = false;
            this.editContent = '';
            this.editPageNumber = null;
            this.editIsPublic = true;
        },
        async saveEdit() {
            if (!this.editContent.trim()) {
                this.$emit('show-notification', 'Будь ласка, введіть текст цитати.', 'error');
                return;
            }

            this.isSaving = true;
            try {
                const response = await axios.put(`/books/${this.bookSlug}/quotes/${this.quote.id}`, {
                    content: this.editContent.trim(),
                    page_number: this.editPageNumber || null,
                    is_public: this.editIsPublic,
                });

                if (response.data.success) {
                    this.$emit('show-notification', 'Цитату оновлено!', 'success');
                    this.$emit('quote-updated', response.data.quote);
                    this.isEditing = false;
                } else {
                    this.$emit('show-notification', response.data.message || 'Помилка при оновленні цитати.', 'error');
                }
            } catch (error) {
                console.error('Error updating quote:', error);
                this.$emit('show-notification', error.response?.data?.message || 'Помилка при оновленні цитати.', 'error');
            } finally {
                this.isSaving = false;
            }
        },
        async deleteQuote() {
            if (!confirm('Ви впевнені, що хочете видалити цю цитату?')) return;
            try {
                const response = await axios.delete(`/books/${this.bookSlug}/quotes/${this.quote.id}`);
                if (response.data.success) {
                    this.$emit('show-notification', 'Цитату видалено!', 'success');
                    this.$emit('quote-deleted', this.quote.id);
                } else {
                    this.$emit('show-notification', response.data.message || 'Помилка при видаленні цитати.', 'error');
                }
            } catch (error) {
                console.error('Error deleting quote:', error);
                this.$emit('show-notification', error.response?.data?.message || 'Помилка при видаленні цитати.', 'error');
            }
        },

        getContentPreview() {
            // Возвращаем превью контента для жалобы
            if (this.quote.content) {
                const text = this.quote.content;
                return text.substring(0, 100) + (text.length > 100 ? '...' : '');
            }
            return 'Цитата з книги';
        },

        getContentUrl() {
            // Возвращаем URL контента
            return `/books/${this.bookSlug}#quote-${this.quote.id}`;
        }
    }
};
</script>

<style scoped>
/* Quote card styles inherited from parent */
</style>
