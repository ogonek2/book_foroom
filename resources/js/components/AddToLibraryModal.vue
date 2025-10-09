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
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Додати до списку</h3>
                    <button @click="closeModal"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Status Options -->
                <div class="space-y-3">
                    <!-- Прочитано -->
                    <button @click="selectStatus('read')"
                            class="w-full text-left p-4 rounded-xl border-2 border-transparent hover:border-indigo-500 dark:hover:border-indigo-400 transition-all duration-300 bg-slate-50 dark:bg-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 group">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mr-4 group-hover:bg-green-200 dark:group-hover:bg-green-800/40 transition-colors">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    Прочитано</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Книга прочитана повністю</p>
                            </div>
                        </div>
                    </button>

                    <!-- Читаю -->
                    <button @click="selectStatus('reading')"
                            class="w-full text-left p-4 rounded-xl border-2 border-transparent hover:border-indigo-500 dark:hover:border-indigo-400 transition-all duration-300 bg-slate-50 dark:bg-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 group">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mr-4 group-hover:bg-blue-200 dark:group-hover:bg-blue-800/40 transition-colors">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    Читаю</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Зараз читаю цю книгу</p>
                            </div>
                        </div>
                    </button>

                    <!-- Буду читать -->
                    <button @click="selectStatus('want-to-read')"
                            class="w-full text-left p-4 rounded-xl border-2 border-transparent hover:border-indigo-500 dark:hover:border-indigo-400 transition-all duration-300 bg-slate-50 dark:bg-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 group">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mr-4 group-hover:bg-purple-200 dark:group-hover:bg-purple-800/40 transition-colors">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    Буду читати</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Планую прочитати цю книгу</p>
                            </div>
                        </div>
                    </button>

                    <!-- Divider -->
                    <div class="border-t border-slate-200 dark:border-slate-600 my-4"></div>

                    <!-- Add to Library Button -->
                    <button @click="openCustomLibraryModal"
                            class="w-full text-left p-4 rounded-xl border-2 border-transparent hover:border-orange-500 dark:hover:border-orange-400 transition-all duration-300 bg-slate-50 dark:bg-slate-700 hover:bg-orange-50 dark:hover:bg-orange-900/20 group">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center mr-4 group-hover:bg-orange-200 dark:group-hover:bg-orange-800/40 transition-colors">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                    Додати до добірки</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-400">Створити або додати до існуючої добірки</p>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AddToLibraryModal',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        book: {
            type: Object,
            required: true
        },
        userLibraries: {
            type: Array,
            default: () => []
        }
    },
    computed: {
        modalClasses() {
            return this.show 
                ? 'scale-100 opacity-100' 
                : 'scale-95 opacity-0';
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
        selectStatus(status) {
            this.$emit('status-selected', status);
        },
        openCustomLibraryModal() {
            // Эмитим событие для открытия модального окна с кастомными библиотеками
            this.$emit('open-custom-library');
        }
    }
}
</script>

<style scoped>
/* Стили для модального окна */
</style>
