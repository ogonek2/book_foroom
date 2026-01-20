<template>
    <div v-if="show" class="fixed inset-0 overflow-y-auto" style="z-index: 9999 !important;" @click.self="close">
        <div class="flex min-h-screen items-center justify-center p-4">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <!-- Modal -->
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full max-h-[90vh] overflow-hidden" style="z-index: 10000 !important;">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-light-text-primary dark:text-dark-text-primary">
                        Поскаржитись на контент
                    </h3>
                    <button @click="close" 
                            class="text-light-text-tertiary dark:text-dark-text-tertiary hover:text-light-text-primary dark:hover:text-dark-text-primary transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-4">
                    <!-- Content Preview -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-light-text-secondary dark:text-dark-text-secondary mb-2">
                            Контент:
                        </h4>
                        <p class="text-sm text-light-text-primary dark:text-dark-text-primary">
                            {{ contentPreview }}
                        </p>
                    </div>

                    <!-- Report Type -->
                    <div>
                        <label class="block text-sm font-medium text-light-text-primary dark:text-dark-text-primary mb-2">
                            Причина скарги *
                        </label>
                        <select v-model="reportType" 
                                class="w-full px-3 py-2 bg-light-bg-secondary dark:bg-gray-700 border border-light-border dark:border-dark-border rounded-lg text-light-text-primary dark:text-dark-text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                            <option value="">Оберіть причину скарги</option>
                            <option v-for="(label, value) in reportTypes" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="block text-sm font-medium text-light-text-primary dark:text-dark-text-primary mb-2">
                            Додаткова інформація
                        </label>
                        <textarea v-model="reason" 
                                  rows="4" 
                                  placeholder="Опишіть причину скарги (необов'язково)"
                                  class="w-full px-3 py-2 bg-light-bg-secondary dark:bg-gray-700 border border-light-border dark:border-dark-border rounded-lg text-light-text-primary dark:text-dark-text-primary placeholder-light-text-tertiary dark:placeholder-dark-text-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none">
                        </textarea>
                        <p class="mt-2 text-xs text-light-text-tertiary dark:text-dark-text-tertiary">
                            Скарги розглядаються модераторами. Подання безпідставних скарг може призвести до обмежень.
                        </p>
                    </div>

                    <!-- Error Message -->
                    <div v-if="errorMessage" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                        <p class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</p>
                    </div>

                    <!-- Success Message -->
                    <div v-if="successMessage" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                        <p class="text-sm text-green-600 dark:text-green-400">{{ successMessage }}</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700">
                    <button @click="close" 
                            class="px-4 py-2 text-light-text-secondary dark:text-dark-text-secondary hover:text-light-text-primary dark:hover:text-dark-text-primary transition-colors">
                        Скасувати
                    </button>
                    <button @click="submitReport" 
                            :disabled="!reportType || isSubmitting"
                            class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ isSubmitting ? 'Відправляємо...' : 'Подати скаргу' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ReportModal',
    props: {
        show: {
            type: Boolean,
            default: false
        },
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
            reportType: '',
            reason: '',
            reportTypes: {},
            isSubmitting: false,
            errorMessage: '',
            successMessage: ''
        };
    },
    mounted() {
        this.loadReportTypes();
    },
    methods: {
        async loadReportTypes() {
            try {
                const response = await axios.get('/api/reports/types');
                this.reportTypes = response.data.types;
            } catch (error) {
                console.error('Error loading report types:', error);
                // Fallback types
                this.reportTypes = {
                    'spam': 'Спам',
                    'harassment': 'Образи, булінг, переслідування',
                    'inappropriate': 'Образливий або неприйнятний контент',
                    'copyright': 'Порушення авторського права',
                    'fake': 'Неправдива або оманлива інформація',
                    'hate_speech': 'Розпалювання ненависті',
                    'violence': 'Жорстокий або шокуючий контент',
                    'adult_content': 'Контент для дорослих (18+)',
                    'other': 'Інше'
                };
            }
        },

        async submitReport() {
            if (!this.reportType) {
                this.errorMessage = 'Будь ласка, виберіть причину скарги';
                return;
            }

            this.isSubmitting = true;
            this.errorMessage = '';
            this.successMessage = '';

            try {
                const response = await axios.post('/api/reports', {
                    reportable_type: this.reportableType,
                    reportable_id: this.reportableId,
                    type: this.reportType,
                    reason: this.reason,
                    content_url: this.contentUrl
                });

                if (response.data.success) {
                    this.successMessage = response.data.message;
                    
                    // Закрываем модальное окно через 2 секунды
                    setTimeout(() => {
                        this.close();
                        this.resetForm();
                    }, 2000);
                } else {
                    this.errorMessage = response.data.message || 'Помилка при відправці скарги';
                }
            } catch (error) {
                console.error('Error submitting report:', error);
                
                if (error.response && error.response.data && error.response.data.message) {
                    this.errorMessage = error.response.data.message;
                } else {
                    this.errorMessage = 'Помилка при відправці скарги. Спробуйте ще раз.';
                }
            } finally {
                this.isSubmitting = false;
            }
        },

        close() {
            this.$emit('close');
        },

        resetForm() {
            this.reportType = '';
            this.reason = '';
            this.errorMessage = '';
            this.successMessage = '';
            this.isSubmitting = false;
        }
    },

    watch: {
        show(newVal) {
            if (!newVal) {
                // Сбрасываем форму при закрытии
                this.resetForm();
            }
        }
    }
};
</script>

<style scoped>
/* Дополнительные стили при необходимости */
</style>

<style>
/* Глобальные стили для модального окна жалоб */
.report-modal-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    z-index: 9999 !important;
    background-color: rgba(0, 0, 0, 0.5);
}

.report-modal-content {
    z-index: 10000 !important;
}
</style>
