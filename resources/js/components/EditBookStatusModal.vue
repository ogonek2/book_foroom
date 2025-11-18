<template>
    <div v-if="show" 
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         @click="handleBackdropClick">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300"
             :class="modalClasses"
             @click.stop>
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Редагувати статус книги</h3>
                    <button @click="closeModal"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="saveStatus">
                    <!-- Status -->
                    <div class="mb-4">
                        <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-2">Статус</label>
                        <select v-model="formData.status" required
                            class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-slate-900 dark:text-white">
                            <option value="want_to_read">Хочу прочитати</option>
                            <option value="reading">Читаю</option>
                            <option value="read">Прочитано</option>
                            <option value="abandoned">Закинуто</option>
                        </select>
                    </div>

                    <!-- Times Read -->
                    <div class="mb-4">
                        <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-2">
                            Кількість разів прочитано
                        </label>
                        <input v-model.number="formData.times_read" type="number" min="1" required
                            class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-slate-900 dark:text-white">
                    </div>

                    <!-- Reading Language -->
                    <div class="mb-6">
                        <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-2">
                            Мова читання
                        </label>
                        <select v-model="formData.reading_language"
                            class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-slate-900 dark:text-white">
                            <option value="">Не вказано</option>
                            <option value="uk">Українська</option>
                            <option value="en">English</option>
                            <option value="ru">Русский</option>
                            <option value="pl">Polski</option>
                            <option value="de">Deutsch</option>
                            <option value="fr">Français</option>
                            <option value="es">Español</option>
                            <option value="it">Italiano</option>
                            <option value="other">Інша</option>
                        </select>
                    </div>

                    <!-- Rating (if read) -->
                    <div v-if="formData.status === 'read'" class="mb-4">
                        <label class="block text-slate-700 dark:text-slate-300 text-sm font-medium mb-2">Оцінка</label>
                        <div class="flex items-center space-x-1">
                            <button v-for="i in 10" :key="i" type="button"
                                @click="formData.rating = i"
                                :class="[
                                    'w-10 h-10 rounded-lg transition-all duration-200',
                                    i <= (formData.rating || 0) 
                                        ? 'bg-yellow-400 text-white' 
                                        : 'bg-slate-200 dark:bg-slate-700 text-slate-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/30'
                                ]">
                                {{ i }}
                            </button>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-3">
                        <button type="submit" :disabled="isSaving"
                            class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 px-6 rounded-xl font-bold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ isSaving ? 'Збереження...' : 'Зберегти' }}
                        </button>
                        <button type="button" @click="closeModal" :disabled="isSaving"
                            class="flex-1 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 py-3 px-6 rounded-xl font-bold hover:bg-slate-300 dark:hover:bg-slate-600 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            Скасувати
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'EditBookStatusModal',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        readingStatus: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            isSaving: false,
            formData: {
                status: 'want_to_read',
                times_read: 1,
                reading_language: '',
                rating: null
            }
        }
    },
    computed: {
        modalClasses() {
            return this.show 
                ? 'scale-100 opacity-100' 
                : 'scale-95 opacity-0';
        }
    },
    watch: {
        readingStatus: {
            immediate: true,
            handler(newVal) {
                if (newVal) {
                    this.formData = {
                        status: newVal.status || 'want_to_read',
                        times_read: newVal.times_read || 1,
                        reading_language: newVal.reading_language || '',
                        rating: newVal.rating || null
                    };
                }
            }
        }
    },
    methods: {
        closeModal() {
            this.$emit('close');
        },
        handleBackdropClick(event) {
            if (event.target === event.currentTarget) {
                this.closeModal();
            }
        },
        async saveStatus() {
            this.isSaving = true;
            try {
                const response = await axios.put(`/api/reading-status/${this.readingStatus.id}`, this.formData);
                
                if (response.data.success) {
                    this.$emit('saved', response.data.data);
                    this.$emit('notification', {
                        message: 'Статус оновлено!',
                        type: 'success'
                    });
                    this.closeModal();
                } else {
                    this.$emit('notification', {
                        message: response.data.message || 'Помилка при збереженні',
                        type: 'error'
                    });
                }
            } catch (error) {
                console.error('Error saving status:', error);
                this.$emit('notification', {
                    message: error.response?.data?.message || 'Помилка при збереженні статусу',
                    type: 'error'
                });
            } finally {
                this.isSaving = false;
            }
        }
    }
}
</script>

<style scoped>
/* Стили для модального окна */
</style>

