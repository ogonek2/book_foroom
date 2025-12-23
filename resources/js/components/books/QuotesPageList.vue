<template>
    <div class="lg:px-4">
        <div v-if="localQuotes.length > 0" class="space-y-4">
            <quote-card
                v-for="quote in localQuotes"
                :key="quote.id"
                :quote="quote"
                :book-slug="bookSlug"
                :current-user-id="currentUserId"
                @like-toggled="handleLikeToggled"
                @quote-deleted="handleQuoteDeleted"
                @quote-updated="handleQuoteUpdated"
                @show-notification="showNotification"
            />
        </div>
        <div v-else class="text-center py-20">
            <div class="w-32 h-32 mx-auto mb-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"
                    />
                </svg>
            </div>
            <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-4">Поки немає цитат</h3>
            <p class="text-gray-500 dark:text-gray-400 text-xl font-medium">
                Станьте першим, хто додасть улюблену цитату з книги
            </p>
        </div>
    </div>
</template>

<script>
import QuoteCard from './QuoteCard.vue';

export default {
    name: 'QuotesPageList',
    components: {
        QuoteCard,
    },
    props: {
        quotes: {
            type: [Array, Object],
            default: () => [],
        },
        bookSlug: {
            type: String,
            default: '',
        },
        currentUserId: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            localQuotes: Array.isArray(this.quotes) ? [...this.quotes] : Object.values(this.quotes || {}),
        };
    },
    computed: {
        isAuthenticated() {
            return this.currentUserId !== null;
        },
    },
    methods: {
        handleQuoteDeleted(quoteId) {
            this.localQuotes = this.localQuotes.filter((q) => q.id !== quoteId);
        },
        handleQuoteUpdated(updatedQuote) {
            const index = this.localQuotes.findIndex((q) => q.id === updatedQuote.id);
            if (index !== -1) {
                this.$set(this.localQuotes, index, updatedQuote);
            }
        },
        handleLikeToggled(payload) {
            const quote = this.localQuotes.find((q) => q.id === payload.quoteId);
            if (quote) {
                this.$set(quote, 'is_liked_by_current_user', payload.isLiked);
                this.$set(quote, 'likes_count', payload.likesCount);
            }
        },
        showNotification(message, type = 'info') {
            this.$emit('show-notification', message, type);
        },
    },
};
</script>

<style scoped>
/* Вертикальна колонка без власного фону контейнера для окремої сторінки цитат */
</style>
