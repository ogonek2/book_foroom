/**
 * Утиліта для кешування статусів читання книг в localStorage
 */
class BookStatusCache {
    constructor() {
        this.cacheKey = 'book_reading_statuses';
        this.cacheVersion = '1.0';
        this.versionKey = 'book_status_cache_version';
        this.maxAge = 24 * 60 * 60 * 1000; // 24 години в мілісекундах
    }

    /**
     * Отримати всі кешовані статуси
     */
    getAll() {
        try {
            const cached = localStorage.getItem(this.cacheKey);
            if (!cached) return {};

            const data = JSON.parse(cached);
            
            // Перевіряємо версію кешу
            const version = localStorage.getItem(this.versionKey);
            if (version !== this.cacheVersion) {
                this.clear();
                return {};
            }

            // Перевіряємо час життя кешу
            if (data.timestamp && Date.now() - data.timestamp > this.maxAge) {
                this.clear();
                return {};
            }

            return data.statuses || {};
        } catch (error) {
            console.error('Error reading book status cache:', error);
            return {};
        }
    }

    /**
     * Отримати статус для конкретної книги
     */
    get(bookId) {
        const statuses = this.getAll();
        return statuses[bookId] || null;
    }

    /**
     * Зберегти статус для книги
     */
    set(bookId, statusData) {
        try {
            const statuses = this.getAll();
            statuses[bookId] = {
                ...statusData,
                cachedAt: Date.now()
            };

            const data = {
                statuses,
                timestamp: Date.now()
            };

            localStorage.setItem(this.cacheKey, JSON.stringify(data));
            localStorage.setItem(this.versionKey, this.cacheVersion);
        } catch (error) {
            console.error('Error saving book status cache:', error);
            // Якщо localStorage переповнений, очищаємо старий кеш
            if (error.name === 'QuotaExceededError') {
                this.clear();
            }
        }
    }

    /**
     * Масове збереження статусів
     */
    setBatch(statusesMap) {
        try {
            const existingStatuses = this.getAll();
            const data = {
                statuses: {
                    ...existingStatuses,
                    ...statusesMap
                },
                timestamp: Date.now()
            };

            localStorage.setItem(this.cacheKey, JSON.stringify(data));
            localStorage.setItem(this.versionKey, this.cacheVersion);
        } catch (error) {
            console.error('Error saving book status cache batch:', error);
            if (error.name === 'QuotaExceededError') {
                this.clear();
            }
        }
    }

    /**
     * Видалити статус для книги
     */
    remove(bookId) {
        try {
            const statuses = this.getAll();
            delete statuses[bookId];

            const data = {
                statuses,
                timestamp: Date.now()
            };

            localStorage.setItem(this.cacheKey, JSON.stringify(data));
        } catch (error) {
            console.error('Error removing book status from cache:', error);
        }
    }

    /**
     * Очистити весь кеш
     */
    clear() {
        try {
            localStorage.removeItem(this.cacheKey);
            localStorage.removeItem(this.versionKey);
        } catch (error) {
            console.error('Error clearing book status cache:', error);
        }
    }

    /**
     * Перевірити чи є кешовані дані
     */
    has(bookId) {
        return this.get(bookId) !== null;
    }

    /**
     * Отримати список всіх кешованих bookId
     */
    getCachedBookIds() {
        return Object.keys(this.getAll());
    }
}

// Експортуємо singleton instance
export default new BookStatusCache();

