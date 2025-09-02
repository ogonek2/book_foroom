<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Book Routes
|--------------------------------------------------------------------------
*/

Route::prefix('books')->name('books.')->group(function () {
    // Основные маршруты для книг
    Route::get('/', [BookController::class, 'index'])->name('index');
    Route::get('/{book}', [BookController::class, 'show'])->name('show');
    
    // Маршруты для рецензий
    Route::prefix('{book}/reviews')->name('reviews.')->group(function () {
        // Создание рецензий
        Route::post('/', [ReviewController::class, 'store'])->name('store')->middleware('auth');
        Route::post('/guest', [ReviewController::class, 'guestStore'])->name('guest-store');
        
        // Ответы на рецензии
        Route::post('/{review}/replies', [ReviewController::class, 'storeReply'])->name('replies.store');
        
        // Редактирование и удаление рецензий
        Route::get('/{review}/edit', [ReviewController::class, 'edit'])->name('edit')->middleware('auth');
        Route::put('/{review}', [ReviewController::class, 'update'])->name('update')->middleware('auth');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy')->middleware('auth');
    });
});

// Categories for books
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [App\Http\Controllers\CategoryController::class, 'index'])->name('index');
    Route::get('/{category}', [App\Http\Controllers\CategoryController::class, 'show'])->name('show');
});

// Search
Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');
