<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookReadingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            'rating' => $status ? $status->rating : null,
            'review' => $status ? $status->review : null,
            'started_at' => $status ? $status->started_at : null,
            'finished_at' => $status ? $status->finished_at : null,
        ]);
    }

    /**
     * Установить или обновить статус чтения книги
     */
    public function setStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:read,reading,want_to_read',
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
            'rating' => $request->rating,
            'review' => $request->review,
        ];

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
            $status = BookReadingStatus::create($data);
        }

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
        
        if (!in_array($status, ['read', 'reading', 'want_to_read'])) {
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

        return response()->json([
            'success' => true,
            'message' => 'Рейтинг и отзыв обновлены',
            'data' => $status
        ]);
    }
}