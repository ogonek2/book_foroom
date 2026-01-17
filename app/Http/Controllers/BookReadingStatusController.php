<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookReadingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class BookReadingStatusController extends Controller
{
    /**
     * Получить статус чтения книги для текущего пользователя
     */
    public function getStatus(Request $request, $id)
    {
        $user = Auth::user();
        $book = Book::findOrFail($id);
        $status = $book->getReadingStatusForUser($user->id);
        
        return response()->json([
            'status' => $status ? $status->status : null,
            'times_read' => $status ? $status->times_read : 1,
            'reading_language' => $status ? $status->reading_language : null,
            'rating' => $status ? $status->rating : null,
            'review' => $status ? $status->review : null,
            'started_at' => $status ? $status->started_at : null,
            'finished_at' => $status ? $status->finished_at : null,
        ]);
    }

    /**
     * Масове завантаження статусів для списку книг
     */
    public function getBatchStatuses(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'book_ids' => 'required|array',
            'book_ids.*' => 'integer|exists:books,id'
        ]);

        $bookIds = $request->input('book_ids', []);
        
        if (empty($bookIds)) {
            return response()->json(['statuses' => []]);
        }

        // Завантажуємо всі статуси одним запитом
        $statuses = BookReadingStatus::where('user_id', $user->id)
            ->whereIn('book_id', $bookIds)
            ->get()
            ->keyBy('book_id');

        $result = [];
        foreach ($bookIds as $bookId) {
            $status = $statuses->get($bookId);
            $result[$bookId] = $status ? [
                'status' => $status->status,
                'times_read' => $status->times_read,
                'reading_language' => $status->reading_language,
                'rating' => $status->rating,
                'review' => $status->review,
                'started_at' => $status->started_at,
                'finished_at' => $status->finished_at,
            ] : null;
        }

        return response()->json([
            'statuses' => $result
        ]);
    }

    /**
     * Установить или обновить статус чтения книги
     */
    public function setStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:read,reading,want_to_read,abandoned',
                'times_read' => 'nullable|integer|min:1',
                'reading_language' => 'nullable|string|max:10',
                'rating' => 'nullable|integer|min:1|max:10',
                'review' => 'nullable|string|max:2000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }
            
            $book = Book::findOrFail($id);
            $status = $book->getReadingStatusForUser($user->id);

        $data = [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => $request->status,
        ];

        // Обновляем количество прочтений
        if ($request->has('times_read')) {
            $data['times_read'] = $request->times_read;
        } elseif ($status && $status->times_read) {
            $data['times_read'] = $status->times_read;
        } else {
            $data['times_read'] = 1;
        }

        // Обновляем язык чтения
        if ($request->has('reading_language')) {
            $data['reading_language'] = $request->reading_language;
        } elseif ($status && $status->reading_language) {
            $data['reading_language'] = $status->reading_language;
        }

        // Обновляем рейтинг только если он передан в запросе
        if ($request->has('rating')) {
            $data['rating'] = $request->rating;
        } elseif ($status && $status->rating) {
            // Сохраняем существующий рейтинг, если новый не передан
            $data['rating'] = $status->rating;
        }

        // Обновляем отзыв только если он передан в запросе
        if ($request->has('review')) {
            $data['review'] = $request->review;
        } elseif ($status && $status->review) {
            // Сохраняем существующий отзыв, если новый не передан
            $data['review'] = $status->review;
        }

        // Устанавливаем даты в зависимости от статуса
        if ($request->status === 'reading' && !$status) {
            $data['started_at'] = now();
        } elseif ($request->status === 'read') {
            $data['finished_at'] = now();
            if (!$status || !$status->started_at) {
                $data['started_at'] = now();
            }
        }

        if ($status) {
            $status->update($data);
        } else {
            // Устанавливаем значения по умолчанию при создании
            if (!isset($data['times_read'])) {
                $data['times_read'] = 1;
            }
            $status = BookReadingStatus::create($data);
        }

        // Кешуємо дані книги для швидкого оновлення компонентів
        $this->cacheBookData($book);

            return response()->json([
                'success' => true,
                'message' => 'Статус чтения обновлен',
                'data' => $status
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in setStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при сохранении статуса: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удалить статус чтения книги
     */
    public function removeStatus(Request $request, $id)
    {
        $user = Auth::user();
        $book = Book::findOrFail($id);
        $status = $book->getReadingStatusForUser($user->id);

        if ($status) {
            $status->delete();
            
            // Очищаємо кеш книги
            $this->clearBookCache($book);
            
            return response()->json([
                'success' => true,
                'message' => 'Статус чтения удален'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Статус чтения не найден'
        ], 404);
    }

    /**
     * Получить книги пользователя по статусу
     */
    public function getBooksByStatus(Request $request, $status)
    {
        $user = Auth::user();
        
        if (!in_array($status, ['read', 'reading', 'want_to_read', 'abandoned'])) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный статус'
            ], 400);
        }

        $books = $user->readingStatuses()
            ->where('status', $status)
            ->with('book')
            ->orderBy('updated_at', 'desc')
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $books
        ]);
    }

    /**
     * Получить статистику чтения пользователя
     */
    public function getReadingStats(Request $request)
    {
        $user = Auth::user();
        
        $stats = [
            'read_count' => $user->readingStatuses()->where('status', 'read')->count(),
            'reading_count' => $user->readingStatuses()->where('status', 'reading')->count(),
            'want_to_read_count' => $user->readingStatuses()->where('status', 'want_to_read')->count(),
            'abandoned_count' => $user->readingStatuses()->where('status', 'abandoned')->count(),
            'total_books' => $user->readingStatuses()->count(),
            'average_rating' => $user->readingStatuses()
                ->where('status', 'read')
                ->whereNotNull('rating')
                ->avg('rating'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Обновить рейтинг и отзыв для прочитанной книги
     */
    public function updateReview(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:10',
            'review' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $book = Book::findOrFail($id);
        $status = $book->getReadingStatusForUser($user->id);

        if (!$status || $status->status !== 'read') {
            return response()->json([
                'success' => false,
                'message' => 'Книга должна быть прочитана для добавления рейтинга'
            ], 400);
        }

        $status->update([
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        // Кешуємо дані книги для швидкого оновлення компонентів
        $this->cacheBookData($book);

        return response()->json([
            'success' => true,
            'message' => 'Рейтинг и отзыв обновлены',
            'data' => $status
        ]);
    }

    /**
     * Обновить статус чтения книги (полное обновление)
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:read,reading,want_to_read,abandoned',
                'times_read' => 'nullable|integer|min:1',
                'reading_language' => 'nullable|string|max:10',
                'rating' => 'nullable|integer|min:1|max:10',
                'review' => 'nullable|string|max:2000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }
            
            $status = BookReadingStatus::findOrFail($id);
            
            // Проверяем, что статус принадлежит текущему пользователю
            if ($status->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нет доступа к этому статусу'
                ], 403);
            }

            $data = [
                'status' => $request->status,
                'times_read' => $request->times_read ?? $status->times_read ?? 1,
                'reading_language' => $request->reading_language ?? $status->reading_language,
            ];

            // Обновляем рейтинг если передан
            if ($request->has('rating')) {
                $data['rating'] = $request->rating;
            }

            // Обновляем отзыв если передан
            if ($request->has('review')) {
                $data['review'] = $request->review;
            }

            // Устанавливаем даты в зависимости от статуса
            if ($request->status === 'reading' && !$status->started_at) {
                $data['started_at'] = now();
            } elseif ($request->status === 'read') {
                $data['finished_at'] = now();
                if (!$status->started_at) {
                    $data['started_at'] = now();
                }
            }

            $status->update($data);

            // Кешуємо дані книги для швидкого оновлення компонентів
            $this->cacheBookData($status->book);

            return response()->json([
                'success' => true,
                'message' => 'Статус обновлен',
                'data' => $status->fresh()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении статуса: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить статус чтения по ID
     */
    public function show($id)
    {
        $user = Auth::user();
        $status = BookReadingStatus::with(['book', 'book.author'])->findOrFail($id);
        
        // Проверяем, что статус принадлежит текущему пользователю
        if ($status->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Нет доступа к этому статусу'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $status
        ]);
    }

    /**
     * Кешує дані книги для швидкого оновлення компонентів
     */
    protected function cacheBookData(Book $book)
    {
        $book->cacheBookData();
    }

    /**
     * Очищає кеш книги
     */
    protected function clearBookCache(Book $book)
    {
        $book->clearBookCache();
    }
}