/**
 * Утиліта для кешування списків книг на сторінці індексу
 */
class BooksListCache {
    constructor() {
        this.cacheKey = 'books_list_cache';
        this.cacheVersion = '1.0';
        this.versionKey = 'books_list_cache_version';
        this.maxAge = 2 * 60 * 60 * 1000; // 2 години в мілісекундах
    }

    /**
     * Створити ключ кешу на основі параметрів запиту
     */
    getCacheKey(params) {
        const keyParts = [
            'page:' + (params.page || 1),
            'search:' + (params.search || ''),
            'category:' + (params.category || ''),
            'sort:' + (params.sort || 'rating'),
            'rating_min:' + (params.rating_min || 1),
            'rating_max:' + (params.rating_max || 10),
        ];
        return keyParts.join('|');
    }

    /**
     * Отримати всі кешовані списки
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

            // Очищаємо застарілі записи
            const now = Date.now();
            const validEntries = {};
            for (const [key, entry] of Object.entries(data.entries || {})) {
                if (entry.timestamp && (now - entry.timestamp) <= this.maxAge) {
                    validEntries[key] = entry;
                }
            }

            // Оновлюємо кеш, видаляючи застарілі записи
            if (Object.keys(validEntries).length !== Object.keys(data.entries || {}).length) {
                data.entries = validEntries;
                data.timestamp = now;
                localStorage.setItem(this.cacheKey, JSON.stringify(data));
            }

            return validEntries;
        } catch (error) {
            console.error('Error reading books list cache:', error);
            return {};
        }
    }

    /**
     * Отримати кешований список книг для конкретних параметрів
     */
    get(params) {
        const entries = this.getAll();
        const key = this.getCacheKey(params);
        const entry = entries[key];
        
        if (entry && entry.data) {
            return {
                books: entry.data.books || [],
                pagination: entry.data.pagination || null,
                timestamp: entry.timestamp
            };
        }
        
        return null;
    }

    /**
     * Зберегти список книг в кеш
     */
    set(params, booksData) {
        try {
            const entries = this.getAll();
            const key = this.getCacheKey(params);
            
            entries[key] = {
                data: {
                    books: booksData.books || [],
                    pagination: booksData.pagination || null
                },
                timestamp: Date.now()
            };

            const data = {
                entries,
                timestamp: Date.now()
            };

            localStorage.setItem(this.cacheKey, JSON.stringify(data));
            localStorage.setItem(this.versionKey, this.cacheVersion);
        } catch (error) {
            console.error('Error saving books list cache:', error);
            // Якщо localStorage переповнений, очищаємо старий кеш
            if (error.name === 'QuotaExceededError') {
                this.clearOldEntries();
            }
        }
    }

    /**
     * Очистити застарілі записи (старіше 2 годин)
     */
    clearOldEntries() {
        try {
            const entries = this.getAll();
            const now = Date.now();
            const validEntries = {};
            
            for (const [key, entry] of Object.entries(entries)) {
                if (entry.timestamp && (now - entry.timestamp) <= this.maxAge) {
                    validEntries[key] = entry;
                }
            }

            const data = {
                entries: validEntries,
                timestamp: now
            };

            localStorage.setItem(this.cacheKey, JSON.stringify(data));
        } catch (error) {
            console.error('Error clearing old entries:', error);
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
            console.error('Error clearing books list cache:', error);
        }
    }

    /**
     * Очистити кеш для конкретних параметрів (наприклад, при зміні фільтрів)
     */
    clearForParams(params) {
        try {
            const entries = this.getAll();
            const key = this.getCacheKey(params);
            delete entries[key];

            const data = {
                entries,
                timestamp: Date.now()
            };

            localStorage.setItem(this.cacheKey, JSON.stringify(data));
        } catch (error) {
            console.error('Error clearing cache for params:', error);
        }
    }

    /**
     * Перевірити чи є кешований список
     */
    has(params) {
        return this.get(params) !== null;
    }

    /**
     * Отримати розмір кешу (кількість записів)
     */
    getSize() {
        return Object.keys(this.getAll()).length;
    }
}

// Експортуємо singleton instance
export default new BooksListCache();

