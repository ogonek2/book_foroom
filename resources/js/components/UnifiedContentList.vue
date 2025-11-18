<template>
    <div>
        <!-- Content List -->
        <div v-if="filteredContent.length > 0" class="space-y-4">
            <div v-for="item in filteredContent" :key="`${item.type}-${item.id}`" 
                 class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
                
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
                    <div v-if="item.type === 'review'" class="flex items-center">
                        <div class="flex items-center bg-green-100 dark:bg-green-900/30 px-3 py-1 rounded-full">
                            <i class="fas fa-star text-green-600 dark:text-green-400 text-sm mr-1"></i>
                            <span class="text-green-700 dark:text-green-300 font-medium text-sm">{{ item.rating }}</span>
                        </div>
                    </div>
                </div>

                <!-- Title (for Discussions) -->
                <h3 v-if="item.type === 'discussion'" 
                    class="text-lg font-bold text-light-text-primary dark:text-dark-text-primary mb-3 hover:text-brand-500 dark:hover:text-brand-400 transition-colors">
                    <a :href="getItemUrl(item)">
                        {{ item.title }}
                    </a>
                </h3>

                <!-- Content Preview -->
                <div class="text-light-text-primary dark:text-dark-text-primary leading-relaxed mb-4 relative">
                    <div class="text-sm content-preview" 
                         :class="{ 'content-fade': !isExpanded(item) && getTextLength(item.content) > 150 }"
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
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16">
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

        <!-- Pagination -->
        <div v-if="showPagination && totalPages > 1" class="mt-8 flex justify-center">
            <nav class="flex items-center space-x-2">
                <button v-for="page in visiblePages" :key="page"
                        @click="goToPage(page)"
                        :class="[
                            'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                            page === currentPage 
                                ? 'bg-brand-500 text-white' 
                                : 'bg-white dark:bg-gray-800 text-light-text-primary dark:text-dark-text-primary hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700'
                        ]">
                    {{ page }}
                </button>
            </nav>
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
        },
        currentPage: {
            type: Number,
            default: 1
        },
        perPage: {
            type: Number,
            default: 10
        }
    },
    data() {
        return {
            expandedItems: new Set(),
            showPagination: true,
            itemLikes: {} // Храним состояние лайков для каждого элемента
        };
    },
    mounted() {
        console.log('UnifiedContentList mounted');
        console.log('Discussions:', this.discussions);
        console.log('Reviews:', this.reviews);
        
        // Инициализируем состояние лайков для всех элементов
        this.initializeLikes();
    },
    computed: {
        // Объединяем и нормализуем данные
        normalizedContent() {
            const discussions = this.discussions.map(discussion => ({
                id: discussion.id,
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
                url: `/discussions/${discussion.id}`
            }));

            const reviews = this.reviews.map(review => ({
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
                book: review.book,
                url: `/books/${review.book?.slug}/reviews/${review.id}`
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

            // Пагинация
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            
            return content.slice(start, end);
        },

        totalPages() {
            let totalItems = this.normalizedContent.length;
            
            if (this.activeFilter === 'discussions') {
                totalItems = this.discussions.length;
            } else if (this.activeFilter === 'reviews') {
                totalItems = this.reviews.length;
            }

            return Math.ceil(totalItems / this.perPage);
        },

        visiblePages() {
            const pages = [];
            const maxVisible = 5;
            const half = Math.floor(maxVisible / 2);
            
            let start = Math.max(1, this.currentPage - half);
            let end = Math.min(this.totalPages, start + maxVisible - 1);
            
            if (end - start < maxVisible - 1) {
                start = Math.max(1, end - maxVisible + 1);
            }
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            
            return pages;
        }
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
            console.log('Toggle expanded for:', key, 'Current state:', this.expandedItems.has(key));
            if (this.expandedItems.has(key)) {
                this.expandedItems.delete(key);
            } else {
                this.expandedItems.add(key);
            }
            console.log('New state:', this.expandedItems.has(key));
        },

        isExpanded(item) {
            const key = `${item.type}-${item.id}`;
            const expanded = this.expandedItems.has(key);
            console.log('Is expanded for:', key, expanded);
            return expanded;
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
                case 'trending':
                    // Сортировка по активности за последние 7 дней
                    const weekAgo = new Date();
                    weekAgo.setDate(weekAgo.getDate() - 7);
                    return sorted.sort((a, b) => {
                        const aRecent = new Date(a.updated_at) > weekAgo;
                        const bRecent = new Date(b.updated_at) > weekAgo;
                        
                        if (aRecent && !bRecent) return -1;
                        if (!aRecent && bRecent) return 1;
                        
                        return (b.likes_count + b.replies_count + b.comments_count) - (a.likes_count + a.replies_count + a.comments_count);
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
                    url = `/discussions/${item.id}/like`;
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
            if (navigator.share) {
                try {
                    await navigator.share({
                        title: item.title || 'Контент',
                        url: window.location.origin + (item.url || '#')
                    });
                } catch (error) {
                    console.log('Share cancelled');
                }
            } else {
                // Fallback to clipboard
                try {
                    await navigator.clipboard.writeText(window.location.origin + (item.url || '#'));
                    alert('Посилання скопійовано в буфер обміну!');
                } catch (error) {
                    console.error('Failed to copy to clipboard:', error);
                }
            }
        },

        goToPage(page) {
            this.$emit('page-changed', page);
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
</style>
