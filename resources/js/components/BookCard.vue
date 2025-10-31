<template>
    <div>
        <div
            class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/30 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="p-4">
                <div class="flex space-x-4">
                    <!-- Book Cover -->
                    <div class="flex-shrink-0">
                        <a :href="bookUrl" class="block">
                            <img :src="book.cover_image || 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=200&h=300&fit=crop&crop=center'"
                                :alt="book.title"
                                class="aspect-[3/4] object-cover rounded-lg shadow-md group-hover:shadow-lg transition-all duration-300"
                                style="width: 120px; height: auto; aspect-ratio: 2 / 3;">
                        </a>
                    </div>

                    <!-- Book Info -->
                    <div class="flex w-full flex-col h-auto justify-between">
                        <div>
                            <h3
                                class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors mb-1">
                                <a :href="bookUrl">{{ book.title }}</a>
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-2">{{ book.author }}</p>

                            <!-- Rating -->
                            <div class="flex items-center mb-2">
                                <div class="flex items-center">
                                    <template v-for="i in 10">
                                        <svg class="w-3 h-3"
                                            :class="i <= book.rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </template>
                                </div>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400 font-medium">{{
                                    formatRating(book.rating) }}</span>
                                <span class="ml-1 text-sm text-gray-500 dark:text-gray-500">({{ book.reviews_count || 0
                                    }})</span>
                            </div>
                        </div>

                        <!-- Stats and Button -->
                        <div class="flex justify-end w-full">
                            <div class="flex justify-between w-full align-center">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2 text-slate-600 dark:text-slate-400" v-if="book.reviews_count > 0">
                                        <i class="fas fa-feather"></i>
                                        <span class="text-base font-semibold">{{ book.reviews_count }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2 text-slate-600 dark:text-slate-400" v-if="book.pages > 0">
                                        <i class="fas fa-paragraph"></i>
                                        <span class="text-base font-semibold">{{ book.pages }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Add Button -->
                            <button v-if="isAuthenticated" @click="openReadingStatusModal"
                                class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 font-bold py-2 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span class="text-sm">Додати</span>
                                </div>
                            </button>
                            <a v-else :href="loginUrl"
                                class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 font-bold py-2 px-4 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span class="text-sm">Додати</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reading Status Modal -->
        <add-to-library-modal v-if="isAuthenticated" :show="showModal" :book="book" :user-libraries="userLibraries"
            @close="closeModal" @status-selected="handleStatusSelected" @open-custom-library="openCustomLibraryModal"
            @notification="$emit('notification', $event)" />

        <!-- Custom Library Modal -->
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
    name: 'BookCard',
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
        user: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            showModal: false,
            showCustomLibraryModal: false,
            selectedLibraryId: ''
        }
    },
    computed: {
        bookUrl() {
            return `/books/${this.book.slug}`;
        },
        loginUrl() {
            return '/login';
        },
        profileCollectionsUrl() {
            // Получаем текущего пользователя из глобальных данных Vue или window
            if (this.user && this.user.username) {
                return `/users/${this.user.username}/collections`;
            }
            return '#';
        }
    },
    mounted() {
        console.log('BookCard mounted with book:', this.book);
        console.log('Is authenticated:', this.isAuthenticated);
        console.log('User:', this.user);
        console.log('User libraries:', this.userLibraries);
    },
    methods: {
        formatRating(rating) {
            return rating ? Number(rating).toFixed(1) : '0.0';
        },
        openReadingStatusModal() {
            console.log('Opening modal, isAuthenticated:', this.isAuthenticated);
            console.log('Book:', this.book);
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
                const response = await axios.post(`/api/reading-status/book/${bookId}`, {
                    status: status === 'want-to-read' ? 'want_to_read' : status
                });

                if (response.data.success) {
                    this.$emit('notification', {
                        message: 'Книга додана до списку!',
                        type: 'success'
                    });
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
        async getBookIdBySlug(slug) {
            try {
                const response = await axios.get(`/api/books/${slug}/id`);
                return response.data.id;
            } catch (error) {
                console.error('Error getting book ID:', error);
                return null;
            }
        },
        openCustomLibraryModal() {
            this.showModal = false; // Закрываем модальное окно статусов
            this.showCustomLibraryModal = true; // Открываем модальное окно кастомных библиотек
        },
        closeCustomLibraryModal() {
            this.showCustomLibraryModal = false;
            this.selectedLibraryId = ''; // Сбрасываем выбранную библиотеку
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
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
