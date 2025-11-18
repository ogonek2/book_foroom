<template>
    <div class="relative" ref="searchContainer">
        <form @submit.prevent="handleSearch" class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-light-text-tertiary dark:text-dark-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input 
                type="text" 
                v-model="searchQuery"
                @input="handleInput"
                @focus="showSuggestions = true"
                @blur="handleBlur"
                @keydown.arrow-down="navigateDown"
                @keydown.arrow-up="navigateUp"
                @keydown.enter="selectSuggestion"
                @keydown.escape="hideSuggestions"
                class="block w-full pl-10 pr-20 py-2 border border-light-border dark:border-dark-border rounded-lg bg-light-bg-secondary dark:bg-dark-bg-secondary text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-colors duration-200" 
                :placeholder="placeholder"
                autocomplete="off">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <button 
                    type="submit"
                    class="text-brand-500 dark:text-brand-400 hover:text-brand-600 dark:hover:text-brand-500 font-medium text-sm transition-colors">
                    Пошук
                </button>
            </div>
        </form>

        <!-- Suggestions Dropdown -->
        <div 
            v-if="showSuggestions && suggestions.length > 0" 
            class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 max-h-96 overflow-y-auto"
            ref="suggestionsList">
            <div 
                v-for="(book, index) in suggestions" 
                :key="book.id"
                @click="selectBook(book)"
                @mouseenter="selectedIndex = index"
                :class="[
                    'flex items-center space-x-3 p-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors',
                    selectedIndex === index ? 'bg-gray-100 dark:bg-gray-700' : ''
                ]">
                <!-- Cover Image -->
                <div class="flex-shrink-0">
                    <img 
                        :src="book.cover_image || 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=80&h=120&fit=crop'"
                        :alt="book.title"
                        class="w-12 h-16 object-cover rounded shadow-sm">
                </div>
                
                <!-- Book Info -->
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                        {{ book.title }}
                    </h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 truncate">
                        {{ book.author }}
                    </p>
                    <div class="flex items-center space-x-2 mt-1">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="text-xs text-gray-600 dark:text-gray-400 ml-1">
                                {{ formatRating(book.rating) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'BookSearch',
    props: {
        placeholder: {
            type: String,
            default: 'Пошук книг...'
        },
        initialValue: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            searchQuery: this.initialValue,
            suggestions: [],
            showSuggestions: false,
            selectedIndex: -1,
            searchTimeout: null
        };
    },
    mounted() {
        // Close suggestions when clicking outside
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeDestroy() {
        document.removeEventListener('click', this.handleClickOutside);
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
    },
    methods: {
        handleInput() {
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }

            if (this.searchQuery.length < 2) {
                this.suggestions = [];
                this.showSuggestions = false;
                return;
            }

            this.searchTimeout = setTimeout(() => {
                this.fetchSuggestions();
            }, 300);
        },
        async fetchSuggestions() {
            try {
                const response = await axios.get('/api/books/search/suggestions', {
                    params: {
                        q: this.searchQuery,
                        limit: 5
                    }
                });
                this.suggestions = response.data.data || [];
                this.showSuggestions = this.suggestions.length > 0;
                this.selectedIndex = -1;
            } catch (error) {
                console.error('Error fetching suggestions:', error);
                this.suggestions = [];
            }
        },
        handleBlur() {
            // Delay to allow click events to fire
            setTimeout(() => {
                this.showSuggestions = false;
            }, 200);
        },
        handleClickOutside(event) {
            if (this.$refs.searchContainer && !this.$refs.searchContainer.contains(event.target)) {
                this.showSuggestions = false;
            }
        },
        navigateDown() {
            if (this.suggestions.length > 0) {
                this.selectedIndex = Math.min(this.selectedIndex + 1, this.suggestions.length - 1);
                this.scrollToSelected();
            }
        },
        navigateUp() {
            if (this.selectedIndex > 0) {
                this.selectedIndex--;
                this.scrollToSelected();
            }
        },
        scrollToSelected() {
            this.$nextTick(() => {
                if (this.$refs.suggestionsList && this.selectedIndex >= 0) {
                    const items = this.$refs.suggestionsList.children;
                    if (items[this.selectedIndex]) {
                        items[this.selectedIndex].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    }
                }
            });
        },
        selectSuggestion() {
            if (this.selectedIndex >= 0 && this.suggestions[this.selectedIndex]) {
                this.selectBook(this.suggestions[this.selectedIndex]);
            } else {
                this.handleSearch();
            }
        },
        selectBook(book) {
            window.location.href = `/books/${book.slug}`;
        },
        handleSearch() {
            if (this.searchQuery.trim()) {
                window.location.href = `/search?q=${encodeURIComponent(this.searchQuery.trim())}`;
            }
        },
        hideSuggestions() {
            this.showSuggestions = false;
        },
        formatRating(rating) {
            if (!rating || rating === 0) return '0.0';
            return parseFloat(rating).toFixed(1);
        }
    }
};
</script>

