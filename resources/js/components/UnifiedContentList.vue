<template>
    <div>
        <!-- Loading Skeletons -->
        <div v-if="initialLoading && filteredContent.length === 0" class="space-y-4">
            <div v-for="n in 5" :key="`skeleton-${n}`" 
                 class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="animate-pulse">
                    <!-- Header Skeleton -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 animate-shimmer"></div>
                            <div>
                                <div class="h-4 w-32 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded animate-shimmer mb-2"></div>
                                <div class="h-3 w-24 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded animate-shimmer"></div>
                            </div>
                        </div>
                        <div class="h-6 w-6 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded animate-shimmer"></div>
                    </div>
                    
                    <!-- Title Skeleton -->
                    <div class="h-6 w-3/4 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded animate-shimmer mb-3"></div>
                    
                    <!-- Content Skeleton -->
                    <div class="space-y-2 mb-4">
                        <div class="h-4 w-full bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded animate-shimmer"></div>
                        <div class="h-4 w-full bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded animate-shimmer"></div>
                        <div class="h-4 w-5/6 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded animate-shimmer"></div>
                    </div>
                    
                    <!-- Actions Skeleton -->
                    <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="h-5 w-16 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded animate-shimmer"></div>
                        <div class="h-5 w-16 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded animate-shimmer"></div>
                        <div class="h-5 w-16 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700 rounded animate-shimmer"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content List -->
        <transition-group name="fade-in" tag="div" class="space-y-4" v-if="filteredContent.length > 0">
            <div v-for="(item, index) in filteredContent" :key="`${item.type}-${item.id}-${index}`" 
                 :class="[
                     'bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border hover:shadow-md transition-shadow',
                     item.type === 'discussion' && item.is_pinned 
                         ? 'border-yellow-400 dark:border-yellow-500 border-1 bg-yellow-50/50 dark:bg-yellow-900/10' 
                         : 'border-gray-200 dark:border-gray-700'
                 ]">
                
                <!-- Pinned Badge for Discussions -->
                <div v-if="item.type === 'discussion' && item.is_pinned" 
                     class="flex items-center gap-2 mb-3 pb-3 border-b border-yellow-200 dark:border-yellow-800">
                    <span class="inline-flex items-center gap-1.5 bg-yellow-500 text-yellow-900 dark:bg-yellow-600 dark:text-yellow-100 px-3 py-1 rounded-full text-xs font-bold">
                        <i class="fas fa-thumbtack"></i>
                        Закріплено
                    </span>
                </div>
                
                <!-- Header with Avatar and Action -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <template v-if="item.user && item.user.username">
                            <a :href="profileUrl(item.user.username)"
                               class="flex items-center space-x-3 group">
                                <div>
                                    <img v-if="userAvatar(item.user)"
                                         :src="userAvatar(item.user)"
                                         :alt="item.user.name"
                                         class="w-10 h-10 rounded-full transition-transform duration-200 group-hover:scale-110">
                                    <div v-else
                                         class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-light-text-primary dark:text-dark-text-primary font-semibold transition-transform duration-200 group-hover:scale-110">
                                        {{ userInitial(item.user) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-light-text-primary dark:text-dark-text-primary font-medium group-hover:text-brand-500 dark:group-hover:text-brand-400 transition-colors">
                                        {{ item.user.name }}
                                    </div>
                                    <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm">
                                        {{ getActionText(item.type) }}
                                    </div>
                                </div>
                            </a>
                        </template>
                        <template v-else>
                            <div>
                                <img v-if="userAvatar(item.user)"
                                     :src="userAvatar(item.user)"
                                     :alt="item.user ? (item.user.name || item.user.username || 'Користувач') : 'Користувач'"
                                     class="w-10 h-10 rounded-full">
                                <div v-else
                                     class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                    <span>{{ userInitial(item.user) }}</span>
                                </div>
                            </div>
                            <div>
                                <div class="text-light-text-primary dark:text-dark-text-primary font-medium">
                                    {{ item.user ? item.user.name : 'Невідомий користувач' }}
                                </div>
                                <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm">
                                    {{ getActionText(item.type) }}
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <!-- Rating for Reviews -->
                    <div v-if="item.type === 'review'" class="flex items-center gap-2">
                        <div class="flex items-center bg-green-100 dark:bg-green-900/30 px-3 py-1 rounded-full">
                            <i class="fas fa-star text-green-600 dark:text-green-400 text-sm mr-1"></i>
                            <span class="text-green-700 dark:text-green-300 font-medium text-sm">{{ item.rating }}</span>
                        </div>
                        <!-- Opinion Reaction -->
                        <opinion-type-icon :opinion-type="item.opinion_type" size="sm"></opinion-type-icon>
                    </div>
                </div>

                <!-- Title (for Discussions) -->
                <h3 v-if="item.type === 'discussion'" 
                    class="text-lg font-bold text-light-text-primary dark:text-dark-text-primary mb-3 hover:text-brand-500 dark:hover:text-brand-400 transition-colors break-words"
                    style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                    <a :href="getItemUrl(item)">
                        {{ item.title }}
                    </a>
                </h3>

                <!-- Book Title (for Reviews) -->
                <h3 v-if="item.type === 'review' && item.book && item.book.slug" 
                    class="text-sm font-bold text-light-text-primary dark:text-brand-400/50 mb-3 hover:text-brand-500 dark:hover:text-brand-400 transition-colors break-words"
                    style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;">
                    <a :href="`/books/${item.book.slug}`">
                        {{ item.book.title }}
                    </a>
                </h3>

                <!-- Content Preview -->
                <div class="text-light-text-primary dark:text-dark-text-primary leading-relaxed mb-4 relative">
                    <div class="text-sm content-preview break-words" 
                         :class="{ 'content-fade': !isExpanded(item) && getTextLength(item.content) > 150 }"
                         style="word-break: break-word; overflow-wrap: break-word; hyphens: auto; -webkit-hyphens: auto; -ms-hyphens: auto;"
                         v-html="getProcessedContent(item)">
                    </div>
                    <div v-if="!isExpanded(item) && getTextLength(item.content) >250" 
                         class="absolute bottom-0 left-0 right-0 h-10 bg-gradient-to-t from-white dark:from-gray-800 to-transparent pointer-events-none">
                    </div>
                </div>

                <!-- Read More Button -->
                <div v-if="getTextLength(item.content) > 10" class="mb-4">
                    <a :href="getItemUrl(item)"
                       class="text-brand-500 dark:text-brand-400 hover:text-brand-600 dark:hover:text-brand-300 text-sm font-medium transition-colors">
                        Читати далі
                    </a>
                </div>

                <!-- Stats -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <!-- Likes -->
                        <button @click.stop="toggleLike(item)" 
                                type="button"
                                class="flex items-center space-x-2 text-light-text-tertiary dark:text-dark-text-tertiary hover:text-red-500 dark:hover:text-red-400 transition-colors cursor-pointer">
                            <i class="fas fa-heart" :class="{ 'text-red-500 dark:text-red-400': item.is_liked }"></i>
                            <span>{{ item.likes_count || 0 }}</span>
                        </button>

                        <!-- Comments/Replies -->
                        <a :href="getItemUrl(item) + '#replies-section'"
                           class="flex items-center space-x-2 text-light-text-tertiary dark:text-dark-text-tertiary hover:text-brand-500 dark:hover:text-brand-400 transition-colors">
                            <i class="fas fa-comments"></i>
                            <span>{{ item.replies_count || item.comments_count || 0 }}</span>
                        </a>

                        <!-- Share -->
                        <button @click.stop="shareItem(item)" 
                                type="button"
                                class="flex items-center space-x-2 text-light-text-tertiary dark:text-dark-text-tertiary hover:text-brand-500 dark:hover:text-brand-400 transition-colors cursor-pointer">
                            <i class="fas fa-share"></i>
                        </button>
                    </div>

                    <!-- Date and Actions -->
                    <div class="flex items-center space-x-3">
                        <div class="text-light-text-tertiary dark:text-dark-text-tertiary text-sm">
                            {{ formatDate(item.created_at) }}
                        </div>
                        
                        <!-- Report Button -->
                        <report-button 
                            :reportable-type="getReportableType(item)"
                            :reportable-id="item.id"
                            :content-preview="getContentPreview(item)"
                            :content-url="getItemUrl(item)">
                        </report-button>
                    </div>
                </div>

                <div v-if="item.top_comment"
                     class="mt-4 flex items-center gap-3 rounded-2xl border border-white/10 dark:border-white/5 bg-slate-50/70 dark:bg-white/5 px-4 py-3 shadow-inner">
                    <div class="flex-shrink-0">
                        <template v-if="item.top_comment.user && item.top_comment.user.username">
                            <a :href="profileUrl(item.top_comment.user.username)" class="inline-flex">
                                <img v-if="userAvatar(item.top_comment.user)"
                                     :src="userAvatar(item.top_comment.user)"
                                     :alt="item.top_comment.user.name"
                                     class="w-8 h-8 rounded-full ring-2 ring-white/60 dark:ring-white/10">
                                <div v-else
                                     class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xs font-semibold">
                                    {{ userInitial(item.top_comment.user) }}
                                </div>
                            </a>
                        </template>
                        <template v-else>
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xs font-semibold">
                                {{ userInitial(item.top_comment.user) }}
                            </div>
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-light-text-tertiary dark:text-dark-text-tertiary mb-1">
                            Топ-коментар
                        </p>
                        <p class="text-sm font-medium text-light-text-primary dark:text-dark-text-primary truncate">
                            <span v-if="item.top_comment.user && item.top_comment.user.username">
                                <a :href="profileUrl(item.top_comment.user.username)"
                                   class="text-brand-500 dark:text-brand-400 hover:underline mr-1">
                                    {{ item.top_comment.user.name }}
                                </a>
                            </span>
                            <span v-else class="mr-1">
                                {{ item.top_comment.user?.name || 'Користувач' }}
                            </span>
                            <span class="text-light-text-tertiary dark:text-dark-text-tertiary font-normal">
                                — “{{ item.top_comment.content }}”
                            </span>
                        </p>
                    </div>
                    <div class="flex items-center text-xs font-semibold text-pink-500 dark:text-pink-400">
                        <i class="fas fa-heart mr-1"></i>
                        {{ item.top_comment.likes_count || 0 }}
                    </div>
                </div>
            </div>
        </transition-group>

        <!-- Empty State -->
        <div v-if="!initialLoading && !loading && filteredContent.length === 0" class="text-center py-16">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 border border-gray-200 dark:border-gray-700 shadow-xl">
                <svg class="w-24 h-24 mx-auto mb-6 text-light-text-tertiary dark:text-dark-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 class="text-2xl font-bold text-light-text-primary dark:text-dark-text-primary mb-4">
                    Немає контенту
                </h3>
                <p class="text-light-text-secondary dark:text-dark-text-secondary">
                    Спробуйте змінити фільтри або створити новий контент
                </p>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div v-if="loading" class="mt-8 flex justify-center py-8">
            <div class="flex items-center gap-3 text-slate-500 dark:text-slate-400">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-500"></div>
                <span class="text-sm font-medium">Завантаження...</span>
            </div>
        </div>

        <!-- End of Content -->
        <div v-if="!hasMore && !loading && filteredContent.length > 0" class="mt-8 text-center py-8">
            <p class="text-slate-500 dark:text-slate-400 text-sm">Всі записи завантажено</p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'UnifiedContentList',
    props: {
        discussions: {
            type: Array,
            default: () => []
        },
        reviews: {
            type: Array,
            default: () => []
        },
        activeFilter: {
            type: String,
            default: 'all'
        },
        sortBy: {
            type: String,
            default: 'newest'
        }
    },
    data() {
        return {
            expandedItems: new Set(),
            itemLikes: {}, // Храним состояние лайков для каждого элемента
            allDiscussions: [],
            allReviews: [],
            currentPage: 1,
            perPage: 15, // Количество элементов на странице
            loading: false,
            initialLoading: true, // Флаг для первой загрузки
            hasMore: true,
            cacheKey: 'discussions_content_cache',
            scrollHandler: null
        };
    },
    mounted() {
        // Инициализируем данные из props
        const hasPropsData = (this.discussions && this.discussions.length > 0) || (this.reviews && this.reviews.length > 0);
        
        if (this.discussions && this.discussions.length > 0) {
            this.allDiscussions = [...this.discussions];
        }
        if (this.reviews && this.reviews.length > 0) {
            this.allReviews = [...this.reviews];
        }
        
        // Загружаем кешированные данные только если нет данных из props
        // Если есть данные из props, кеш не загружаем, чтобы избежать дубликатов
        if (!hasPropsData) {
            this.loadCachedData();
        }
        
        // Если есть данные из props или кеша, отключаем initialLoading
        if (this.allDiscussions.length > 0 || this.allReviews.length > 0) {
            this.initialLoading = false;
        }
        
        // Инициализируем состояние лайков для всех элементов
        this.initializeLikes();
        
        // Загружаем следующую страницу, если есть еще данные
        if (this.hasMore && (this.allDiscussions.length < this.perPage && this.allReviews.length < this.perPage)) {
            this.currentPage = 2; // Начинаем со второй страницы, так как первая уже загружена
            this.loadMore();
        } else if (this.initialLoading) {
            // Если нет данных, загружаем первую страницу
            this.loadMore();
        }
        
        // Добавляем обработчик скролла
        this.setupScrollListener();
    },
    beforeUnmount() {
        // Удаляем обработчик скролла
        if (this.scrollHandler) {
            window.removeEventListener('scroll', this.scrollHandler);
        }
    },
    watch: {
        activeFilter() {
            // При изменении фильтра сбрасываем страницу и загружаем заново
            this.currentPage = 1;
            this.hasMore = true;
            this.allDiscussions = [];
            this.allReviews = [];
            this.initialLoading = true;
            // Очищаем кеш
            localStorage.removeItem(this.cacheKey);
            this.loadMore();
        },
        sortBy() {
            // При изменении сортировки сбрасываем страницу и загружаем заново
            this.currentPage = 1;
            this.hasMore = true;
            this.allDiscussions = [];
            this.allReviews = [];
            this.initialLoading = true;
            // Очищаем кеш
            localStorage.removeItem(this.cacheKey);
            this.loadMore();
        }
    },
    computed: {
        // Объединяем и нормализуем данные
        normalizedContent() {
            const discussions = (this.allDiscussions.length > 0 ? this.allDiscussions : this.discussions).map(discussion => ({
                id: discussion.id,
                slug: discussion.slug || null,
                type: 'discussion',
                title: discussion.title,
                content: discussion.content,
                user: discussion.user,
                created_at: discussion.created_at,
                updated_at: discussion.updated_at,
                likes_count: this.getItemLikesCount(discussion.id, 'discussion'),
                replies_count: discussion.replies_count || 0,
                is_liked: this.getItemLiked(discussion.id, 'discussion'),
                is_pinned: discussion.is_pinned,
                is_closed: discussion.is_closed,
                url: `/discussions/${discussion.slug || discussion.id}`,
                top_comment: discussion.top_comment || null,
            }));

            const reviews = (this.allReviews.length > 0 ? this.allReviews : this.reviews).map(review => ({
                id: review.id,
                type: 'review',
                title: review.book?.title || 'Рецензія',
                content: review.content,
                user: review.user,
                created_at: review.created_at,
                updated_at: review.updated_at,
                likes_count: this.getItemLikesCount(review.id, 'review'),
                comments_count: review.replies_count || 0,
                is_liked: this.getItemLiked(review.id, 'review'),
                rating: review.rating,
                review_type: review.review_type || null,
                opinion_type: review.opinion_type || null,
                book: review.book,
                url: `/books/${review.book?.slug}/reviews/${review.id}`,
                top_comment: review.top_comment || null,
            }));

            return [...discussions, ...reviews].filter(item => item.user && item.user.id);
        },

        // Фильтруем контент
        filteredContent() {
            let content = this.normalizedContent;

            // Применяем фильтр типа
            if (this.activeFilter === 'discussions') {
                content = content.filter(item => item.type === 'discussion');
            } else if (this.activeFilter === 'reviews') {
                content = content.filter(item => item.type === 'review');
            }

            // Сортируем
            content = this.sortContent(content);
            
            return content;
        },

    },
    methods: {
        profileUrl(username) {
            return username ? `/users/${username}` : '#';
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
        userInitial(user) {
            if (!user) {
                return 'Н';
            }
            const source = user.name || user.username || 'Н';
            return source.charAt(0).toUpperCase();
        },
        getActionText(type) {
            return type === 'discussion' ? 'створив обговорення' : 'написав рецензію';
        },

        getItemUrl(item) {
            return item.url;
        },

        /**
         * Получает длину текста без HTML тегов
         */
        getTextLength(content) {
            if (!content) return 0;
            const div = document.createElement('div');
            div.innerHTML = content;
            return (div.textContent || div.innerText || '').length;
        },

        /**
         * Обрабатывает контент: удаляет опасные теги, оставляет только теги форматирования текста
         */
        stripDangerousTags(content) {
            if (!content) return '';
            
            const div = document.createElement('div');
            div.innerHTML = content;
            
            // Разрешенные теги для форматирования текста
            const allowedTags = ['b', 'i', 'u', 'strong', 'em', 'p', 'br', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'blockquote', 'code', 'pre'];
            
            // Удаляем все запрещенные теги (img, a, table, script и т.д.)
            const allElements = div.querySelectorAll('*');
            allElements.forEach(el => {
                if (!allowedTags.includes(el.tagName.toLowerCase())) {
                    // Заменяем элемент на его текстовое содержимое
                    const textNode = document.createTextNode(el.textContent || '');
                    el.parentNode.replaceChild(textNode, el);
                }
            });
            
            return div.innerHTML;
        },

        /**
         * Обрабатывает и форматирует контент для отображения
         */
        getProcessedContent(item) {
            if (!item.content) return '';
            
            // Удаляем опасные теги
            let content = this.stripDangerousTags(item.content);
            
            // Если контент не раскрыт и длина больше 150 символов
            if (!this.isExpanded(item) && this.getTextLength(content) > 150) {
                // Обрезаем по символам (не по HTML)
                const div = document.createElement('div');
                div.innerHTML = content;
                const text = div.textContent || div.innerText || '';
                
                if (text.length > 650) {
                    const truncatedText = text.substring(0, 650) + '...';
                    return this.escapeHtml(truncatedText);
                }
            }
            
            return content;
        },

        /**
         * Экранирует HTML специальные символы
         */
        escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        },

        toggleExpanded(item) {
            const key = `${item.type}-${item.id}`;
            if (this.expandedItems.has(key)) {
                this.expandedItems.delete(key);
            } else {
                this.expandedItems.add(key);
            }
        },

        isExpanded(item) {
            const key = `${item.type}-${item.id}`;
            return this.expandedItems.has(key);
        },

        sortContent(content) {
            const sorted = [...content];
            
            switch (this.sortBy) {
                case 'newest':
                    return sorted.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                case 'oldest':
                    return sorted.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                case 'popular':
                    return sorted.sort((a, b) => (b.likes_count + b.replies_count + b.comments_count) - (a.likes_count + a.replies_count + a.comments_count));
                case 'pinned':
                    // Сортировка закрепленных обсуждений (сначала закрепленные, потом остальные)
                    return sorted.sort((a, b) => {
                        const aPinned = a.is_pinned || false;
                        const bPinned = b.is_pinned || false;
                        
                        if (aPinned && !bPinned) return -1;
                        if (!aPinned && bPinned) return 1;
                        
                        // Если оба закреплены или оба не закреплены, сортируем по дате создания (новые сверху)
                        return new Date(b.created_at) - new Date(a.created_at);
                    });
                default:
                    return sorted;
            }
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'щойно';
            if (diffMins < 60) return `${diffMins} хв тому`;
            if (diffHours < 24) return `${diffHours} год тому`;
            if (diffDays < 7) return `${diffDays} дн тому`;

            return date.toLocaleDateString('uk-UA', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        initializeLikes() {
            // Инициализируем состояние лайков из props
            this.discussions.forEach(discussion => {
                const key = `discussion-${discussion.id}`;
                this.$set(this.itemLikes, key, {
                    is_liked: discussion.is_liked || false,
                    likes_count: discussion.likes_count || 0
                });
            });
            
            this.reviews.forEach(review => {
                const key = `review-${review.id}`;
                this.$set(this.itemLikes, key, {
                    is_liked: review.is_liked || false,
                    likes_count: review.likes_count || 0
                });
            });
        },

        getItemLiked(itemId, type) {
            const key = `${type}-${itemId}`;
            return this.itemLikes[key]?.is_liked || false;
        },

        getItemLikesCount(itemId, type) {
            const key = `${type}-${itemId}`;
            return this.itemLikes[key]?.likes_count || 0;
        },

        getReportableType(item) {
            // Возвращаем полное имя класса модели
            if (item.type === 'discussion') {
                return 'App\\Models\\Discussion';
            } else if (item.type === 'review') {
                return 'App\\Models\\Review';
            }
            return '';
        },

        getContentPreview(item) {
            // Возвращаем превью контента для жалобы
            if (item.content) {
                const div = document.createElement('div');
                div.innerHTML = item.content;
                const text = div.textContent || div.innerText || '';
                return text.substring(0, 100) + (text.length > 100 ? '...' : '');
            }
            return item.title || 'Контент';
        },

        async toggleLike(item) {
            console.log('Toggle like called for item:', item);
            try {
                let url;
                if (item.type === 'discussion') {
                    if (!item.slug) {
                        console.error('Discussion slug is missing:', item);
                        return;
                    }
                    url = `/discussions/${item.slug}/like`;
                } else {
                    if (!item.book || !item.book.slug) {
                        console.error('Book slug is missing for review:', item);
                        return;
                    }
                    url = `/books/${item.book.slug}/reviews/${item.id}/like`;
                }

                console.log('Like URL:', url);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                console.log('Response status:', response.status);
                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    // Обновляем локальное состояние лайков
                    const key = `${item.type}-${item.id}`;
                    this.$set(this.itemLikes, key, {
                        is_liked: data.liked || data.is_liked,
                        likes_count: data.likes_count
                    });
                    console.log('Like toggled successfully', item);
                }
            } catch (error) {
                console.error('Error toggling like:', error);
            }
        },

        async shareItem(item) {
            const { shareContent } = await import('../utils/shareHelper');
            const url = item.url ? (item.url.startsWith('http') ? item.url : window.location.origin + item.url) : window.location.href;
            
            // Strip HTML from content for sharing
            let shareText = '';
            if (item.content) {
                const div = document.createElement('div');
                div.innerHTML = item.content;
                shareText = (div.textContent || div.innerText || '').trim();
                if (shareText.length > 200) {
                    shareText = shareText.substring(0, 200) + '...';
                }
            }
            
            await shareContent({
                title: item.title || (item.type === 'review' ? 'Рецензія' : item.type === 'discussion' ? 'Обговорення' : 'Контент'),
                text: shareText,
                url: url
            });
        },

        // Загрузка данных с сервера
        async loadMore() {
            if (this.loading || !this.hasMore) return;
            
            this.loading = true;
            
            try {
                const params = new URLSearchParams({
                    filter: this.activeFilter,
                    sort: this.sortBy,
                    page: this.currentPage,
                    per_page: this.perPage
                });
                
                const response = await fetch(`/discussions?${params.toString()}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Failed to load content');
                }
                
                const data = await response.json();
                
                // Добавляем новые данные, избегая дубликатов
                if (data.discussions && data.discussions.length > 0) {
                    const existingIds = new Set(this.allDiscussions.map(d => d.id));
                    const newDiscussions = data.discussions.filter(d => !existingIds.has(d.id));
                    this.allDiscussions = [...this.allDiscussions, ...newDiscussions];
                }
                
                if (data.reviews && data.reviews.length > 0) {
                    const existingIds = new Set(this.allReviews.map(r => r.id));
                    const newReviews = data.reviews.filter(r => !existingIds.has(r.id));
                    this.allReviews = [...this.allReviews, ...newReviews];
                }
                
                // Проверяем, есть ли еще данные
                this.hasMore = data.has_more || false;
                
                // Кешируем данные
                this.cacheData();
                
                // Увеличиваем страницу для следующей загрузки
                if (this.hasMore) {
                    this.currentPage++;
                }
                
                // Отключаем флаг первой загрузки после получения данных
                if (this.initialLoading) {
                    this.initialLoading = false;
                }
                
            } catch (error) {
                console.error('Error loading content:', error);
                this.hasMore = false;
                this.initialLoading = false;
            } finally {
                this.loading = false;
            }
        },
        
        // Кеширование данных в localStorage
        cacheData() {
            try {
                const cacheData = {
                    discussions: this.allDiscussions,
                    reviews: this.allReviews,
                    filter: this.activeFilter,
                    sortBy: this.sortBy,
                    timestamp: Date.now()
                };
                
                localStorage.setItem(this.cacheKey, JSON.stringify(cacheData));
            } catch (error) {
                console.error('Error caching data:', error);
            }
        },
        
        // Загрузка кешированных данных
        loadCachedData() {
            try {
                const cached = localStorage.getItem(this.cacheKey);
                if (!cached) return;
                
                const cacheData = JSON.parse(cached);
                
                // Проверяем, актуальны ли кешированные данные (не старше 1 часа)
                const cacheAge = Date.now() - (cacheData.timestamp || 0);
                const maxAge = 60 * 60 * 1000; // 1 час
                
                if (cacheAge > maxAge) {
                    localStorage.removeItem(this.cacheKey);
                    return;
                }
                
                // Проверяем, совпадают ли фильтры
                if (cacheData.filter === this.activeFilter && cacheData.sortBy === this.sortBy) {
                    // Объединяем данные из кеша с данными из props, избегая дубликатов
                    const cachedDiscussions = cacheData.discussions || [];
                    const cachedReviews = cacheData.reviews || [];
                    
                    // Объединяем discussions без дубликатов
                    const existingDiscussionIds = new Set(this.allDiscussions.map(d => d.id));
                    const newDiscussions = cachedDiscussions.filter(d => !existingDiscussionIds.has(d.id));
                    this.allDiscussions = [...this.allDiscussions, ...newDiscussions];
                    
                    // Объединяем reviews без дубликатов
                    const existingReviewIds = new Set(this.allReviews.map(r => r.id));
                    const newReviews = cachedReviews.filter(r => !existingReviewIds.has(r.id));
                    this.allReviews = [...this.allReviews, ...newReviews];
                    
                    // Если есть кешированные данные, начинаем со следующей страницы
                    if (this.allDiscussions.length > 0 || this.allReviews.length > 0) {
                        this.currentPage = Math.floor((this.allDiscussions.length + this.allReviews.length) / this.perPage) + 1;
                    }
                }
            } catch (error) {
                console.error('Error loading cached data:', error);
                localStorage.removeItem(this.cacheKey);
            }
        },
        
        // Настройка обработчика скролла
        setupScrollListener() {
            this.scrollHandler = this.handleScroll.bind(this);
            window.addEventListener('scroll', this.scrollHandler, { passive: true });
        },
        
        // Обработчик скролла для infinite scroll
        handleScroll() {
            if (this.loading || !this.hasMore) return;
            
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            
            // Загружаем следующую страницу, когда пользователь прокрутил на 80% страницы
            if (scrollTop + windowHeight >= documentHeight * 0.8) {
                this.loadMore();
            }
        }
    }
};
</script>

<style scoped>
.content-preview {
    position: relative;
    overflow: hidden;
}

.content-fade {
    max-height: 8rem; /* Примерно 6-8 строк текста */
    overflow: hidden;
    position: relative;
}

/* Стили для форматирования текста внутри контента */
.content-preview >>> p {
    margin-bottom: 0.5em;
}

.content-preview >>> strong,
.content-preview >>> b {
    font-weight: 600;
}

.content-preview >>> em,
.content-preview >>> i {
    font-style: italic;
}

.content-preview >>> u {
    text-decoration: underline;
}

.content-preview >>> blockquote {
    border-left: 3px solid #e5e7eb;
    padding-left: 1rem;
    margin: 0.5rem 0;
    font-style: italic;
    color: #6b7280;
}

.dark .content-preview >>> blockquote {
    border-left-color: #4b5563;
    color: #9ca3af;
}

.content-preview >>> code {
    background-color: #f3f4f6;
    padding: 0.125rem 0.25rem;
    border-radius: 0.25rem;
    font-family: monospace;
    font-size: 0.875em;
}

.dark .content-preview >>> code {
    background-color: #374151;
}

.content-preview >>> ul,
.content-preview >>> ol {
    margin-left: 1.5rem;
    margin-bottom: 0.5em;
}

.content-preview >>> li {
    margin-bottom: 0.25em;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Shimmer animation for loading skeletons */
@keyframes shimmer {
    0% {
        background-position: -2000px 0;
    }
    100% {
        background-position: 2000px 0;
    }
}

.animate-shimmer {
    background-size: 2000px 100%;
    animation: shimmer 2s infinite linear;
    background-image: linear-gradient(
        90deg,
        var(--shimmer-start, #e5e7eb) 0%,
        var(--shimmer-mid, #f3f4f6) 20%,
        var(--shimmer-bright, #ffffff) 50%,
        var(--shimmer-mid, #f3f4f6) 80%,
        var(--shimmer-end, #e5e7eb) 100%
    );
}

.dark .animate-shimmer {
    --shimmer-start: #374151;
    --shimmer-mid: #4b5563;
    --shimmer-bright: #6b7280;
    --shimmer-end: #374151;
}

/* Fade-in animation for content */
.fade-in-enter-active {
    transition: opacity 0.5s ease-in, transform 0.5s ease-in;
}

.fade-in-leave-active {
    transition: opacity 0.3s ease-out, transform 0.3s ease-out;
}

.fade-in-enter-from {
    opacity: 0;
    transform: translateY(10px);
}

.fade-in-enter-to {
    opacity: 1;
    transform: translateY(0);
}

.fade-in-leave-from {
    opacity: 1;
    transform: translateY(0);
}

.fade-in-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
