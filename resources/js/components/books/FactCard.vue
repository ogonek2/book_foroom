<template>
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700 hover:shadow-xl transition-all duration-300 h-full
        flex flex-col justify-between">
        <!-- User Info -->
        <div>
            <div class="flex items-center space-x-3 justify-between">
                <div class="flex items-center space-x-3">
                    <template v-if="fact.user && fact.user.username">
                        <a :href="profileUrl(fact.user.username)"
                           class="flex items-center space-x-3 group"
                           @click.stop>
                            <div>
                                <img v-if="fact.user.avatar_display" :src="fact.user.avatar_display"
                                    :alt="fact.user.name"
                                    class="w-8 h-8 rounded-full transition-transform duration-200 group-hover:scale-110">
                                <div v-else
                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center text-white font-bold text-sm transition-transform duration-200 group-hover:scale-110">
                                    {{ (fact.user?.name || 'U').charAt(0).toUpperCase() }}
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-brand-500 dark:group-hover:text-brand-400 transition-colors">
                                    {{ fact.user?.name || 'Користувач' }}
                                </div>
                                <div v-if="fact.user?.username"
                                    class="text-xs text-slate-500 dark:text-slate-400 group-hover:text-brand-500 dark:group-hover:text-brand-400 transition-colors">
                                    {{ '@' + fact.user.username }}
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ formatDate(fact.created_at) }}
                                </div>
                            </div>
                        </a>
                    </template>
                    <template v-else>
                        <img v-if="fact.user && fact.user.avatar_display" :src="fact.user.avatar_display"
                            :alt="fact.user?.name || 'Користувач'" class="w-8 h-8 rounded-full">
                        <div v-else
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center text-white font-bold text-sm">
                            {{ (fact.user?.name || 'U').charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                {{ fact.user?.name || 'Користувач' }}
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                {{ formatDate(fact.created_at) }}
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <!-- Fact Text -->
            <div v-if="!isEditing" class="text-lg text-slate-700 dark:text-slate-300 leading-relaxed font-medium my-4">
                {{ fact.content }}
            </div>
            
            <!-- Edit Form -->
            <div v-else class="my-4">
                <textarea v-model="editContent" 
                    class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"
                    rows="3" placeholder="Введіть цікавий факт..."></textarea>
                <div class="flex space-x-2 mt-3">
                    <button @click="saveEdit" :disabled="isSaving"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 text-sm">
                        {{ isSaving ? 'Збереження...' : 'Зберегти' }}
                    </button>
                    <button @click="cancelEdit" :disabled="isSaving"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50 text-sm">
                        Скасувати
                    </button>
                </div>
            </div>
        </div>
        <div>
            <!-- Actions -->
            <div class="flex items-center space-x-4">
                <!-- Like Button -->
                <button @click="toggleLike"
                    class="flex items-center space-x-1 text-slate-600 dark:text-slate-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                    <i :class="['fas fa-heart', isLiked ? 'text-red-500 dark:text-red-400' : '']"></i>
                    <span class="text-sm">{{ likesCount }}</span>
                </button>

                <!-- Share Button -->
                <button @click="shareFact"
                    class="flex items-center space-x-1 text-slate-600 dark:text-slate-400 hover:text-brand-500 dark:hover:text-brand-400 transition-colors">
                    <i class="fas fa-share-alt"></i>
                </button>

                <!-- Edit Button (for owner) -->
                <button v-if="canEdit && !isEditing" @click="startEdit"
                    class="text-slate-600 dark:text-slate-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                    <i class="fas fa-edit text-sm"></i>
                </button>

                <!-- Delete Button (for owner) -->
                <button v-if="canDelete" @click="deleteFact"
                    class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">
                    <i class="fas fa-trash text-sm"></i>
                </button>

                <!-- Report Button -->
                <report-button 
                    :reportable-type="'App\\Models\\Fact'"
                    :reportable-id="fact.id"
                    :content-preview="getContentPreview()"
                    :content-url="getContentUrl()">
                </report-button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'FactCard',
    props: {
        fact: {
            type: Object,
            required: true
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
            isLiked: this.fact.is_liked_by_current_user || false,
            likesCount: this.fact.likes_count || 0,
            isEditing: false,
            editContent: '',
            isSaving: false,
        };
    },
    computed: {
        canEdit() {
            return this.currentUserId && this.fact.user_id === this.currentUserId;
        },
        canDelete() {
            return this.currentUserId && this.fact.user_id === this.currentUserId;
        }
    },
    methods: {
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
        async toggleLike() {
            if (!this.currentUserId) {
                this.$emit('show-notification', 'Будь ласка, увійдіть, щоб поставити лайк.', 'error');
                return;
            }
            try {
                const response = await axios.post(`/books/${this.bookSlug}/facts/${this.fact.id}/like`);
                if (response.data.success) {
                    this.isLiked = response.data.is_liked;
                    this.likesCount = response.data.likes_count;
                    this.$emit('like-toggled', { factId: this.fact.id, isLiked: this.isLiked, likesCount: this.likesCount });
                }
            } catch (error) {
                console.error('Error toggling like:', error);
                this.$emit('show-notification', 'Помилка при зміні лайка.', 'error');
            }
        },
        shareFact() {
            const text = `Цікавий факт про книгу: ${this.fact.content}`;
            if (navigator.share) {
                navigator.share({
                    title: 'Цікавий факт',
                    text: text,
                    url: window.location.href
                }).catch(err => console.log('Error sharing:', err));
            } else {
                navigator.clipboard.writeText(text).then(() => {
                    this.$emit('show-notification', 'Факт скопійовано!', 'success');
                });
            }
        },
        startEdit() {
            this.isEditing = true;
            this.editContent = this.fact.content;
        },
        cancelEdit() {
            this.isEditing = false;
            this.editContent = '';
        },
        async saveEdit() {
            if (!this.editContent.trim()) {
                this.$emit('show-notification', 'Будь ласка, введіть текст факту.', 'error');
                return;
            }

            this.isSaving = true;
            try {
                const response = await axios.put(`/books/${this.bookSlug}/facts/${this.fact.id}`, {
                    content: this.editContent.trim(),
                });

                if (response.data.success) {
                    this.$emit('show-notification', 'Факт оновлено!', 'success');
                    this.$emit('fact-updated', response.data.fact);
                    this.isEditing = false;
                } else {
                    this.$emit('show-notification', response.data.message || 'Помилка при оновленні факту.', 'error');
                }
            } catch (error) {
                console.error('Error updating fact:', error);
                this.$emit('show-notification', error.response?.data?.message || 'Помилка при оновленні факту.', 'error');
            } finally {
                this.isSaving = false;
            }
        },
        async deleteFact() {
            const confirmed = await confirm('Ви впевнені, що хочете видалити цей факт?', 'Підтвердження', 'warning');
            if (!confirmed) return;
            try {
                const response = await axios.delete(`/books/${this.bookSlug}/facts/${this.fact.id}`);
                if (response.data.success) {
                    this.$emit('show-notification', 'Факт видалено!', 'success');
                    this.$emit('fact-deleted', this.fact.id);
                } else {
                    this.$emit('show-notification', response.data.message || 'Помилка при видаленні факту.', 'error');
                }
            } catch (error) {
                console.error('Error deleting fact:', error);
                this.$emit('show-notification', error.response?.data?.message || 'Помилка при видаленні факту.', 'error');
            }
        },

        getContentPreview() {
            // Возвращаем превью контента для жалобы
            if (this.fact.content) {
                const text = this.fact.content;
                return text.substring(0, 100) + (text.length > 100 ? '...' : '');
            }
            return 'Цікавий факт про книгу';
        },

        getContentUrl() {
            // Возвращаем URL контента
            return `/books/${this.bookSlug}#fact-${this.fact.id}`;
        },
        profileUrl(username) {
            return username ? `/users/${username}` : '#';
        }
    }
};
</script>

<style scoped>
/* Fact card styles inherited from parent */
</style>
