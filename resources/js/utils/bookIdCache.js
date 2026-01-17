/**
 * Утиліта для кешування ID книг по slug
 */
class BookIdCache {
    constructor() {
        this.cacheKey = 'book_ids_cache';
        this.cacheVersion = '1.0';
        this.versionKey = 'book_id_cache_version';
        this.maxAge = 7 * 24 * 60 * 60 * 1000; // 7 днів (ID книг рідко змінюються)
    }

    /**
     * Отримати всі кешовані ID
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

            return data.ids || {};
        } catch (error) {
            console.error('Error reading book ID cache:', error);
            return {};
        }
    }

    /**
     * Отримати ID для конкретного slug
     */
    get(slug) {
        const ids = this.getAll();
        return ids[slug] || null;
    }

    /**
     * Зберегти ID для slug
     */
    set(slug, bookId) {
        try {
            const ids = this.getAll();
            ids[slug] = bookId;

            const data = {
                ids,
                timestamp: Date.now()
            };

            localStorage.setItem(this.cacheKey, JSON.stringify(data));
            localStorage.setItem(this.versionKey, this.cacheVersion);
        } catch (error) {
            console.error('Error saving book ID cache:', error);
            // Якщо localStorage переповнений, очищаємо старий кеш
            if (error.name === 'QuotaExceededError') {
                this.clear();
            }
        }
    }

    /**
     * Масове збереження ID
     */
    setBatch(idsMap) {
        try {
            const existingIds = this.getAll();
            const data = {
                ids: {
                    ...existingIds,
                    ...idsMap
                },
                timestamp: Date.now()
            };

            localStorage.setItem(this.cacheKey, JSON.stringify(data));
            localStorage.setItem(this.versionKey, this.cacheVersion);
        } catch (error) {
            console.error('Error saving book ID cache batch:', error);
            if (error.name === 'QuotaExceededError') {
                this.clear();
            }
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
            console.error('Error clearing book ID cache:', error);
        }
    }

    /**
     * Перевірити чи є кешований ID
     */
    has(slug) {
        return this.get(slug) !== null;
    }
}

// Експортуємо singleton instance
export default new BookIdCache();

