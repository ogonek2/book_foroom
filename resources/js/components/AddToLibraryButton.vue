<template>
    <div>
        <!-- Кнопка добавления в список чтения -->
        <div v-if="currentStatus" class="w-full">
            <div
                class="flex items-center gap-2 rounded-2xl light:bg-slate-900/70 dark:bg-slate-800/80 border border-white/5 dark:border-slate-700/60">
                <button @click="openReadingStatusModal"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-semibold text-sm uppercase tracking-wide transition-all duration-300 shadow-[0_15px_40px_-25px_rgba(147,51,234,0.9)] hover:scale-[1.01]"
                    :class="statusColors[currentStatus] || statusColors.default">
                    <span class="text-white">{{ statusTexts[currentStatus] }}</span>
                </button>
                <button @click="removeStatus"
                    class="px-4 py-3 rounded-2xl bg-slate-900/70 dark:bg-slate-900/80 text-slate-200 hover:text-white hover:bg-slate-900 transition-all duration-200 flex items-center justify-center"
                    title="Видалити статус">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
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
            :current-status="currentStatus"
            @close="closeModal" @status-selected="handleStatusSelected" @open-custom-library="handleOpenCustomLibrary" />

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

                    <!-- Список существующих добірок, где уже есть книга -->
                    <div v-if="currentBookLibraries.length > 0" class="mb-6">
                        <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-3">
                            Книга вже в добірках:
                        </label>
                        <div class="space-y-2">
                            <div v-for="library in currentBookLibraries" :key="library.id"
                                class="flex items-center justify-between p-3 bg-slate-100 dark:bg-slate-700 rounded-xl">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-folder text-orange-500"></i>
                                    <span class="text-slate-900 dark:text-white font-medium">{{ library.name }}</span>
                                    <span v-if="library.is_private"
                                        class="text-xs text-slate-500 dark:text-slate-400">(Приватна)</span>
                                    <span v-else class="text-xs text-slate-500 dark:text-slate-400">(Публічна)</span>
                                </div>
                                <button @click="removeFromLibrary(library)"
                                    class="p-1 text-red-500 hover:text-red-700 dark:hover:text-red-400 transition-colors"
                                    title="Видалити з добірки">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitToCustomLibrary">
                        <div class="mb-6">
                            <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-3">Оберіть
                                добірку</label>
                            <select v-model="selectedLibraryId" required
                                class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-slate-900 dark:text-white">
                                <option value="">-- Оберіть добірку --</option>
                                <option v-for="library in availableLibraries" :key="library.id" :value="library.id">
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
            currentBookLibraries: [],
            statusTexts: {
                'read': 'Прочитано',
                'reading': 'Читаю',
                'want_to_read': 'Буду читати',
                'abandoned': 'Закинуто'
            },
            statusColors: {
                default: 'bg-gradient-to-r from-violet-500 via-fuchsia-500 to-pink-500 hover:opacity-95',
                read: 'bg-gradient-to-r from-violet-500 via-fuchsia-500 to-pink-500 hover:opacity-95',
                reading: 'bg-gradient-to-r from-violet-500 via-fuchsia-500 to-pink-500 hover:opacity-95',
                want_to_read: 'bg-gradient-to-r from-violet-500 via-fuchsia-500 to-pink-500 hover:opacity-95',
                abandoned: 'bg-gradient-to-r from-violet-500 via-fuchsia-500 to-pink-500 hover:opacity-95'
            }
        }
    },
    mounted() {
        if (this.isAuthenticated) {
            this.loadBookLibraries();
        }
    },
    computed: {
        profileCollectionsUrl() {
            if (this.$root.$data.user && this.$root.$data.user.username) {
                return `/users/${this.$root.$data.user.username}/collections`;
            }
            return '#';
        },
        availableLibraries() {
            // Доборки, в которых книга еще не добавлена
            const bookLibraryIds = this.currentBookLibraries.map(lib => lib.id);
            return this.userLibraries.filter(lib => !bookLibraryIds.includes(lib.id));
        }
    },
    methods: {
        openReadingStatusModal() {
            // Проверяем авторизацию перед открытием модального окна
            if (!this.isAuthenticated) {
                // Сохраняем текущий URL для редиректа после логина
                const currentUrl = window.location.href;
                window.location.href = `/login?redirect=${encodeURIComponent(currentUrl)}`;
                return;
            }
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
                    this.showAlert('Книга додана до списку!', 'Успіх', 'success');
                    this.$emit('status-updated', this.currentStatus);
                } else {
                    this.showAlert(response.data.message || 'Помилка при збереженні статусу', 'Помилка', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showAlert('Помилка при збереженні статусу', 'Помилка', 'error');
            }

            this.closeModal();
        },
        async removeStatus() {
            try {
                if (!window.alertModalInstance || !window.alertModalInstance.$refs || !window.alertModalInstance.$refs.modal) {
                    if (!confirm('Ви впевнені, що хочете видалити статус для цієї книги?')) {
                        return;
                    }
                } else {
                    const confirmed = await window.alertModalInstance.$refs.modal.confirm('Ви впевнені, що хочете видалити статус для цієї книги?', 'Підтвердження', 'warning');
                    if (!confirmed) {
                        return;
                    }
                }
            } catch (e) {
                if (!confirm('Ви впевнені, що хочете видалити статус для цієї книги?')) {
                    return;
                }
            }

            try {
                const bookId = await this.getBookIdBySlug(this.book.slug);
                const response = await axios.delete(`/api/reading-status/book/${bookId}`);

                if (response.data.success) {
                    this.currentStatus = null;
                    this.showAlert('Статус видалено!', 'Успіх', 'success');
                    this.$emit('status-updated', null);
                } else {
                    this.showAlert(response.data.message || 'Помилка при видаленні статусу', 'Помилка', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showAlert('Помилка при видаленні статусу', 'Помилка', 'error');
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
        async openCustomLibraryModal() {
            // Проверяем авторизацию перед открытием модального окна
            if (!this.isAuthenticated) {
                // Сохраняем текущий URL для редиректа после логина
                const currentUrl = window.location.href;
                window.location.href = `/login?redirect=${encodeURIComponent(currentUrl)}`;
                return;
            }
            await this.loadBookLibraries(); // Загружаем актуальный список библиотек
            this.showCustomLibraryModal = true;
        },
        closeCustomLibraryModal() {
            this.showCustomLibraryModal = false;
            this.selectedLibraryId = '';
        },
        async submitToCustomLibrary() {
            if (!this.selectedLibraryId) {
                this.showAlert('Оберіть добірку', 'Помилка', 'error');
                return;
            }

            try {
                const url = `/libraries/${this.selectedLibraryId}/add-book`;
                const formData = new FormData();
                formData.append('book_slug', this.book.slug);

                const response = await axios.post(url, formData);

                if (response.data.success) {
                    await this.loadBookLibraries(); // Обновляем список библиотек
                    this.showAlert('Книга успішно додана до добірки!', 'Успіх', 'success');
                    this.selectedLibraryId = '';
                } else {
                    this.showAlert(response.data.message || 'Помилка при додаванні книги', 'Помилка', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showAlert('Помилка при додаванні книги', 'Помилка', 'error');
            }
        },
        async loadBookLibraries() {
            if (!this.isAuthenticated) return;
            
            try {
                const response = await axios.get(`/api/books/${this.book.slug}/libraries`);
                if (response.data && response.data.success) {
                    this.currentBookLibraries = response.data.libraries || [];
                }
            } catch (error) {
                // Игнорируем ошибку, если библиотеки не найдены
                if (error.response && error.response.status !== 404) {
                    console.error('Error loading book libraries:', error);
                }
            }
        },
        async removeFromLibrary(library) {
            try {
                const response = await axios.delete(`/libraries/${library.id}/books/${this.book.slug}`);

                if (response.data.success) {
                    this.currentBookLibraries = this.currentBookLibraries.filter(lib => lib.id !== library.id);
                    this.showAlert(`Книгу видалено з добірки "${library.name}"`, 'Успіх', 'success');
                } else {
                    this.showAlert(response.data.message || 'Помилка при видаленні книги', 'Помилка', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showAlert(error.response?.data?.message || 'Помилка при видаленні книги', 'Помилка', 'error');
            }
        },
        showAlert(message, title, type = 'info') {
            if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
                window.alertModalInstance.$refs.modal.alert(message, title, type);
            } else {
                // Fallback если модалка не готова
                setTimeout(() => {
                    if (window.alertModalInstance && window.alertModalInstance.$refs && window.alertModalInstance.$refs.modal) {
                        window.alertModalInstance.$refs.modal.alert(message, title, type);
                    }
                }, 100);
            }
        }
    }
};
</script>

<style scoped>
/* Стили для компонента */
</style>

