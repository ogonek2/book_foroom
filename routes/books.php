<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\FactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Book Routes
|--------------------------------------------------------------------------
*/

Route::prefix('books')->name('books.')->group(function () {
    // Основные маршруты для книг
    Route::get('/', [BookController::class, 'index'])->name('index');
    
    
    Route::get('/{book:slug}', [BookController::class, 'show'])->name('show');
    
    // Маршруты для рецензий
    Route::prefix('{book:slug}/reviews')->name('reviews.')->group(function () {
        // Список рецензий / редирект на страницу книги
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        // Форма создания рецензии
        Route::get('/create', [ReviewController::class, 'create'])->name('create')->middleware('auth');
        // Создание рецензий
        Route::post('/', [ReviewController::class, 'store'])->name('store')->middleware(['auth', \App\Http\Middleware\SanitizeHtmlContent::class]);
        Route::post('/guest', [ReviewController::class, 'guestStore'])->name('guest-store')->middleware(\App\Http\Middleware\SanitizeHtmlContent::class);
        
        // Просмотр отдельной рецензии (поддерживает как ID, так и slug книги)
        Route::get('/{reviewIdentifier}', [ReviewController::class, 'show'])->name('show');
        
        // Ответы на рецензии
        Route::post('/{review}/replies', [ReviewController::class, 'storeReply'])->name('replies.store')->middleware(\App\Http\Middleware\SanitizeHtmlContent::class);
        
        // Редактирование и удаление ответов
        Route::post('/{review}/update', [ReviewController::class, 'updateReply'])->name('reply.update')->middleware(['auth', \App\Http\Middleware\SanitizeHtmlContent::class]);
        Route::delete('/{review}/delete', [ReviewController::class, 'deleteReply'])->name('reply.delete')->middleware('auth');
        
        // Лайки рецензий
        Route::post('/{review}/like', [ReviewController::class, 'toggleLike'])->name('like')->middleware('auth');
        
        // Избранные рецензии
        Route::post('/{review}/favorite', [ReviewController::class, 'toggleFavorite'])->name('favorite')->middleware('auth');
        
        // Редактирование и удаление рецензий
        Route::get('/{review}/edit', [ReviewController::class, 'edit'])->name('edit')->middleware('auth');
        Route::get('/{review}/edit-draft', [ReviewController::class, 'editDraft'])->name('edit-draft')->middleware('auth');
        Route::get('/{review}/data', [ReviewController::class, 'getReviewData'])->name('data')->middleware('auth');
        Route::put('/{review}', [ReviewController::class, 'update'])->name('update')->middleware(['auth', \App\Http\Middleware\SanitizeHtmlContent::class]);
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy')->middleware('auth');
    });
    
    // Маршруты для цитат
    Route::prefix('{book:slug}/quotes')->name('quotes.')->group(function () {
        // Список всіх цитат книги
        Route::get('/', [QuoteController::class, 'index'])->name('index');
        Route::post('/', [QuoteController::class, 'store'])->name('store')->middleware('auth');
        Route::post('/{quote}/like', [QuoteController::class, 'toggleLike'])->name('like')->middleware('auth');
        Route::post('/{quote}/favorite', [QuoteController::class, 'toggleFavorite'])->name('favorite')->middleware('auth');
        Route::get('/{quote}/data', [QuoteController::class, 'getQuoteData'])->name('data')->middleware('auth');
        Route::get('/{quote}/edit-draft', [QuoteController::class, 'editDraft'])->name('edit-draft')->middleware('auth');
        Route::put('/{quote}', [QuoteController::class, 'update'])->name('update')->middleware('auth');
        Route::delete('/{quote}', [QuoteController::class, 'destroy'])->name('destroy')->middleware('auth');
    });
    
    // Маршруты для фактов
    Route::prefix('{book:slug}/facts')->name('facts.')->group(function () {
        // Список всіх фактів книги
        Route::get('/', [FactController::class, 'index'])->name('index');
        Route::post('/', [FactController::class, 'store'])->name('store')->middleware('auth');
        Route::post('/{fact}/like', [FactController::class, 'toggleLike'])->name('like')->middleware('auth');
        Route::put('/{fact}', [FactController::class, 'update'])->name('update')->middleware('auth');
        Route::delete('/{fact}', [FactController::class, 'destroy'])->name('destroy')->middleware('auth');
    });
});

// Categories for books
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [App\Http\Controllers\CategoryController::class, 'index'])->name('index');
    Route::get('/{category}', [App\Http\Controllers\CategoryController::class, 'show'])->name('show');
});

// Search
Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');
