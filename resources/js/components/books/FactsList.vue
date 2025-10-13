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

        <div v-if="localFacts && localFacts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
            <fact-card 
                v-for="fact in localFacts" 
                :key="fact.id"
                :fact="fact"
                :book-slug="bookSlug"
                :current-user-id="currentUserId"
                @show-notification="showNotification"
                @like-toggled="handleLikeToggled"
                @fact-deleted="handleFactDeleted"
                @fact-updated="handleFactUpdated">
            </fact-card>
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
import FactCard from './FactCard.vue';

export default {
    name: 'FactsList',
    components: {
        FactCard
    },
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
            localFacts: [...this.facts],
            showAddForm: false,
            newFact: '',
            isSubmitting: false,
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
                    content: this.newFact.trim()
                });
                
                if (response.data.success) {
                    this.localFacts.unshift(response.data.fact);
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
        handleFactDeleted(factId) {
            this.localFacts = this.localFacts.filter(fact => fact.id !== factId);
        },
        handleFactUpdated(updatedFact) {
            const index = this.localFacts.findIndex(f => f.id === updatedFact.id);
            if (index !== -1) {
                this.$set(this.localFacts, index, updatedFact);
            }
        },
        handleLikeToggled(data) {
            const fact = this.localFacts.find(f => f.id === data.factId);
            if (fact) {
                fact.is_liked_by_current_user = data.isLiked;
                fact.likes_count = data.likesCount;
            }
        },
        showNotification(message, type) {
            // Создаем уведомление
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg text-white font-semibold shadow-lg transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
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
