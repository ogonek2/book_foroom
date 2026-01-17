<?php

use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\BookReadingStatusController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('web')->group(function () {
    Route::get('/quotes/featured', [App\Http\Controllers\Api\QuoteController::class, 'featured'])->name('quotes.featured');

    // Маршруты для книг
    Route::prefix('books')->name('books.')->group(function () {
        // Получение ID книги по slug
        Route::get('/{slug}/id', [App\Http\Controllers\BookController::class, 'getIdBySlug'])->name('id');
        // Поиск с подсказками
        Route::get('/search/suggestions', [App\Http\Controllers\BookController::class, 'searchSuggestions'])->name('search.suggestions');
        // Получение библиотек, в которых находится книга (требует авторизации)
        Route::middleware(['auth'])->get('/{slug}/libraries', [App\Http\Controllers\LibraryController::class, 'getBookLibraries'])->name('libraries');
    });

    // Маршруты для рецензий
    Route::prefix('reviews')->name('reviews.')->group(function () {
        // Получение ответов на рецензию
        Route::get('/{review}/replies', [ReviewController::class, 'getReplies'])->name('replies');
        
        // Лайки и дизлайки для рецензий
        Route::post('/{review}/like', [ReviewController::class, 'like'])->name('like');
        Route::post('/{review}/dislike', [ReviewController::class, 'dislike'])->name('dislike');
        
        // Удаление рецензий и ответов
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
    });

    // Маршруты для статусов чтения книг
    Route::middleware(['web', 'auth'])->prefix('reading-status')->name('reading-status.')->group(function () {
        // Получить статистику чтения (должен быть перед /{id})
        Route::get('/stats', [BookReadingStatusController::class, 'getReadingStats'])->name('stats');
        
        // Получить книги по статусу (должен быть перед /{id})
        Route::get('/books/{status}', [BookReadingStatusController::class, 'getBooksByStatus'])->name('books');
        
        // Масове завантаження статусів для списку книг
        Route::post('/batch', [BookReadingStatusController::class, 'getBatchStatuses'])->name('batch');
        
        // Получить статус чтения конкретной книги
        Route::get('/book/{id}', [BookReadingStatusController::class, 'getStatus'])->name('get');
        
        // Установить/обновить статус чтения
        Route::post('/book/{id}', [BookReadingStatusController::class, 'setStatus'])->name('set');
        
        // Удалить статус чтения
        Route::delete('/book/{id}', [BookReadingStatusController::class, 'removeStatus'])->name('remove');
        
        // Обновить рейтинг и отзыв
        Route::put('/book/{id}/review', [BookReadingStatusController::class, 'updateReview'])->name('review');
        
        // Получить статус чтения по ID (должен быть в конце, после всех специфичных роутов)
        Route::get('/status/{id}', [BookReadingStatusController::class, 'show'])->name('show');
        
        // Обновить статус чтения по ID
        Route::put('/status/{id}', [BookReadingStatusController::class, 'update'])->name('update');
    });

    // Маршруты для жалоб
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::post('/', [ReportController::class, 'store'])->name('store');
        Route::get('/types', [ReportController::class, 'getTypes'])->name('types');
    });

    // Маршруты для поиска пользователей (для упоминаний)
    Route::get('/users/search', function (Request $request) {
        $query = $request->get('q', '');
        
        // Если запрос пустой, возвращаем топ-5 активных пользователей
        if (empty($query)) {
            $users = \App\Models\User::orderBy('created_at', 'desc')
                ->select('id', 'username', 'name', 'avatar')
                ->limit(5)
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'username' => $user->username,
                        'name' => $user->name,
                        'avatar' => $user->avatar,
                        'avatar_display' => $user->avatar_display ?? null,
                    ];
                });
            return response()->json(['users' => $users]);
        }

        // Если есть запрос, ищем по username и name
        $users = \App\Models\User::where(function($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->select('id', 'username', 'name', 'avatar')
            ->limit(5)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->name,
                    'avatar' => $user->avatar,
                    'avatar_display' => $user->avatar_display ?? null,
                ];
            });

        return response()->json(['users' => $users]);
    })->name('users.search');

    // Маршрут для загрузки изображений
    Route::middleware(['auth'])->post('/images/upload', function (Request $request) {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $file = $request->file('image');
        $url = \App\Helpers\CDNUploader::uploadFile($file, 'discussions');

        if (!$url) {
            return response()->json([
                'success' => false,
                'message' => 'Не вдалося завантажити зображення.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'url' => $url
        ]);
    })->name('images.upload');
});
