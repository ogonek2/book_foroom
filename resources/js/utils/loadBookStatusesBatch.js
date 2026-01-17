/**
 * Утиліта для масового завантаження статусів книг
 */
import axios from 'axios';
import bookStatusCache from './bookStatusCache';

/**
 * Завантажити статуси для списку книг
 * @param {Array} books - Масив об'єктів книг з полями id або slug
 * @returns {Promise<Object>} - Об'єкт зі статусами { bookId: statusData }
 */
export async function loadBookStatusesBatch(books) {
    if (!books || books.length === 0) {
        return {};
    }

    // Спочатку отримуємо ID всіх книг
    const bookIds = [];
    const slugToIdMap = {};
    
    for (const book of books) {
        if (book.id) {
            bookIds.push(book.id);
        } else if (book.slug) {
            // Якщо немає ID, отримуємо його з кешу або з сервера
            try {
                const cachedStatus = bookStatusCache.get(book.slug);
                if (cachedStatus && cachedStatus.bookId) {
                    bookIds.push(cachedStatus.bookId);
                    slugToIdMap[book.slug] = cachedStatus.bookId;
                } else {
                    // Отримуємо ID з сервера
                    const response = await axios.get(`/api/books/${book.slug}/id`);
                    if (response.data && response.data.id) {
                        bookIds.push(response.data.id);
                        slugToIdMap[book.slug] = response.data.id;
                    }
                }
            } catch (error) {
                console.error(`Error getting book ID for slug ${book.slug}:`, error);
            }
        }
    }

    if (bookIds.length === 0) {
        return {};
    }

    // Перевіряємо кеш для всіх книг
    const cachedStatuses = {};
    const missingBookIds = [];
    
    for (const bookId of bookIds) {
        const cached = bookStatusCache.get(bookId);
        if (cached) {
            cachedStatuses[bookId] = cached;
        } else {
            missingBookIds.push(bookId);
        }
    }

    // Якщо всі статуси є в кеші, повертаємо їх
    if (missingBookIds.length === 0) {
        return cachedStatuses;
    }

    // Завантажуємо відсутні статуси з сервера одним запитом
    try {
        const response = await axios.post('/api/reading-status/batch', {
            book_ids: missingBookIds
        });

        if (response.data && response.data.statuses) {
            // Оновлюємо кеш
            const statusesToCache = {};
            for (const [bookId, statusData] of Object.entries(response.data.statuses)) {
                if (statusData) {
                    statusesToCache[bookId] = statusData;
                    bookStatusCache.set(bookId, statusData);
                }
            }
            
            // Об'єднуємо кешовані та нові статуси
            return {
                ...cachedStatuses,
                ...statusesToCache
            };
        }
    } catch (error) {
        console.error('Error loading book statuses batch:', error);
    }

    return cachedStatuses;
}

