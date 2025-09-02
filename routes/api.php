<?php

use App\Http\Controllers\Api\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('web')->group(function () {
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
});
