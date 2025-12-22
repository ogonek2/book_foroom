<template>
    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/30 p-6 hover:shadow-2xl transition-all duration-300 cursor-pointer"
         @click="openLibrary">
        <!-- Header with author info -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <!-- Avatar -->
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                    <img :src="library.user.avatar" alt="Avatar" class="w-10 h-10 rounded-full" v-if="library.user.avatar">
                    <span class="text-white font-bold text-sm" v-else>{{ library.user.name.charAt(0) }}</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ library.user.name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ formatDate(library.created_at) }}</p>
                </div>
            </div>
        </div>

        <!-- Library title -->
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3 line-clamp-1">{{ library.name }}</h3>

        <!-- Library description -->
        <!-- <p v-if="library.description" class="text-sm text-slate-600 dark:text-slate-400 mb-4 line-clamp-2">{{ library.description }}</p> -->

        <!-- Books preview -->
        <div class="mb-4">
            <div class="flex space-x-2">
                <!-- First book cover -->
                <div v-if="library.books && library.books.length > 0" class="flex-shrink-0" style="width: 32%">
                    <img :src="library.books[0].cover_image || defaultCover"
                         :alt="library.books[0].title"
                         class="object-cover rounded-lg shadow-md w-full h-[180px]" style="aspect-ratio: 2 / 3;">
                </div>
                <div v-else class="bg-gray-300 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                    </svg>
                </div>

                <!-- Second book cover -->
                <div v-if="library.books && library.books.length > 1" class="flex-shrink-0"  style="width: 32%">
                    <img :src="library.books[1].cover_image || defaultCover"
                         :alt="library.books[1].title"
                         class="object-cover rounded-lg shadow-md w-full h-[180px]" style="aspect-ratio: 2 / 3;">
                </div>
                <div v-else class="bg-gray-300 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                    </svg>
                </div>

                <!-- Additional books counter -->
                <div v-if="library.books_count > 2" 
                     class="w-16 bg-gradient-to-br from-orange-400 to-red-500 rounded-lg flex items-center justify-center backdrop-blur-sm"  style="width: 32%">
                    <span class="text-white font-bold text-sm">+{{ library.books_count - 2 }}</span>
                </div>
                <div v-else class="w-16 bg-gray-300 dark:bg-gray-600 rounded-lg flex items-center justify-center" style="width: 32%">
                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Like button -->
                <button @click.stop="toggleLike" 
                        class="flex items-center space-x-1 text-slate-600 dark:text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-4 h-4" :class="{ 'text-red-500': isLiked }" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm">{{ likesCount }}</span>
                </button>

                <!-- Save button -->
                <button v-if="isAuthenticated" @click.stop="toggleSave" 
                        class="flex items-center space-x-1 text-slate-600 dark:text-slate-400 hover:text-blue-500 transition-colors">
                    <svg class="w-4 h-4" :class="{ 'text-blue-500': isSaved }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    <span class="text-sm">Зберегти</span>
                </button>
            </div>

            <!-- Share button -->
            <button @click.stop="shareLibrary" 
                    class="flex items-center space-x-1 text-slate-600 dark:text-slate-400 hover:text-green-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                </svg>
                <span class="text-sm">Поділитись</span>
            </button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'LibraryCollection',
    props: {
        library: {
            type: Object,
            required: true
        },
        isAuthenticated: {
            type: Boolean,
            default: false
        },
        isLiked: {
            type: Boolean,
            default: false
        },
        isSaved: {
            type: Boolean,
            default: false
        },
        likesCount: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            defaultCover: 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=200&h=300&fit=crop&crop=center'
        }
    },
    methods: {
        formatDate(date) {
            const d = new Date(date);
            return d.toLocaleDateString('uk-UA');
        },
        openLibrary() {
            window.location.href = `/libraries/${this.library.id}`;
        },
        toggleMenu() {
            // TODO: Implement menu functionality
            console.log('Toggle menu for library:', this.library.id);
        },
        async toggleLike() {
            if (!this.isAuthenticated) {
                return;
            }

            try {
                const response = await axios.post(`/libraries/${this.library.id}/like`);
                if (response.data.success) {
                    this.$emit('liked', {
                        libraryId: this.library.id,
                        isLiked: response.data.is_liked,
                        likesCount: response.data.likes_count
                    });
                }
            } catch (error) {
                console.error('Error toggling like:', error);
            }
        },
        async toggleSave() {
            if (!this.isAuthenticated) {
                return;
            }

            try {
                const response = await axios.post(`/libraries/${this.library.id}/save`, {}, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                if (response.data.success) {
                    this.$emit('saved', {
                        libraryId: this.library.id,
                        isSaved: response.data.is_saved
                    });
                }
            } catch (error) {
                console.error('Error toggling save:', error);
            }
        },
        async shareLibrary() {
            const { shareContent } = await import('../utils/shareHelper');
            await shareContent({
                title: this.library.name,
                text: this.library.description || `Добірка "${this.library.name}" від ${this.library.user.name}`,
                url: `${window.location.origin}/libraries/${this.library.id}`
            });
        }
    }
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
