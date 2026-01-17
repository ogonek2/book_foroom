<template>
    <div>
        <div class="lg:bg-white/80 lg:dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl lg:shadow-xl lg:border border-white/20 dark:border-slate-700/30 reviews-list-container">
            <div class="lg:p-8 border-b pb-4 lg:pb-0 border-slate-200/30 dark:border-slate-700/30 reviews-list-container-header">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 dark:text-white">Рецензії</h2>
                        <p class="text-slate-600 dark:text-slate-400 mt-2 font-medium" v-if="!hideHeader">Поділіться своєю думкою про книгу
                        </p>
                    </div>
                    <div v-if="!hideHeader">
                        <div v-if="isAuthenticated && !hideAddButton">
                            <div v-if="lastReviewInfo && cooldownSeconds > 0" 
                                 class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl px-4 py-3 inline-block">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-clock text-orange-500 dark:text-orange-400"></i>
                                    <span class="text-sm text-orange-700 dark:text-orange-300 font-medium">
                                        Ви можете написати наступну рецензію через: 
                                        <span class="font-bold">{{ formattedCooldownTime }}</span>
                                    </span>
                                </div>
                            </div>
                            <div v-else-if="canWriteReview">
                                <a v-if="bookSlug" :href="`/books/${bookSlug}/reviews/create`"
                                    class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-2 rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 inline-block">
                                    <i class="fas fa-plus mr-2"></i>
                                    Написати рецензію
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="lg:px-8 py-4">
                <div v-if="localReviews.length > 0" class="space-y-8">
                    <div v-for="review in localReviews" :key="review.id"
                        class="review-item rounded-2xl bg-white dark:bg-slate-800 shadow-lg border border-slate-200 dark:border-slate-700 p-6 mb-6 hover:shadow-xl transition-all duration-300 cursor-pointer"
                        @click="goToReview(review.id)">
                        <!-- Review Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <template v-if="!review.is_guest && review.user && review.user.username">
                                    <a :href="profileUrl(review.user.username)"
                                        class="flex items-center space-x-4 group"
                                        @click.stop>
                                        <div>
                                            <img v-if="review.user.avatar_display"
                                                :src="review.user.avatar_display"
                                                :alt="review.user.name"
                                                class="w-10 h-10 rounded-full border-2 border-slate-200 dark:border-slate-700 transition-transform duration-200 group-hover:scale-105">
                                            <div v-else
                                                class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center text-white font-bold text-lg transition-transform duration-200 group-hover:scale-105">
                                                {{ (review.user?.name || 'U').charAt(0).toUpperCase() }}
                                            </div>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-slate-900 dark:text-white group-hover:text-brand-500 dark:group-hover:text-brand-400 transition-colors">
                                                {{ review.user?.name || 'Користувач' }}
                                            </h4>
                                            <p v-if="review.user?.username"
                                                class="text-sm text-slate-500 dark:text-slate-400 group-hover:text-brand-500 dark:group-hover:text-brand-400 transition-colors">
                                                {{ '@' + review.user.username }}
                                            </p>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                                {{ formatDate(review.created_at) }}
                                            </p>
                                        </div>
                                    </a>
                                </template>
                                <template v-else>
                                    <template v-if="review.user && review.user.avatar_display">
                                        <img :src="review.user.avatar_display"
                                            :alt="review.user.name"
                                            class="w-10 h-10 rounded-full border-2 border-slate-200 dark:border-slate-700">
                                    </template>
                                    <div v-else-if="review.is_guest"
                                        class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-lg">
                                        Г
                                    </div>
                                    <div v-else-if="review.user"
                                        class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center text-white font-bold text-lg">
                                        {{ (review.user.name || 'U').charAt(0).toUpperCase() }}
                                    </div>
                                    <div class="ml-0">
                                        <h4 class="font-semibold text-slate-900 dark:text-white">
                                            {{ review.is_guest ? 'Гість' : (review.user?.name || 'Користувач') }}
                                        </h4>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            {{ formatDate(review.created_at) }}
                                        </p>
                                    </div>
                                </template>
                            </div>

                            <!-- Rating Stars and Opinion -->
                            <div v-if="review.rating" class="flex items-center space-x-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-yellow-400 text-2xl"><i class="fas fa-star"></i></span>
                                    <span class="text-lg font-bold text-slate-700 dark:text-slate-300">{{ review.rating }}/10</span>
                                </div>
                                <!-- Opinion Reaction -->
                                <opinion-type-icon :opinion-type="review.opinion_type" size="md"></opinion-type-icon>
                            </div>
                        </div>

                        <!-- Review Meta Info -->
                        <div v-if="review.review_type || review.book_type || review.language" class="flex items-center flex-wrap gap-2 py-2">
                            <span v-if="review.review_type" 
                                  :class="[
                                      'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium text-white border',
                                      review.review_type === 'review' 
                                          ? 'bg-blue-500/60 dark:bg-blue-600/60 border-blue-400 dark:border-blue-500' 
                                          : 'bg-purple-500/60 dark:bg-purple-600/60 border-purple-400 dark:border-purple-500'
                                  ]">
                                {{ review.review_type === 'review' ? 'Рецензія' : 'Відгук' }}
                            </span>
                            <span v-if="review.book_type" 
                                  class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-indigo-500/60 dark:bg-indigo-600/60 border border-indigo-400 dark:border-indigo-500 text-white">
                                <span v-if="review.book_type === 'paper'">Паперова</span>
                                <span v-else-if="review.book_type === 'electronic'">Електронна</span>
                                <span v-else-if="review.book_type === 'audio'">Аудіо</span>
                            </span>
                            <span v-if="review.language" 
                                  class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-emerald-500/60 dark:bg-emerald-600/60 border border-emerald-400 dark:border-emerald-500 text-white">
                                <span v-if="review.language === 'uk'">Українська</span>
                                <span v-else-if="review.language === 'en'">Англійська</span>
                                <span v-else-if="review.language === 'pl'">Польська</span>
                                <span v-else-if="review.language === 'de'">Німецька</span>
                                <span v-else-if="review.language === 'fr'">Французька</span>
                                <span v-else-if="review.language === 'es'">Іспанська</span>
                                <span v-else-if="review.language === 'it'">Італійська</span>
                                <span v-else-if="review.language === 'ru'">російська</span>
                                <span v-else-if="review.language === 'cs'">Чеська</span>
                                <span v-else-if="review.language === 'sk'">Словацька</span>
                                <span v-else-if="review.language === 'hu'">Угорська</span>
                                <span v-else-if="review.language === 'ro'">Румунська</span>
                                <span v-else-if="review.language === 'bg'">Болгарська</span>
                                <span v-else-if="review.language === 'lt'">Литовська</span>
                                <span v-else-if="review.language === 'pt'">Португальська</span>
                                <span v-else-if="review.language === 'nl'">Нідерландська</span>
                                <span v-else-if="review.language === 'sv'">Шведська</span>
                                <span v-else-if="review.language === 'no'">Норвезька</span>
                                <span v-else-if="review.language === 'da'">Данська</span>
                                <span v-else-if="review.language === 'fi'">Фінська</span>
                                <span v-else-if="review.language === 'ja'">Японська</span>
                                <span v-else-if="review.language === 'ko'">Корейська</span>
                                <span v-else-if="review.language === 'zh'">Китайська</span>
                                <span v-else>{{ review.language }}</span>
                            </span>
                        </div>

                        <!-- Review Text -->
                        <div class="mb-4">
                            <!-- Spoiler Content -->
                            <div v-if="review.contains_spoiler" class="relative">
                                <!-- Blurred text background -->
                                <div class="relative overflow-hidden">
                                    <p
                                        class="text-lg text-slate-700 dark:text-slate-300 leading-relaxed line-clamp-3 blur-sm filter break-words"
                                        style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                                        {{ stripHTML(review.content) }}
                                    </p>
                                    <!-- Dark overlay -->
                                    <div class="absolute inset-0 bg-opacity-70 flex items-center justify-center">
                                        <div class="text-center">
                                            <p class="text-white text-lg font-bold mb-4">Рецензія містить спойлер</p>
                                            <button @click="goToReview(review.id)"
                                                class="bg-white text-black px-6 py-2 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                                                Читати
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Normal Content -->
                            <div v-else>
                                <p
                                    class="text-lg text-slate-700 dark:text-slate-300 leading-relaxed font-medium line-clamp-3 break-words"
                                    style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                                    {{ truncateText(stripHTML(review.content), 300) }}
                                </p>
                                <p v-if="stripHTML(review.content).length > 300"
                                    class="text-indigo-600 dark:text-indigo-400 text-sm font-medium mt-2 cursor-pointer hover:text-indigo-700 dark:hover:text-indigo-300"
                                    @click="goToReview(review.id)">
                                    Розгорнути ↓
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <!-- Like Button -->
                                <button @click.stop="toggleLike(review.id)"
                                    :class="['flex items-center space-x-2 px-4 py-2 rounded-xl font-semibold transition-all duration-200',
                                        review.is_liked ? 'text-red-500 bg-red-50 dark:bg-red-900/20' : 'text-slate-600 dark:text-slate-400 hover:text-red-500 hover:bg-slate-100 dark:hover:bg-slate-700']">
                                    <svg class="w-5 h-5" :fill="review.is_liked ? 'currentColor' : 'none'"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span>{{ review.likes_count || 0 }}</span>
                                </button>

                                <!-- Comments Button -->
                                <button @click.stop="goToReview(review.id)"
                                    class="flex items-center space-x-2 px-4 py-2 rounded-xl font-semibold text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <span>{{ review.replies_count || 0 }}</span>
                                </button>

                                <!-- Favorite Button -->
                                <button v-if="isAuthenticated" @click.stop="toggleFavorite(review.id)"
                                    :class="['flex items-center space-x-2 px-4 py-2 rounded-xl font-semibold transition-all duration-200', review.is_favorited ? 'text-yellow-500 bg-yellow-50 dark:bg-yellow-900/20' : 'text-slate-600 dark:text-slate-400 hover:text-yellow-500 hover:bg-slate-100 dark:hover:bg-slate-700']">
                                    <svg class="w-5 h-5" :fill="review.is_favorited ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                </button>

                                <!-- Share Button -->
                                <button @click.stop="shareReview(review)"
                                    class="flex items-center space-x-2 px-4 py-2 rounded-xl font-semibold text-slate-600 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                </button>

                                <!-- Report Button (Three dots menu) -->
                                <div class="relative">
                                    <button @click.stop="toggleMenu(review.id)"
                                        class="px-3 py-2 rounded-xl text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-200">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>
                                    <div v-if="activeMenu === review.id"
                                        class="absolute right-0 top-full mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl z-10 min-w-48">
                                        <div class="py-2">
                                            <button v-if="isAuthenticated && review.user_id === currentUserId"
                                                @click.stop="editReview(review)"
                                                class="block w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">
                                                <i class="fas fa-edit mr-2"></i> Редагувати
                                            </button>
                                            <button v-if="isAuthenticated && review.user_id === currentUserId"
                                                @click.stop="deleteReview(review.id)"
                                                class="block w-full text-left px-4 py-2 text-sm text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                <i class="fas fa-trash mr-2"></i> Видалити
                                            </button>
                                            <!-- Report Button -->
                                            <button @click.stop="openReportModal(review)"
                                                class="block w-full text-left px-4 py-2 text-sm text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                <i class="fas fa-flag mr-2"></i> Поскаржитись
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-20">
                        <div
                            class="w-32 h-32 mx-auto mb-8 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 dark:text-white mb-4">Поки немає рецензій</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-xl font-medium">Станьте першим, хто поділиться
                            своєю думкою про цю
                            книгу</p>
                    </div>
                </div>
            </div>

            <!-- Report Modal -->
            <report-modal v-if="reportData" :show="showReportModal" :reportable-type="reportData.type"
                :reportable-id="reportData.id"
                :content-preview="reportData.content" :content-url="reportData.url"
                @close="closeReportModal">
            </report-modal>
        </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ReviewsList',
    props: {
        reviews: {
            type: Array,
            default: () => []
        },
        bookSlug: {
            type: String,
            default: ''
        },
        currentUserId: {
            type: Number,
            default: null
        },
        userReview: {
            type: Object,
            default: null
        },
        lastReviewInfo: {
            type: Object,
            default: null
        },
        hideHeader: {
            type: Boolean,
            default: false
        },
        hideAddButton: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            localReviews: [...this.reviews],
            activeMenu: null,
            showReportModal: false,
            reportData: null,
            cooldownSeconds: 0,
            cooldownTimer: null
        };
    },
    mounted() {
        // Debug: проверяем наличие opinion_type в данных
        if (this.localReviews.length > 0) {
            console.log('Reviews data sample:', this.localReviews[0]);
            console.log('Has opinion_type:', this.localReviews.some(r => r.opinion_type));
        }
        // Слушаем глобальные события для обновления списка рецензий
        window.addEventListener('review-added', (event) => {
            this.handleReviewAdded(event.detail);
        });
        
        // Debug: проверяем данные о cooldown
        console.log('ReviewsList mounted:', {
            lastReviewInfo: this.lastReviewInfo,
            cooldownSeconds: this.cooldownSeconds,
            canWriteReview: this.canWriteReview,
            remaining_seconds: this.lastReviewInfo ? this.lastReviewInfo.remaining_seconds : 'N/A'
        });
        
        // Проверяем, что watch отработал
        this.$nextTick(() => {
            console.log('After nextTick:', {
                cooldownSeconds: this.cooldownSeconds,
                canWriteReview: this.canWriteReview
            });
        });
    },
    watch: {
        lastReviewInfo: {
            immediate: true,
            handler(newVal) {
                if (newVal && newVal.remaining_seconds) {
                    this.cooldownSeconds = parseInt(newVal.remaining_seconds);
                    if (this.cooldownSeconds > 0) {
                        this.$nextTick(() => {
                            this.startCooldownTimer();
                        });
                    }
                } else {
                    this.cooldownSeconds = 0;
                    if (this.cooldownTimer) {
                        clearInterval(this.cooldownTimer);
                        this.cooldownTimer = null;
                    }
                }
            }
        }
    },
    beforeDestroy() {
        if (this.cooldownTimer) {
            clearInterval(this.cooldownTimer);
        }
    },
    computed: {
        isAuthenticated() {
            return this.currentUserId !== null;
        },
        canWriteReview() {
            // Если нет информации о последней рецензии, можно писать
            if (!this.lastReviewInfo || !this.lastReviewInfo.remaining_seconds) {
                return true;
            }
            // Используем cooldownSeconds (который обновляется через watch и таймер)
            // Если cooldown истек (меньше или равно 0), можно писать
            return this.cooldownSeconds <= 0;
        },
        formattedCooldownTime() {
            if (this.cooldownSeconds <= 0) {
                return '0 хв';
            }
            const hours = Math.floor(this.cooldownSeconds / 3600);
            const minutes = Math.floor((this.cooldownSeconds % 3600) / 60);
            const seconds = this.cooldownSeconds % 60;
            
            if (hours > 0) {
                return `${hours} год. ${minutes} хв.`;
            } else if (minutes > 0) {
                return `${minutes} хв. ${seconds} сек.`;
            } else {
                return `${seconds} сек.`;
            }
        }
    },
    methods: {
        startCooldownTimer() {
            if (this.cooldownTimer) {
                clearInterval(this.cooldownTimer);
            }
            
            this.cooldownTimer = setInterval(() => {
                if (this.cooldownSeconds > 0) {
                    this.cooldownSeconds--;
                } else {
                    clearInterval(this.cooldownTimer);
                    this.cooldownTimer = null;
                }
            }, 1000);
        },
        getBookSlug(review) {
            // Используем bookSlug из prop, если он передан, иначе используем slug из review
            if (this.bookSlug) {
                return this.bookSlug;
            }
            // Пробуем разные варианты получения slug книги
            if (review && review.book_slug) {
                return review.book_slug;
            }
            if (review && review.book && review.book.slug) {
                return review.book.slug;
            }
            return '';
        },
        openReportModal(review) {
            const plainText = this.stripHTML(review.content);
            this.reportData = {
                type: 'App\\Models\\Review',
                id: review.id,
                content: this.truncateText(plainText, 200) + (plainText.length > 200 ? '...' : ''),
                url: window.location.href
            };
            this.showReportModal = true;
            this.activeMenu = null; // Закрываем меню
        },

        closeReportModal() {
            this.showReportModal = false;
            this.reportData = null;
        },

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
        stripHTML(html) {
            if (!html) return '';
            const temp = document.createElement('div');
            temp.innerHTML = html;
            return temp.textContent || temp.innerText || '';
        },
        truncateText(text, length) {
            if (!text) return '';
            if (text.length <= length) return text;
            return text.substring(0, length);
        },
        goToReview(reviewId) {
            const review = this.localReviews.find(r => r.id === reviewId);
            const bookSlug = this.getBookSlug(review || {});
            if (!bookSlug) {
                console.error('Cannot determine book slug for review:', reviewId);
                return;
            }
            window.location.href = `/books/${bookSlug}/reviews/${reviewId}`;
        },
        async toggleLike(reviewId) {
            if (!this.isAuthenticated) {
                return;
            }
            try {
                const review = this.localReviews.find(r => r.id === reviewId);
                const bookSlug = this.getBookSlug(review || {});
                if (!bookSlug) {
                    console.error('Cannot determine book slug for review:', reviewId);
                    return;
                }
                const response = await axios.post(`/books/${bookSlug}/reviews/${reviewId}/like`);
                if (response.data.success) {
                    if (review) {
                        this.$set(review, 'is_liked', response.data.is_liked);
                        this.$set(review, 'likes_count', response.data.likes_count);
                    }
                }
            } catch (error) {
                console.error('Error toggling like:', error);
            }
        },
        async shareReview(review) {
            const plainText = this.stripHTML(review.content);
            const text = this.truncateText(plainText, 200) + (plainText.length > 200 ? '...' : '');
            const bookSlug = this.getBookSlug(review);
            const url = review.url || (bookSlug ? `${window.location.origin}/books/${bookSlug}/reviews/${review.id}` : window.location.href);
            
            const { shareContent } = await import('../../utils/shareHelper');
            await shareContent({
                title: 'Рецензія на книгу',
                text: text,
                url: url
            });
        },
        toggleMenu(reviewId) {
            this.activeMenu = this.activeMenu === reviewId ? null : reviewId;
        },
        editReview(review) {
            // Перенаправляем на страницу редактирования
            const bookSlug = this.getBookSlug(review);
            if (!bookSlug) {
                this.showNotification('Помилка: не вдалося визначити книгу.', 'error');
                return;
            }
            window.location.href = `/books/${bookSlug}/reviews/${review.id}/edit`;
            this.activeMenu = null; // Закрываем меню
        },
        async deleteReview(reviewId) {
            const confirmed = await confirm('Ви впевнені, що хочете видалити цю рецензію?', 'Підтвердження', 'warning');
            if (!confirmed) return;

            try {
                const review = this.localReviews.find(r => r.id === reviewId);
                const bookSlug = this.getBookSlug(review || {});
                if (!bookSlug) {
                    this.showNotification('Помилка: не вдалося визначити книгу.', 'error');
                    return;
                }
                const response = await axios.delete(`/books/${bookSlug}/reviews/${reviewId}`);
                if (response.data.success) {
                    this.showNotification('Рецензію видалено!', 'success');
                    // Удаляем рецензию из локального списка
                    this.localReviews = this.localReviews.filter(r => r.id !== reviewId);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    this.showNotification(response.data.message || 'Помилка при видаленні рецензії.', 'error');
                }
            } catch (error) {
                console.error('Error deleting review:', error);
                this.showNotification('Помилка при видаленні рецензії.', 'error');
            }
            this.activeMenu = null; // Закрываем меню
        },
        async reportReview(reviewId) {
            const confirmed = await confirm('Ви впевнені, що хочете поскаржитись на цю рецензію?', 'Підтвердження', 'warning');
            if (!confirmed) return;

            const reason = await prompt('Вкажіть причину скарги (необов\'язково):', '', 'Причина скарги', 'Введіть причину...');

            try {
                const review = this.localReviews.find(r => r.id === reviewId);
                const bookSlug = this.getBookSlug(review || {});
                if (!bookSlug) {
                    this.showNotification('Помилка: не вдалося визначити книгу.', 'error');
                    return;
                }
                const response = await axios.post(`/books/${bookSlug}/reviews/${reviewId}/report`, {
                    reason: reason
                });
                if (response.data.success) {
                    this.showNotification('Дякуємо за повідомлення. Модератори розглянуть вашу скаргу.', 'success');
                } else {
                    this.showNotification(response.data.message || 'Помилка при відправці скарги.', 'error');
                }
            } catch (error) {
                console.error('Error reporting review:', error);
                this.showNotification('Помилка при відправці скарги.', 'error');
            }
            this.activeMenu = null;
        },
        async deleteUserReview() {
            const confirmed = await confirm('Ви впевнені, що хочете видалити свою рецензію?', 'Підтвердження', 'warning');
            if (!confirmed) return;

            try {
                const bookSlug = this.getBookSlug(this.userReview);
                if (!bookSlug) {
                    this.showNotification('Помилка: не вдалося визначити книгу.', 'error');
                    return;
                }
                const response = await axios.delete(`/books/${bookSlug}/reviews/${this.userReview.id}`);
                if (response.data.success) {
                    this.showNotification('Рецензію видалено!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    this.showNotification(response.data.message || 'Помилка при видаленні рецензії.', 'error');
                }
            } catch (error) {
                console.error('Error deleting review:', error);
                this.showNotification('Помилка при видаленні рецензії.', 'error');
            }
        },
        editUserReview() {
            // Перенаправляем на страницу редактирования
            const bookSlug = this.getBookSlug(this.userReview);
            if (!bookSlug) {
                this.showNotification('Помилка: не вдалося визначити книгу.', 'error');
                return;
            }
            window.location.href = `/books/${bookSlug}/reviews/${this.userReview.id}/edit`;
        },
        openReviewModal() {
            // Вызываем метод модального Vue приложения
            if (window.modalApp && window.modalApp.showAddReviewModal) {
                window.modalApp.showAddReviewModal();
            }
        },
        handleReviewAdded(newReview) {
            this.localReviews.unshift(newReview);
        },
        async toggleFavorite(reviewId) {
            if (!this.isAuthenticated) {
                return;
            }
            try {
                const review = this.localReviews.find(r => r.id === reviewId);
                const bookSlug = this.getBookSlug(review || {});
                if (!bookSlug) {
                    console.error('Cannot determine book slug for review:', reviewId);
                    return;
                }
                const response = await axios.post(`/books/${bookSlug}/reviews/${reviewId}/favorite`);
                if (response.data.success) {
                    if (review) {
                        this.$set(review, 'is_favorited', response.data.is_favorited);
                    }
                }
            } catch (error) {
                console.error('Error toggling favorite:', error);
            }
        },
        profileUrl(username) {
            return username ? `/users/${username}` : '#';
        },
        showNotification(message, type = 'info') {
            this.$emit('show-notification', message, type);
        }
    },
    mounted() {
        // Close menu on click outside
        this.handleClickOutside = (e) => {
            // Перевіряємо, чи клік не всередині меню
            if (this.$el && !this.$el.contains(e.target)) {
                this.activeMenu = null;
            }
        };
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeDestroy() {
        if (this.handleClickOutside) {
            document.removeEventListener('click', this.handleClickOutside);
        }
        if (this.cooldownTimer) {
            clearInterval(this.cooldownTimer);
        }
    }
};
</script>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
