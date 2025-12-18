<template>
    <div class="lg:px-4">
        <!-- Add Fact Form -->
        <div
            v-if="showAddForm"
            class="mb-6 p-6 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600"
        >
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Додати новий факт</h4>
            <form @submit.prevent="addFact">
                <div class="mb-4">
                    <textarea
                        v-model="newFact"
                        placeholder="Введіть цікавий факт про книгу..."
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-800 dark:text-white resize-none"
                        rows="3"
                        required
                    ></textarea>
                </div>
                <div class="flex space-x-3">
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-xl font-semibold transition-colors disabled:opacity-50"
                    >
                        <i class="fas fa-save mr-2"></i>
                        {{ isSubmitting ? 'Збереження...' : 'Зберегти' }}
                    </button>
                    <button
                        type="button"
                        @click="cancelAdd"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-xl font-semibold transition-colors"
                    >
                        Скасувати
                    </button>
                </div>
            </form>
        </div>

        <div v-if="localFacts && localFacts.length > 0" class="space-y-4">
            <fact-card
                v-for="fact in localFacts"
                :key="fact.id"
                :fact="fact"
                :book-slug="bookSlug"
                :current-user-id="currentUserId"
                @show-notification="showNotification"
                @like-toggled="handleLikeToggled"
                @fact-deleted="handleFactDeleted"
                @fact-updated="handleFactUpdated"
            />
        </div>

        <div v-else class="text-center py-8">
            <svg
                class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                ></path>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-lg">Цікаві факти про цю книгу ще не додані</p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import FactCard from './FactCard.vue';

export default {
    name: 'FactsPageList',
    components: {
        FactCard,
    },
    props: {
        facts: {
            type: Array,
            default: () => [],
        },
        bookSlug: {
            type: String,
            required: true,
        },
        currentUserId: {
            type: Number,
            default: null,
        },
        isModerator: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            localFacts: [...this.facts],
            showAddForm: false,
            newFact: '',
            isSubmitting: false,
        };
    },
    computed: {
        isAuthenticated() {
            return this.currentUserId !== null;
        },
    },
    methods: {
        async addFact() {
            if (!this.newFact.trim()) return;

            this.isSubmitting = true;

            try {
                const response = await axios.post(`/books/${this.bookSlug}/facts`, {
                    content: this.newFact.trim(),
                });

                if (response.data.success) {
                    this.localFacts.unshift(response.data.fact);
                    this.newFact = '';
                    this.showAddForm = false;
                    this.showNotification('Факт успішно додано!', 'success');
                } else {
                    this.showNotification(response.data.message || 'Помилка при додаванні факту.', 'error');
                }
            } catch (error) {
                console.error('Error adding fact:', error);

                // Пытаемся показать конкретное сообщение валидации (наприклад, про мінімум 50 символів)
                let message = 'Помилка при додаванні факту.';
                if (error.response && error.response.status === 422 && error.response.data && error.response.data.errors) {
                    const errors = error.response.data.errors;
                    if (errors.content && errors.content.length) {
                        message = errors.content[0];
                    }
                }

                this.showNotification(message, 'error');
            }

            this.isSubmitting = false;
        },
        cancelAdd() {
            this.newFact = '';
            this.showAddForm = false;
        },
        handleFactDeleted(factId) {
            this.localFacts = this.localFacts.filter((fact) => fact.id !== factId);
        },
        handleFactUpdated(updatedFact) {
            const index = this.localFacts.findIndex((f) => f.id === updatedFact.id);
            if (index !== -1) {
                this.$set(this.localFacts, index, updatedFact);
            }
        },
        handleLikeToggled(data) {
            const fact = this.localFacts.find((f) => f.id === data.factId);
            if (fact) {
                fact.is_liked_by_current_user = data.isLiked;
                fact.likes_count = data.likesCount;
            }
        },
        showNotification(message, type) {
            this.$emit('show-notification', message, type);
        },
    },
};
</script>

<style scoped>
/* Вертикальна колонка без власного фону контейнера для окремої сторінки фактів */
</style>

{
  "cells": [],
  "metadata": {
    "language_info": {
      "name": "python"
    }
  },
  "nbformat": 4,
  "nbformat_minor": 2
}