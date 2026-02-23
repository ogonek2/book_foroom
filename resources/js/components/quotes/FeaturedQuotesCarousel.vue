<template>
    <div class="quotes-carousel">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-3xl font-bold text-light-text-primary dark:text-dark-text-primary">
                Цитати
            </h2>

            <div v-if="showNavigation" class="flex items-center space-x-3">
                <button
                    :class="[
                        'rounded-full p-2 shadow transition flex items-center justify-center bg-light-bg dark:bg-dark-bg-secondary text-light-text-secondary dark:text-dark-text-primary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary',
                        { 'opacity-50 cursor-not-allowed': isBeginning }
                    ]"
                    type="button"
                    aria-label="Попередня цитата"
                    :disabled="isBeginning"
                    @click="goPrev"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button
                    :class="[
                        'rounded-full p-2 shadow transition flex items-center justify-center bg-light-bg dark:bg-dark-bg-secondary text-light-text-secondary dark:text-dark-text-primary hover:bg-light-bg-secondary dark:hover:bg-dark-bg-secondary',
                        { 'opacity-50 cursor-not-allowed': isEnd }
                    ]"
                    type="button"
                    aria-label="Наступна цитата"
                    :disabled="isEnd"
                    @click="goNext"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="relative bg-transparent">
            <div
                class="overflow-hidden"
                ref="viewport"
                @pointerdown="onPointerDown"
                @pointermove="onPointerMove"
                @pointerup="onPointerUp"
                @pointercancel="onPointerCancel"
                @pointerleave="onPointerLeave"
            >
                <div
                    class="flex gap-6"
                    :style="trackStyle"
                    ref="track"
                >
                    <div
                        v-for="quote in quotes"
                        :key="quote.id"
                        class="flex-shrink-0 w-[260px] sm:w-72 md:w-80 lg:w-96"
                    >
                        <div
                            class="dark:bg-dark-bg-secondary bg-light-bg-secondary rounded-2xl p-6 h-full flex flex-col shadow-sm hover:shadow-md transition-shadow"
                        >
                            <div class="text-4xl text-orange-500/30 mb-4">
                                <svg data-v-3095836c="" width="32" height="14" viewBox="0 0 32 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path data-v-3095836c="" d="M6.9682 0.131836C8.63086 0.131836 10.0929 0.533169 11.3542 1.33584C12.6729 2.08117 13.4755 3.05584 13.7622 4.25983C14.3355 6.38117 13.9342 8.44517 12.5582 10.4518C11.2395 12.4012 9.4622 13.2898 7.2262 13.1178C8.71686 12.2578 9.3762 11.1972 9.2042 9.93583C8.45886 10.1078 7.71353 10.1938 6.9682 10.1938C5.01886 10.1938 3.3562 9.7065 1.9802 8.73183C0.661531 7.75717 0.00219727 6.58184 0.00219727 5.20584C0.00219727 3.7725 0.661531 2.5685 1.9802 1.59383C3.3562 0.619168 5.01886 0.131836 6.9682 0.131836ZM30.8762 4.25983C31.4495 6.38117 31.0769 8.44517 29.7582 10.4518C28.4395 12.4012 26.6335 13.2898 24.3402 13.1178C25.8309 12.2578 26.4902 11.1972 26.3182 9.93583C25.5729 10.1078 24.8275 10.1938 24.0822 10.1938C22.1329 10.1938 20.4702 9.7065 19.0942 8.73183C17.7755 7.75717 17.1162 6.58184 17.1162 5.20584C17.1162 3.7725 17.7755 2.5685 19.0942 1.59383C20.4702 0.619168 22.1329 0.131836 24.0822 0.131836C25.7449 0.131836 27.2069 0.533169 28.4682 1.33584C29.7869 2.08117 30.5895 3.05584 30.8762 4.25983Z" fill="#F97316"></path></svg>
                            </div>
                            <div class="mb-4">
                                <template v-if="quote.book && quote.book.slug">
                                    <a
                                        :href="`/books/${quote.book.slug}`"
                                        class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm hover:text-brand-500 dark:hover:text-brand-400 transition-colors truncate"
                                    >
                                        {{ quote.book.title || 'Без назви книги' }}
                                    </a>
                                </template>
                                <template v-else>
                                    <p class="text-light-text-tertiary dark:text-dark-text-terтиary text-sm truncate">
                                        {{ quote.book?.title || 'Без назви книги' }}
                                    </p>
                                </template>
                            </div>
                            <p class="text-light-text-secondary dark:text-dark-text-secondary text-lg italic leading-relaxed mb-4 flex-1">
                                {{ quote.content }}
                            </p>
                            <div class="flex items-center justify-between mt-auto">
                                <div class="flex items-start space-x-3">
                                    <template v-if="quote.user && quote.user.username">
                                        <a :href="`/users/${quote.user.username}`" class="inline-flex items-center space-x-3 group">
                                            <img v-if="userAvatar(quote.user)"
                                                 :src="userAvatar(quote.user)"
                                                 :alt="quote.user.name"
                                                 loading="lazy"
                                                 decoding="async"
                                                 width="40"
                                                 height="40"
                                                 class="w-10 h-10 rounded-full transition-transform duration-200 group-hover:scale-110">
                                            <div v-else
                                                 class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-light-text-primary dark:text-dark-text-primary font-semibold transition-transform duration-200 group-hover:scale-110">
                                                {{ initial(quote.user.name, quote.user.username) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-light-text-primary dark:text-dark-text-primary font-semibold group-hover:text-brand-500 dark:group-hover:text-brand-400 transition-colors truncate">
                                                    {{ quote.user.name }}
                                                </p>
                                                <p class="text-light-text-tertiary dark:text-dark-text-terтиary text-sm group-hover:text-brand-500 dark:group-hover:text-brand-400 transition-colors truncate">
                                                    {{ '@' + quote.user.username }}
                                                </p>
                                            </div>
                                        </a>
                                    </template>
                                    <template v-else>
                                        <div class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center text-gray-200 font-semibold">
                                            Г
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-light-text-primary dark:text-dark-text-primary font-semibold truncate">
                                                {{ quote.user?.name || 'Анонімний автор' }}
                                            </p>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex items-center space-x-2 text-orange-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ quote.likes_count || 0 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="isLoading" class="py-12 text-center text-light-text-tertiary dark:text-dark-text-terтіary">
                Завантаження цитат...
            </div>
            <div v-else-if="!quotes.length" class="py-12 text-center text-light-text-terтіary dark:text-dark-text-terтіary">
                Поки що немає цитат
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'FeaturedQuotesCarousel',
    props: {
        endpoint: {
            type: String,
            default: '/api/quotes/featured',
        },
        limit: {
            type: Number,
            default: 10,
        },
    },
    data() {
        return {
            quotes: [],
            isLoading: false,
            error: null,
            currentIndex: 0,
            cardWidth: 0,
            gap: 24,
            viewportWidth: 0,
            currentOffset: 0,
            isDragging: false,
            isPointerActive: false,
            hasDragged: false,
            dragStartX: 0,
            dragStartOffset: 0,
            suppressTransition: false,
        };
    },
    computed: {
        visibleCount() {
            if (!this.cardWidth) {
                return 1;
            }
            const step = this.cardWidth + this.gap;
            if (step <= 0) {
                return 1;
            }
            return Math.max(1, Math.floor((this.viewportWidth + this.gap) / step));
        },
        maxIndex() {
            return Math.max(0, this.quotes.length - this.visibleCount);
        },
        isBeginning() {
            return this.currentIndex <= 0;
        },
        isEnd() {
            return this.currentIndex >= this.maxIndex;
        },
        showNavigation() {
            return this.quotes.length > this.visibleCount;
        },
        showPagination() {
            return this.pageCount > 1;
        },
        pageCount() {
            return Math.max(1, this.maxIndex + 1);
        },
        trackStyle() {
            return {
                transform: `translateX(-${this.currentOffset}px)`
                    ,
                transition: this.isDragging || this.suppressTransition
                    ? 'none'
                    : 'transform 0.5s ease-out',
            };
        },
    },
    watch: {
        quotes() {
            this.updateMeasurements();
        },
    },
    mounted() {
        this.fetchQuotes();
        window.addEventListener('resize', this.handleResize);
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.handleResize);
    },
    methods: {
        async fetchQuotes() {
            this.isLoading = true;
            this.error = null;
            try {
                const response = await axios.get(this.endpoint, {
                    params: {
                        limit: this.limit,
                    },
                });
                this.quotes = response.data?.data ?? [];
                this.$nextTick(() => {
                    this.updateMeasurements();
                });
            } catch (error) {
                console.error('Failed to load featured quotes', error);
                this.error = 'Не вдалося завантажити цитати. Спробуйте пізніше.';
            } finally {
                this.isLoading = false;
            }
        },
        updateMeasurements() {
            this.$nextTick(() => {
                const viewport = this.$refs.viewport;
                const track = this.$refs.track;
                if (!viewport || !track || !track.children.length) {
                    return;
                }

                this.viewportWidth = viewport.clientWidth;
                const firstSlide = track.children[0];
                this.cardWidth = firstSlide.offsetWidth;

                const style = window.getComputedStyle(track);
                const gap = parseFloat(style.columnGap || style.gap || '0');
                this.gap = Number.isFinite(gap) ? gap : 0;

                const clampedIndex = Math.min(this.currentIndex, this.maxIndex);
                this.currentIndex = clampedIndex;

                this.suppressTransition = true;
                this.setOffset(clampedIndex * (this.cardWidth + this.gap));
                this.$nextTick(() => {
                    this.suppressTransition = false;
                });
            });
        },
        getScrollableDistance() {
            const viewport = this.$refs.viewport;
            const track = this.$refs.track;
            if (!viewport || !track) {
                return 0;
            }
            return Math.max(0, track.scrollWidth - viewport.clientWidth);
        },
        setOffset(offset) {
            const max = this.getScrollableDistance();
            this.currentOffset = Math.max(0, Math.min(offset, max));
        },
        goToIndex(index) {
            const clamped = Math.max(0, Math.min(index, this.maxIndex));
            this.currentIndex = clamped;
            const step = this.cardWidth + this.gap;
            this.setOffset(step > 0 ? clamped * step : 0);
        },
        goPrev() {
            this.goToIndex(this.currentIndex - 1);
        },
        goNext() {
            this.goToIndex(this.currentIndex + 1);
        },
        goToPage(pageIndex) {
            this.goToIndex(pageIndex);
        },
        onPointerDown(event) {
            if (event.pointerType === 'mouse' && event.button !== 0) {
                return;
            }
            this.isPointerActive = true;
            this.hasDragged = false;
            this.isDragging = false;
            this.dragStartX = event.clientX;
            this.dragStartOffset = this.currentOffset;
            this.suppressTransition = false;
        },
        onPointerMove(event) {
            if (!this.isPointerActive) {
                return;
            }
            const delta = event.clientX - this.dragStartX;
            if (!this.hasDragged && Math.abs(delta) > 6) {
                this.hasDragged = true;
                this.isDragging = true;
                this.suppressTransition = true;
            }
            if (!this.hasDragged) {
                return;
            }
            event.preventDefault();
            this.setOffset(this.dragStartOffset - delta);
        },
        finishDrag(event) {
            if (!this.isPointerActive) {
                return;
            }
            this.isPointerActive = false;
            const wasDragging = this.hasDragged;
            this.hasDragged = false;
            this.isDragging = false;
            this.suppressTransition = false;

            if (wasDragging) {
                const step = this.cardWidth + this.gap;
                if (step > 0) {
                    const index = Math.round(this.currentOffset / step);
                    this.goToIndex(index);
                } else {
                    this.goToIndex(0);
                }
            }
        },
        onPointerUp(event) {
            this.finishDrag(event);
        },
        onPointerCancel(event) {
            this.finishDrag(event);
        },
        onPointerLeave(event) {
            this.finishDrag(event);
        },
        handleResize() {
            this.updateMeasurements();
        },
        userAvatar(user) {
            if (!user) {
                return null;
            }

            return user.avatar_display
                || user.avatar_url
                || user.avatar
                || null;
        },
        initial(name, fallback) {
            const source = name || fallback || '';
            return source.charAt(0).toUpperCase();
        },
    },
};
</script>
