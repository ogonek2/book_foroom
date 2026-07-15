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

    // Always revalidate with server so removals from the cabinet stay in sync
    const cachedStatuses = {};
    for (const bookId of bookIds) {
        const cached = bookStatusCache.get(bookId);
        if (cached) {
            cachedStatuses[bookId] = cached;
        }
    }

    try {
        const response = await axios.post('/api/reading-status/batch', {
            book_ids: bookIds
        });

        if (response.data && response.data.statuses) {
            const statusesToCache = {};
            for (const [bookId, statusData] of Object.entries(response.data.statuses)) {
                if (statusData && statusData.status) {
                    statusesToCache[bookId] = statusData;
                    bookStatusCache.set(bookId, statusData);
                } else {
                    bookStatusCache.remove(bookId);
                    delete cachedStatuses[bookId];
                }
            }

            return {
                ...cachedStatuses,
                ...statusesToCache,
            };
        }
    } catch (error) {
        console.error('Error loading book statuses batch:', error);
    }

    return cachedStatuses;
}
