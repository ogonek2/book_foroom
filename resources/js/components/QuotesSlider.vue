<template>
    <div class="quotes-slider-container">
        <h2 class="text-3xl font-bold text-light-text-primary dark:text-dark-text-primary mb-6">Цитати</h2>
        <div class="relative">
            <!-- Slider -->
            <div ref="slider" class="quotes-slider flex space-x-6 overflow-x-auto pb-4 scrollbar-hide">
                <div v-for="quote in quotes" :key="quote.id" class="flex-shrink-0 w-80">
                    <div class="bg-light-bg dark:bg-dark-bg-secondary rounded-lg p-6 hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary transition-colors duration-200 h-full flex flex-col shadow-sm hover:shadow-md">
                        <!-- Quote Icon -->
                        <div class="text-4xl text-orange-500/30 mb-4">"</div>
                        
                        <!-- Quote Content -->
                        <blockquote class="text-light-text-secondary dark:text-dark-text-secondary text-lg italic leading-relaxed mb-4 flex-1" v-text="quote.content">
                        </blockquote>
                        
                        <!-- Page Number -->
                        <div v-if="quote.page_number" class="text-sm text-slate-500 dark:text-slate-400 mb-2">
                            Сторінка <span v-text="quote.page_number"></span>
                        </div>
                        
                        <!-- User Info and Actions -->
                        <div class="flex items-center justify-between mt-auto">
                            <div>
                                <p class="text-light-text-primary dark:text-dark-text-primary font-semibold" v-text="quote.user ? quote.user.name : 'Анонімний автор'">
                                </p>
                                <p class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm" v-text="quote.book_title || 'Без назви книги'">
                                </p>
                            </div>
                            
                            <!-- Actions -->
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
                                
                                <!-- Report Button -->
                                <button @click.stop="openReportModal(quote)" 
                                        class="text-slate-600 dark:text-slate-400 hover:text-red-500 transition-colors">
                                    <i class="fas fa-flag"></i>
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
            
            <!-- Navigation Arrows -->
            <button v-if="quotes.length > 1" 
                    ref="prevBtn"
                    @click="scrollLeft" 
                    class="quotes-slider-prev absolute left-0 top-1/2 transform -translate-y-1/2 bg-light-bg dark:bg-dark-bg-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary text-light-text-secondary dark:text-dark-text-primary p-2 rounded-full transition-colors duration-200 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            
            <button v-if="quotes.length > 1" 
                    ref="nextBtn"
                    @click="scrollRight" 
                    class="quotes-slider-next absolute right-0 top-1/2 transform -translate-y-1/2 bg-light-bg dark:bg-dark-bg-secondary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary text-light-text-secondary dark:text-dark-text-primary p-2 rounded-full transition-colors duration-200 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
        
        <!-- Report Modal will be handled by global ReportModal component -->
    </div>
</template>

<script>
export default {
    name: 'QuotesSlider',
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
    data() {
        return {
            isScrolling: false
        };
    },
    mounted() {
        this.initializeSlider();
        this.updateButtonStates();
    },
    methods: {
        initializeSlider() {
            const slider = this.$refs.slider;
            if (!slider) return;

            // Touch/swipe support
            let startX = 0;
            let scrollLeft = 0;

            slider.addEventListener('touchstart', (e) => {
                startX = e.touches[0].pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });

            slider.addEventListener('touchmove', (e) => {
                if (!this.isScrolling) return;
                e.preventDefault();
                const x = e.touches[0].pageX - slider.offsetLeft;
                const walk = (x - startX) * 2;
                slider.scrollLeft = scrollLeft - walk;
            });

            slider.addEventListener('touchend', () => {
                this.isScrolling = false;
            });

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    this.scrollLeft();
                } else if (e.key === 'ArrowRight') {
                    this.scrollRight();
                }
            });

            // Update button states on scroll
            slider.addEventListener('scroll', () => {
                this.updateButtonStates();
            });
        },
        
        scrollLeft() {
            const slider = this.$refs.slider;
            if (!slider) return;
            
            const cardWidth = 320; // w-80 = 20rem = 320px
            const scrollAmount = cardWidth * 2; // Scroll 2 cards at a time
            
            slider.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        },
        
        scrollRight() {
            const slider = this.$refs.slider;
            if (!slider) return;
            
            const cardWidth = 320; // w-80 = 20rem = 320px
            const scrollAmount = cardWidth * 2; // Scroll 2 cards at a time
            
            slider.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        },
        
        updateButtonStates() {
            const slider = this.$refs.slider;
            const prevBtn = this.$refs.prevBtn;
            const nextBtn = this.$refs.nextBtn;
            
            if (!slider || !prevBtn || !nextBtn) return;
            
            const isAtStart = slider.scrollLeft <= 0;
            const isAtEnd = slider.scrollLeft >= (slider.scrollWidth - slider.clientWidth);
            
            prevBtn.style.opacity = isAtStart ? '0.5' : '1';
            nextBtn.style.opacity = isAtEnd ? '0.5' : '1';
            
            prevBtn.disabled = isAtStart;
            nextBtn.disabled = isAtEnd;
        },
        
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
        
        async shareQuote(quote) {
            const { shareContent } = await import('../utils/shareHelper');
            const text = `"${quote.content}" — ${quote.user?.name || 'Користувач'}`;
            const url = quote.book?.slug ? `${window.location.origin}/books/${quote.book.slug}#quote-${quote.id}` : window.location.href;
            await shareContent({
                title: 'Цитата з книги',
                text: text,
                url: url
            });
        },
        
        openReportModal(quote) {
            // Use global ReportModal component
            this.$emit('open-report-modal', {
                reportableType: 'App\\Models\\Quote',
                reportableId: quote.id,
                contentPreview: this.getContentPreview(quote),
                contentUrl: this.getContentUrl(quote)
            });
        },
        
        getContentPreview(quote) {
            if (quote.content) {
                const text = quote.content;
                return text.substring(0, 100) + (text.length > 100 ? '...' : '');
            }
            return 'Цитата з книги';
        },
        
        getContentUrl(quote) {
            return `/books/${quote.book_slug || 'unknown'}#quote-${quote.id}`;
        }
    }
};
</script>

<style scoped>
.quotes-slider {
    scroll-behavior: smooth;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.quotes-slider-prev:disabled,
.quotes-slider-next:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.quotes-slider-prev:disabled:hover,
.quotes-slider-next:disabled:hover {
    background-color: inherit;
}
</style>
