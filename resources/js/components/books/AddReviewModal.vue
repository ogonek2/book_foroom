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
            <form @submit.prevent="submitReview" class="p-6 space-y-6">
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
                    </label>
                    <textarea v-model="content" 
                              rows="6"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white resize-none"
                              placeholder="Поділіться своїми враженнями про книгу..."></textarea>
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
            required: true
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
            isSubmitting: false
        };
    },
    methods: {
        show() {
            this.isVisible = true;
            this.isEditMode = false;
        },
        showWithData(reviewData) {
            this.isVisible = true;
            this.isEditMode = true;
            this.editReviewId = reviewData.id;
            this.reviewType = reviewData.review_type || 'review';
            this.rating = reviewData.rating || 0;
            this.opinionType = reviewData.opinion_type || 'positive';
            this.bookType = reviewData.book_type || 'paper';
            this.language = reviewData.language || 'uk';
            this.content = reviewData.content || '';
            this.containsSpoiler = reviewData.contains_spoiler || false;
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
        },
        async submitReview() {
            if (!this.content.trim() || this.rating === 0) {
                this.$emit('show-notification', 'Будь ласка, заповніть всі обов\'язкові поля', 'error');
                return;
            }

            this.isSubmitting = true;

            try {
                const url = this.isEditMode 
                    ? `/books/${this.bookSlug}/reviews/${this.editReviewId}` 
                    : `/books/${this.bookSlug}/reviews`;
                
                const method = this.isEditMode ? 'put' : 'post';
                
                const response = await axios[method](url, {
                    review_type: this.reviewType,
                    rating: this.rating,
                    opinion_type: this.opinionType,
                    book_type: this.bookType,
                    language: this.language,
                    content: this.content,
                    contains_spoiler: this.containsSpoiler
                }, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.data.success) {
                    if (this.isEditMode) {
                        this.$emit('review-updated', response.data.review);
                        this.$emit('show-notification', 'Рецензію успішно оновлено!', 'success');
                    } else {
                        this.$emit('review-added', response.data.review);
                        this.$emit('show-notification', 'Рецензія успішно додана!', 'success');
                    }
                    this.closeModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    this.$emit('show-notification', response.data.message || 'Помилка при збереженні рецензії', 'error');
                }
            } catch (error) {
                console.error('Error submitting review:', error);
                const errorMessage = error.response?.data?.message || 'Помилка при додаванні рецензії';
                this.$emit('show-notification', errorMessage, 'error');
            } finally {
                this.isSubmitting = false;
            }
        }
    }
};
</script>
