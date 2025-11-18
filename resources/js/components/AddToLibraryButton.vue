<template>
    <div>
        <!-- Кнопка добавления в список чтения -->
        <div v-if="currentStatus" class="flex items-center space-x-2">
            <button @click="openReadingStatusModal"
                :class="statusColors[currentStatus]"
                class="flex-1 bg-gradient-to-r text-white px-8 py-3 rounded-xl font-bold transition-all duration-300 transform shadow-lg hover:shadow-xl flex items-center justify-center">
                {{ statusTexts[currentStatus] }}
            </button>
            <button @click="removeStatus"
                class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-3 rounded-xl font-bold transition-all duration-300 transform shadow-lg hover:shadow-xl flex items-center justify-center"
                title="Видалити статус">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
            </button>
        </div>
        <button @click="openReadingStatusModal" v-else
            class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-8 py-3 rounded-xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Додати
        </button>

        <!-- Кнопка добавления в добірку (только для авторизованных) -->
        <button @click="openCustomLibraryModal" v-if="isAuthenticated"
            class="w-full bg-gradient-to-r from-orange-500 to-pink-500 text-white px-8 py-3 rounded-xl font-bold hover:from-orange-600 hover:to-pink-600 transition-all duration-300 transform shadow-lg hover:shadow-xl mt-3">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            Додати до добірки
        </button>

        <!-- Модальное окно статусов чтения -->
        <add-to-library-modal v-if="isAuthenticated" :show="showModal" :book="book" :user-libraries="userLibraries"
            @close="closeModal" @status-selected="handleStatusSelected" @open-custom-library="handleOpenCustomLibrary"
            @notification="$emit('notification', $event)" />

        <!-- Модальное окно выбора добірки -->
        <div v-if="showCustomLibraryModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300"
                :class="{ 'scale-100 opacity-100': showCustomLibraryModal, 'scale-95 opacity-0': !showCustomLibraryModal }">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Додати до добірки</h3>
                        <button @click="closeCustomLibraryModal"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitToCustomLibrary">
                        <div class="mb-6">
                            <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-3">Оберіть
                                добірку</label>
                            <select v-model="selectedLibraryId" required
                                class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-slate-900 dark:text-white">
                                <option value="">-- Оберіть добірку --</option>
                                <option v-for="library in userLibraries" :key="library.id" :value="library.id">
                                    {{ library.name }}
                                    <span v-if="library.is_private">(Приватна)</span>
                                    <span v-else>(Публічна)</span>
                                </option>
                            </select>
                        </div>

                        <div class="flex space-x-3">
                            <button type="submit"
                                class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-6 rounded-xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                                Додати
                            </button>
                            <button type="button" @click="closeCustomLibraryModal"
                                class="flex-1 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 py-3 px-6 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-all duration-300">
                                Скасувати
                            </button>
                        </div>
                    </form>

                    <div v-if="userLibraries.length === 0" class="mt-4 p-4 bg-slate-100 dark:bg-slate-700 rounded-xl">
                        <p class="text-slate-600 dark:text-slate-400 text-sm mb-3">У вас ще немає добірок</p>
                        <a :href="profileCollectionsUrl"
                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-sm font-medium">
                            Створити першу добірку →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'AddToLibraryButton',
    props: {
        book: {
            type: Object,
            required: true
        },
        userLibraries: {
            type: Array,
            default: () => []
        },
        isAuthenticated: {
            type: Boolean,
            default: false
        },
        initialStatus: {
            type: String,
            default: null
        }
    },
    data() {
        return {
            showModal: false,
            showCustomLibraryModal: false,
            selectedLibraryId: '',
            currentStatus: this.initialStatus,
            statusTexts: {
                'read': 'Прочитано',
                'reading': 'Читаю',
                'want_to_read': 'Буду читати',
                'abandoned': 'Закинуто'
            },
            statusColors: {
                'read': 'from-green-500 to-green-600 hover:from-green-600 hover:to-green-700',
                'reading': 'from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700',
                'want_to_read': 'from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700',
                'abandoned': 'from-red-500 to-red-600 hover:from-red-600 hover:to-red-700'
            }
        }
    },
    computed: {
        profileCollectionsUrl() {
            if (this.$root.$data.user && this.$root.$data.user.username) {
                return `/users/${this.$root.$data.user.username}/collections`;
            }
            return '#';
        }
    },
    methods: {
        openReadingStatusModal() {
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
        },
        async handleStatusSelected(status) {
            try {
                // Получаем ID книги из slug
                const bookId = await this.getBookIdBySlug(this.book.slug);

                // Отправляем запрос на сервер
                const normalizedStatus = status === 'want-to-read' ? 'want_to_read' : status;
                const response = await axios.post(`/api/reading-status/book/${bookId}`, {
                    status: normalizedStatus
                });

                if (response.data.success) {
                    this.currentStatus = normalizedStatus;
                    this.$emit('notification', {
                        message: 'Книга додана до списку!',
                        type: 'success'
                    });
                    this.$emit('status-updated', this.currentStatus);
                } else {
                    this.$emit('notification', {
                        message: response.data.message || 'Помилка при збереженні статусу',
                        type: 'error'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                this.$emit('notification', {
                    message: 'Помилка при збереженні статусу',
                    type: 'error'
                });
            }

            this.closeModal();
        },
        async removeStatus() {
            if (!confirm('Ви впевнені, що хочете видалити статус для цієї книги?')) {
                return;
            }

            try {
                const bookId = await this.getBookIdBySlug(this.book.slug);
                const response = await axios.delete(`/api/reading-status/book/${bookId}`);

                if (response.data.success) {
                    this.currentStatus = null;
                    this.$emit('notification', {
                        message: 'Статус видалено!',
                        type: 'success'
                    });
                    this.$emit('status-updated', null);
                } else {
                    this.$emit('notification', {
                        message: response.data.message || 'Помилка при видаленні статусу',
                        type: 'error'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                this.$emit('notification', {
                    message: 'Помилка при видаленні статусу',
                    type: 'error'
                });
            }
        },
        async getBookIdBySlug(slug) {
            try {
                const response = await axios.get(`/api/books/${slug}/id`);
                return response.data.id;
            } catch (error) {
                console.error('Error getting book ID:', error);
                return null;
            }
        },
        handleOpenCustomLibrary() {
            this.closeModal();
            this.openCustomLibraryModal();
        },
        openCustomLibraryModal() {
            this.showCustomLibraryModal = true;
        },
        closeCustomLibraryModal() {
            this.showCustomLibraryModal = false;
            this.selectedLibraryId = '';
        },
        async submitToCustomLibrary() {
            if (!this.selectedLibraryId) {
                this.$emit('notification', { message: 'Оберіть добірку', type: 'error' });
                return;
            }

            try {
                const url = `/libraries/${this.selectedLibraryId}/add-book`;
                const formData = new FormData();
                formData.append('book_slug', this.book.slug);

                const response = await axios.post(url, formData);

                if (response.data.success) {
                    this.$emit('notification', { message: 'Книга успішно додана до добірки!', type: 'success' });
                    this.closeCustomLibraryModal();
                } else {
                    this.$emit('notification', { message: response.data.message || 'Помилка при додаванні книги', type: 'error' });
                }
            } catch (error) {
                console.error('Error:', error);
                this.$emit('notification', { message: 'Помилка при додаванні книги', type: 'error' });
            }
        }
    }
};
</script>

<style scoped>
/* Стили для компонента */
</style>

