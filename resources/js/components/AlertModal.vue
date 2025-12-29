<template>
    <transition name="modal-fade">
        <div v-if="show" 
             class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] flex items-center justify-center p-4"
             @click.self="handleCancel">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all"
                 :class="modalClass">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <!-- Icon -->
                        <div v-if="type === 'success'" 
                             class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div v-else-if="type === 'error'" 
                             class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div v-else-if="type === 'warning'" 
                             class="flex-shrink-0 w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div v-else 
                             class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <!-- Title -->
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white flex-1">
                            {{ title }}
                        </h3>
                        <!-- Close button (only for alert) -->
                        <button v-if="mode === 'alert'" 
                                @click="handleCancel"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-6">
                    <!-- Message -->
                    <p class="text-gray-700 dark:text-gray-300 mb-4 whitespace-pre-wrap">{{ message }}</p>

                    <!-- Input field (for prompt mode) -->
                    <div v-if="mode === 'prompt'" class="mb-4">
                        <input 
                            ref="promptInput"
                            v-model="promptValue"
                            type="text"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            :placeholder="promptPlaceholder"
                            @keyup.enter="handleConfirm"
                            @keyup.esc="handleCancel"
                        >
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                    <!-- Cancel button (for confirm/prompt) -->
                    <button v-if="mode === 'confirm' || mode === 'prompt'"
                            @click="handleCancel"
                            class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-colors">
                        {{ cancelText }}
                    </button>
                    <!-- Confirm/OK button -->
                    <button 
                        @click="handleConfirm"
                        :class="[
                            'px-4 py-2 text-sm font-semibold rounded-xl transition-colors',
                            mode === 'alert' ? 'bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white' :
                            mode === 'confirm' ? 'bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white' :
                            'bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white'
                        ]">
                        {{ confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: 'AlertModal',
    data() {
        return {
            show: false,
            mode: 'alert', // 'alert', 'confirm', 'prompt'
            type: 'info', // 'info', 'success', 'error', 'warning'
            title: '',
            message: '',
            confirmText: 'OK',
            cancelText: 'Скасувати',
            promptValue: '',
            promptPlaceholder: '',
            resolve: null,
            reject: null,
            autoCloseTimer: null,
        }
    },
    computed: {
        modalClass() {
            return {
                'scale-100 opacity-100': this.show,
                'scale-95 opacity-0': !this.show,
            }
        }
    },
    mounted() {
        // Focus input when prompt mode is shown
        this.$watch('show', (newVal) => {
            if (newVal && this.mode === 'prompt' && this.$refs.promptInput) {
                this.$nextTick(() => {
                    this.$refs.promptInput.focus();
                });
            }
        });
    },
    methods: {
        alert(message, title = 'Повідомлення', type = 'info') {
            // Очищаем предыдущий таймер, если он есть
            if (this.autoCloseTimer) {
                clearTimeout(this.autoCloseTimer);
                this.autoCloseTimer = null;
            }

            return new Promise((resolve) => {
                this.mode = 'alert';
                this.type = type;
                this.title = title;
                this.message = message;
                this.confirmText = 'OK';
                this.show = true;
                this.resolve = () => {
                    if (this.autoCloseTimer) {
                        clearTimeout(this.autoCloseTimer);
                        this.autoCloseTimer = null;
                    }
                    this.show = false;
                    resolve(true);
                };
                
                // Автоматически закрываем через 6 секунд
                this.autoCloseTimer = setTimeout(() => {
                    if (this.resolve) {
                        this.resolve();
                    }
                }, 6000);
            });
        },
        confirm(message, title = 'Підтвердження', type = 'warning') {
            return new Promise((resolve, reject) => {
                this.mode = 'confirm';
                this.type = type;
                this.title = title;
                this.message = message;
                this.confirmText = 'Підтвердити';
                this.cancelText = 'Скасувати';
                this.show = true;
                this.resolve = () => {
                    this.show = false;
                    resolve(true);
                };
                this.reject = () => {
                    this.show = false;
                    reject(false);
                };
            });
        },
        prompt(message, defaultValue = '', title = 'Введення', placeholder = '') {
            return new Promise((resolve, reject) => {
                this.mode = 'prompt';
                this.type = 'info';
                this.title = title;
                this.message = message;
                this.promptValue = defaultValue;
                this.promptPlaceholder = placeholder;
                this.confirmText = 'OK';
                this.cancelText = 'Скасувати';
                this.show = true;
                this.resolve = () => {
                    const value = this.promptValue;
                    this.show = false;
                    this.promptValue = '';
                    resolve(value);
                };
                this.reject = () => {
                    this.show = false;
                    this.promptValue = '';
                    reject(null);
                };
            });
        },
        handleConfirm() {
            if (this.autoCloseTimer) {
                clearTimeout(this.autoCloseTimer);
                this.autoCloseTimer = null;
            }
            if (this.resolve) {
                this.resolve();
            }
        },
        handleCancel() {
            if (this.autoCloseTimer) {
                clearTimeout(this.autoCloseTimer);
                this.autoCloseTimer = null;
            }
            if (this.mode === 'alert') {
                this.handleConfirm();
            } else if (this.reject) {
                this.reject();
            }
        }
    }
}
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter,
.modal-fade-leave-to {
    opacity: 0;
}
</style>

