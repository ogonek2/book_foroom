<template>
    <div class="relative inline-block">
        <!-- Three dots button -->
        <button @click="toggleDropdown" 
                class="text-light-text-tertiary dark:text-dark-text-tertiary hover:text-light-text-primary dark:hover:text-dark-text-primary transition-colors p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700"
                :title="'Опції'">
            <i class="fas fa-ellipsis-v text-sm"></i>
        </button>

        <!-- Dropdown menu -->
        <div v-if="showDropdown" 
             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
             @click.stop>
            <!-- Report option -->
            <button @click="openReportModal" 
                    class="w-full px-4 py-2 text-left text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors first:rounded-t-lg">
                <i class="fas fa-flag mr-2"></i>
                Подати скаргу
            </button>
        </div>

        <!-- Report Modal -->
        <ReportModal 
            :show="showReportModal"
            :reportable-type="reportableType"
            :reportable-id="reportableId"
            :content-preview="contentPreview"
            :content-url="contentUrl"
            @close="closeReportModal" />
    </div>
</template>

<script>
import ReportModal from './ReportModal.vue';

export default {
    name: 'ReportButton',
    components: {
        ReportModal
    },
    props: {
        reportableType: {
            type: String,
            required: true
        },
        reportableId: {
            type: [Number, String],
            required: true
        },
        contentPreview: {
            type: String,
            default: ''
        },
        contentUrl: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            showDropdown: false,
            showReportModal: false
        };
    },
    methods: {
        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
        },

        openReportModal() {
            this.showDropdown = false;
            this.showReportModal = true;
        },

        closeReportModal() {
            this.showReportModal = false;
        }
    },

    mounted() {
        // Закрываем dropdown при клике вне его
        this.handleClickOutside = (e) => {
            if (this.$el && !this.$el.contains(e.target)) {
                this.showDropdown = false;
            }
        };
        document.addEventListener('click', this.handleClickOutside);
    },

    beforeDestroy() {
        if (this.handleClickOutside) {
            document.removeEventListener('click', this.handleClickOutside);
        }
    }
};
</script>

<style scoped>
/* Дополнительные стили при необходимости */
</style>
