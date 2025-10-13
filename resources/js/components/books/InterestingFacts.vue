<template>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Цікаві факти
            </h3>
            <button v-if="isAuthenticated" 
                    @click="showAddForm = !showAddForm"
                    class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-2 rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>
                Додати факт
            </button>
        </div>

        <!-- Add Fact Form -->
        <div v-if="showAddForm" class="mb-6 p-6 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Додати новий факт</h4>
            <form @submit.prevent="addFact">
                <div class="mb-4">
                    <textarea v-model="newFact" 
                              placeholder="Введіть цікавий факт про книгу..."
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-800 dark:text-white resize-none"
                              rows="3"
                              required></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-xl font-semibold transition-colors disabled:opacity-50">
                        <i class="fas fa-save mr-2"></i>
                        {{ isSubmitting ? 'Збереження...' : 'Зберегти' }}
                    </button>
                    <button type="button" 
                            @click="cancelAdd"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-xl font-semibold transition-colors">
                        Скасувати
                    </button>
                </div>
            </form>
        </div>

        <div v-if="localFacts && localFacts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="(fact, index) in localFacts" :key="index" 
                 class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700 hover:shadow-xl transition-all duration-300 h-full
                     flex flex-col justify-between">
                <!-- Fact Icon -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                Факт {{ index + 1 }}
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                Цікавий факт
                            </div>
                        </div>
                    </div>
                    <!-- Fact Icon -->
                    <div class="mb-2">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z" fill="#F97316"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Fact Text -->
                <div class="text-lg text-slate-700 dark:text-slate-300 leading-relaxed font-medium mb-4">
                    {{ fact }}
                </div>
                
                <!-- Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Share Button -->
                    <button @click="shareFact(fact, index)"
                        class="flex items-center space-x-1 text-slate-600 dark:text-slate-400 hover:text-indigo-500 dark:hover:text-indigo-400 transition-colors">
                        <i class="fas fa-share-alt"></i>
                        <span class="text-sm">Поділитися</span>
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-lg">
                Цікаві факти про цю книгу ще не додані
            </p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'InterestingFacts',
    props: {
        facts: {
            type: Array,
            default: () => []
        },
        bookSlug: {
            type: String,
            required: true
        },
        currentUserId: {
            type: Number,
            default: null
        },
        isModerator: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            showAddForm: false,
            newFact: '',
            isSubmitting: false,
            localFacts: [...this.facts]
        };
    },
    computed: {
        isAuthenticated() {
            return this.currentUserId !== null;
        }
    },
    methods: {
        async addFact() {
            if (!this.newFact.trim()) return;
            
            this.isSubmitting = true;
            
            try {
                const response = await axios.post(`/books/${this.bookSlug}/facts`, {
                    fact: this.newFact.trim()
                });
                
                if (response.data.success) {
                    this.localFacts.push(this.newFact.trim());
                    this.newFact = '';
                    this.showAddForm = false;
                    this.showNotification('Факт успішно додано!', 'success');
                } else {
                    this.showNotification(response.data.message || 'Помилка при додаванні факту.', 'error');
                }
            } catch (error) {
                console.error('Error adding fact:', error);
                this.showNotification('Помилка при додаванні факту.', 'error');
            }
            
            this.isSubmitting = false;
        },
        cancelAdd() {
            this.newFact = '';
            this.showAddForm = false;
        },
        shareFact(fact, index) {
            const text = `Цікавий факт про книгу: ${fact}`;
            if (navigator.share) {
                navigator.share({
                    title: 'Цікавий факт',
                    text: text,
                    url: window.location.href
                }).catch(err => console.log('Error sharing:', err));
            } else {
                navigator.clipboard.writeText(text).then(() => {
                    this.showNotification('Факт скопійовано!', 'success');
                });
            }
        },
        showNotification(message, type) {
            // Создаем уведомление
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-semibold shadow-lg transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Показываем уведомление
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }, 100);
            
            // Убираем уведомление через 3 секунды
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    }
};
</script>
