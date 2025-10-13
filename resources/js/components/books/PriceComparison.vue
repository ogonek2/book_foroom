<template>
    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 dark:border-slate-700/30 p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                Порівняйте ціни
            </h3>
        </div>

        <div v-if="prices && prices.length > 0" class="space-y-4">
            <div v-for="price in sortedPrices" :key="price.id" 
                 class="bg-white/80 dark:bg-slate-800/80 rounded-xl p-6 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                <div class="flex items-center justify-between">
                    <!-- Store Info -->
                    <div class="flex items-center space-x-4">
                        <!-- Store Logo -->
                        <div class="w-12 h-12 rounded-full bg-white dark:bg-gray-600 flex items-center justify-center shadow-sm">
                            <img v-if="price.bookstore.logo_url" 
                                 :src="price.bookstore.logo_url" 
                                 :alt="price.bookstore.name"
                                 class="w-8 h-8 rounded-full object-cover">
                            <div v-else 
                                 class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                                {{ price.bookstore.name.charAt(0).toUpperCase() }}
                            </div>
                        </div>
                        
                        <!-- Store Details -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ price.bookstore.name }}
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ price.bookstore.description || 'Книжковий магазин' }}
                            </p>
                        </div>
                    </div>

                    <!-- Price and Link -->
                    <div class="flex items-center space-x-4">
                        <!-- Price -->
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ price.formatted_price }}
                            </div>
                            <div v-if="!price.is_available" class="text-sm text-red-500 dark:text-red-400">
                                Немає в наявності
                            </div>
                            <div v-else class="text-sm text-green-500 dark:text-green-400">
                                В наявності
                            </div>
                        </div>

                        <!-- Link Button -->
                        <a :href="price.product_url" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-sm">
                            На сайт
                        </a>
                    </div>
                </div>
            </div>

            <!-- Show More Button -->
            <div class="text-center pt-4">
                <button @click="showAllPrices = !showAllPrices"
                        class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                    {{ showAllPrices ? 'Показати менше' : 'Показати більше пропозицій' }}
                </button>
            </div>
        </div>

        <div v-else class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
            <p class="text-gray-500 dark:text-gray-400 text-lg">
                Ціни для цієї книги ще не додані
            </p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'PriceComparison',
    props: {
        prices: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            showAllPrices: false,
            maxVisiblePrices: 3
        };
    },
    computed: {
        sortedPrices() {
            // Сортируем цены по возрастанию, доступные товары в начале
            const sorted = [...this.prices].sort((a, b) => {
                // Сначала доступные товары
                if (a.is_available && !b.is_available) return -1;
                if (!a.is_available && b.is_available) return 1;
                
                // Затем по цене
                return a.price - b.price;
            });

            // Показываем только первые maxVisiblePrices если не показаны все
            if (!this.showAllPrices && sorted.length > this.maxVisiblePrices) {
                return sorted.slice(0, this.maxVisiblePrices);
            }
            
            return sorted;
        }
    }
};
</script>
