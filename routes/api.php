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
    // Маршруты для книг
    Route::prefix('books')->name('books.')->group(function () {
        // Получение ID книги по slug
        Route::get('/{slug}/id', [App\Http\Controllers\BookController::class, 'getIdBySlug'])->name('id');
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
        // Получить статус чтения конкретной книги
        Route::get('/book/{id}', [BookReadingStatusController::class, 'getStatus'])->name('get');
        
        // Установить/обновить статус чтения
        Route::post('/book/{id}', [BookReadingStatusController::class, 'setStatus'])->name('set');
        
        // Удалить статус чтения
        Route::delete('/book/{id}', [BookReadingStatusController::class, 'removeStatus'])->name('remove');
        
        // Получить книги по статусу
        Route::get('/books/{status}', [BookReadingStatusController::class, 'getBooksByStatus'])->name('books');
        
        // Получить статистику чтения
        Route::get('/stats', [BookReadingStatusController::class, 'getReadingStats'])->name('stats');
        
        // Обновить рейтинг и отзыв
        Route::put('/book/{id}/review', [BookReadingStatusController::class, 'updateReview'])->name('review');
    });

    // Маршруты для жалоб
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::post('/', [ReportController::class, 'store'])->name('store');
        Route::get('/types', [ReportController::class, 'getTypes'])->name('types');
    });
});
