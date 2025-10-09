<template>
    <div class="group cursor-pointer" @click="openBook">
        <div
            class="flex bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-lg overflow-hidden border border-gray-200/30 dark:border-gray-700/30 transition-all duration-300 transform hover:-translate-y-1">
            <div>
                <!-- Book Cover -->
                <img v-if="book.cover_image" :src="book.cover_image" :alt="book.title"
                    class="aspect-[3/4] object-cover rounded-lg shadow-md group-hover:shadow-lg transition-all duration-300"
                    @error="handleImageError" style="width: 120px; height: 170px;">
                <div v-else
                    class="w-full h-full bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z">
                        </path>
                    </svg>
                </div>
            </div>

            <div class="flex flex-col justify-between h-full p-4">
                <!-- Book Info -->
                <div class="text-left">
                    <!-- Status Badge -->
                    <div class="mb-2">
                        <span :class="statusBadgeClasses" class="text-xs px-2 py-1 rounded-full">
                            {{ statusText }}
                        </span>
                    </div>
                    <h3
                        class="text-gray-900 dark:text-white font-medium text-md group-hover:text-purple-600 dark:group-hover:text-purple-200 transition-colors">
                        {{ truncatedTitle }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">
                        {{ truncatedAuthor }}
                    </p>
                </div>
                <!-- Rating Badge -->
                <div v-if="book.rating && book.rating > 0"
                    class="absolute bottom-2 right-2 px-2 py-1">
                    <div class="flex items-center space-x-1">
                        <i class="fas fa-star text-yellow-400"></i>
                        <span class="text-gray-900 dark:text-white text-xs font-medium">{{ book.rating }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'UserLibraryBookCard',
    props: {
        book: {
            type: Object,
            required: true
        },
        readingStatus: {
            type: String,
            required: true
        }
    },
    computed: {
        bookUrl() {
            return `/books/${this.book.slug}`;
        },
        statusText() {
            switch (this.readingStatus) {
                case 'read':
                    return 'Прочитано';
                case 'reading':
                    return 'Читає';
                case 'want_to_read':
                    return 'Планує';
                default:
                    return 'Не визначено';
            }
        },
        statusBadgeClasses() {
            switch (this.readingStatus) {
                case 'read':
                    return 'bg-green-500 text-white';
                case 'reading':
                    return 'bg-blue-500 text-white';
                case 'want_to_read':
                    return 'bg-purple-500 text-white';
                default:
                    return 'bg-gray-500 text-white';
            }
        },
        truncatedTitle() {
            return this.book.title.length > 25 ? this.book.title.substring(0, 25) + '...' : this.book.title;
        },
        truncatedAuthor() {
            return this.book.author.length > 20 ? this.book.author.substring(0, 20) + '...' : this.book.author;
        }
    },
    methods: {
        openBook() {
            window.location.href = this.bookUrl;
        },
        handleImageError(event) {
            // Hide broken image and show placeholder
            event.target.style.display = 'none';
        }
    }
}
</script>

<style scoped>
/* Additional styles if needed */
</style>
