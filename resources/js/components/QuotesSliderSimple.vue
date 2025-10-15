<template>
    <div class="quotes-slider-container">
        <h2 class="text-3xl font-bold text-light-text-primary dark:text-dark-text-primary mb-6">Цитати</h2>
        <div class="relative">
            <div class="flex space-x-6 overflow-x-auto pb-4 scrollbar-hide">
                <div v-for="quote in quotes" :key="quote.id" class="flex-shrink-0 w-80">
                    <div class="bg-light-bg dark:bg-dark-bg-secondary rounded-lg p-6 hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-colors duration-200 h-full flex flex-col shadow-sm hover:shadow-md">
                        <div class="text-4xl text-orange-500/30 mb-4">"</div>
                        <blockquote class="text-light-text-secondary dark:text-dark-text-secondary text-lg italic leading-relaxed mb-4 flex-1" v-text="quote.content">
                        </blockquote>
                        
                        <!-- Page Number -->
                        <div v-if="quote.page_number" class="text-sm text-slate-500 dark:text-slate-400 mb-2">
                            Сторінка <span v-text="quote.page_number"></span>
                        </div>
                        
                        <div class="flex items-center justify-between mt-auto">
                            <div>
                                <p class="text-light-text-primary dark:text-dark-text-primary font-semibold" v-text="quote.user ? quote.user.name : 'Анонімний автор'">
                                </p>
                                <p class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm cursor-pointer hover:text-brand-500 transition-colors" 
                                   @click="goToBook(quote)" 
                                   v-text="quote.book_title || 'Без назви книги'">
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- Like Button -->
                                <button @click.stop="toggleLike(quote)" 
                                        class="flex items-center space-x-1 text-orange-500 hover:text-orange-400 transition-colors">
                                    <i :class="['fas fa-heart', quote.is_liked ? 'text-red-500' : '']"></i>
                                    <span class="text-sm" v-text="quote.likes_count || 0"></span>
                                </button>
                                
                                <!-- Share Button -->
                                <button @click.stop="shareQuote(quote)" 
                                        class="text-slate-600 dark:text-slate-400 hover:text-blue-500 transition-colors">
                                    <i class="fas fa-share-alt"></i>
                                </button>
                                
                                <!-- Go to Book Button -->
                                <button @click.stop="goToBook(quote)" 
                                        class="text-slate-600 dark:text-slate-400 hover:text-green-500 transition-colors">
                                    <i class="fas fa-book"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Empty State -->
                <div v-if="quotes.length === 0" class="flex-shrink-0 w-full text-center py-8">
                    <p class="text-light-text-tertiary dark:text-dark-text-tertiary">Поки що немає цитат</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'QuotesSliderSimple',
    props: {
        quotes: {
            type: Array,
            required: true,
            default: () => []
        },
        currentUserId: {
            type: Number,
            default: null
        }
    },
    methods: {
        async toggleLike(quote) {
            if (!this.currentUserId) {
                this.$emit('show-notification', 'Будь ласка, увійдіть, щоб поставити лайк.', 'error');
                return;
            }
            
            try {
                const response = await fetch(`/books/${quote.book_slug}/quotes/${quote.id}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    quote.is_liked = data.is_liked;
                    quote.likes_count = data.likes_count;
                    this.$emit('like-toggled', { quoteId: quote.id, isLiked: data.is_liked, likesCount: data.likes_count });
                } else {
                    this.$emit('show-notification', data.message || 'Помилка при зміні лайка.', 'error');
                }
            } catch (error) {
                console.error('Error toggling like:', error);
                this.$emit('show-notification', 'Помилка при зміні лайка.', 'error');
            }
        },
        
        shareQuote(quote) {
            const text = `"${quote.content}" — ${quote.user?.name || 'Користувач'}`;
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
        
        goToBook(quote) {
            if (quote.book_slug) {
                window.location.href = `/books/${quote.book_slug}`;
            } else {
                this.$emit('show-notification', 'Посилання на книгу недоступне.', 'error');
            }
        }
    }
};
</script>
