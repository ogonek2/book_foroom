<template>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                Цитати
            </h3>
            <button v-if="isAuthenticated" 
                    @click="openQuoteModal"
                    class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-2 rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>
                Додати цитату
            </button>
        </div>

        <div v-if="localQuotes.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
            <quote-card v-for="quote in localQuotes"
                        :key="quote.id"
                        :quote="quote"
                        :book-slug="bookSlug"
                        :current-user-id="currentUserId"
                        @like-toggled="handleLikeToggled"
                        @quote-deleted="handleQuoteDeleted"
                        @quote-updated="handleQuoteUpdated"
                        @show-notification="showNotification" />
        </div>
        <div v-else class="text-center py-20">
            <div class="w-32 h-32 mx-auto mb-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                </svg>
            </div>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4">Поки немає цитат</h3>
            <p class="text-gray-500 dark:text-gray-400 text-xl font-medium">Станьте першим, хто додасть улюблену цитату з книги</p>
        </div>
    </div>
</template>

<script>
import QuoteCard from './QuoteCard.vue';
import axios from 'axios';

export default {
    name: 'QuotesList',
    components: {
        QuoteCard
    },
    props: {
        quotes: {
            type: [Array, Object],
            default: () => []
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
            localQuotes: Array.isArray(this.quotes) ? [...this.quotes] : Object.values(this.quotes || {}),
        };
    },
    computed: {
        totalQuotesCount() {
            return this.localQuotes.length;
        },
        isAuthenticated() {
            return this.currentUserId !== null;
        }
    },
    mounted() {
        // Слушаем глобальные события для обновления списка цитат
        window.addEventListener('quote-added', (event) => {
            this.handleQuoteAdded(event.detail);
        });
    },
    methods: {
        openQuoteModal() {
            // Вызываем метод модального Vue приложения
            if (window.modalApp && window.modalApp.showAddQuoteModal) {
                window.modalApp.showAddQuoteModal();
            }
        },
        handleQuoteAdded(newQuote) {
            this.localQuotes.unshift(newQuote);
        },
        handleQuoteDeleted(quoteId) {
            this.localQuotes = this.localQuotes.filter(q => q.id !== quoteId);
        },
        handleQuoteUpdated(updatedQuote) {
            const index = this.localQuotes.findIndex(q => q.id === updatedQuote.id);
            if (index !== -1) {
                this.$set(this.localQuotes, index, updatedQuote);
            }
        },
        handleLikeToggled(payload) {
            const quote = this.localQuotes.find(q => q.id === payload.quoteId);
            if (quote) {
                this.$set(quote, 'is_liked_by_current_user', payload.isLiked);
                this.$set(quote, 'likes_count', payload.likesCount);
            }
        },
        showNotification(message, type = 'info') {
            this.$emit('show-notification', message, type);
        }
    }
};
</script>

<style scoped>
/* Styles inherited from parent */
</style>

