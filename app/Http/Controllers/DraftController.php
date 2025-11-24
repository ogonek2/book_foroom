<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Quote;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DraftController extends Controller
{
    /**
     * Publish a draft
     */
    public function publish($type, $id)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model || $model->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Чернетку не знайдено або у вас немає прав'
            ], 404);
        }

        if (!$model->is_draft) {
            return response()->json([
                'success' => false,
                'message' => 'Це не чернетка'
            ], 400);
        }

        $model->update(['is_draft' => false]);

        // Для рецензий обновляем рейтинги при публикации
        if ($type === 'review' && $model instanceof Review) {
            $book = $model->book;
            if ($book && $model->rating) {
                // Синхронизируем рейтинг с BookReadingStatus
                $readingStatus = \App\Models\BookReadingStatus::firstOrCreate(
                    [
                        'book_id' => $book->id,
                        'user_id' => Auth::id(),
                    ],
                    [
                        'status' => 'read',
                        'finished_at' => now(),
                    ]
                );

                $readingStatus->update(['rating' => $model->rating]);

                // Обновляем средний рейтинг книги
                $book->updateRating();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Чернетку опубліковано!'
        ]);
    }

    /**
     * Delete a draft
     */
    public function destroy($type, $id)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model || $model->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Чернетку не знайдено або у вас немає прав'
            ], 404);
        }

        if (!$model->is_draft) {
            return response()->json([
                'success' => false,
                'message' => 'Це не чернетка'
            ], 400);
        }

        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Чернетку видалено!'
        ]);
    }

    /**
     * Get the model instance
     */
    private function getModel($type, $id)
    {
        switch ($type) {
            case 'review':
                return Review::find($id);
            case 'quote':
                return Quote::find($id);
            case 'discussion':
                return Discussion::find($id);
            default:
                return null;
        }
    }
}
