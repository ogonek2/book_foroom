<?php

use App\Http\Controllers\LibraryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    // Public routes
    Route::get('/libraries', [LibraryController::class, 'index'])->name('libraries.index');
    
    // Protected routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/libraries/create', [LibraryController::class, 'create'])->name('libraries.create');
        Route::post('/libraries', [LibraryController::class, 'store'])->name('libraries.store');
        Route::get('/libraries/{library}/edit', [LibraryController::class, 'edit'])->name('libraries.edit');
        Route::put('/libraries/{library}', [LibraryController::class, 'update'])->name('libraries.update');
        Route::delete('/libraries/{library}', [LibraryController::class, 'destroy'])->name('libraries.destroy');
        
        // Book management in libraries
        Route::post('/libraries/{library:id}/add-book', [LibraryController::class, 'addBook'])->name('libraries.add-book');
        Route::delete('/libraries/{library:id}/books/{book}', [LibraryController::class, 'removeBook'])->name('libraries.remove-book');
        
        // User interactions with libraries
        Route::post('/libraries/{library}/save', [LibraryController::class, 'toggleSave'])->name('libraries.toggle-save');
        Route::post('/libraries/{library}/unsave', [LibraryController::class, 'unsave'])->name('libraries.unsave');
        Route::post('/libraries/{library}/like', [LibraryController::class, 'toggleLike'])->name('libraries.toggle-like');
    });
    
    // Public routes that need to be after specific routes
    // Новый маршрут с username и slug
    Route::get('/users/{username}/libraries/{library:slug}', [LibraryController::class, 'showBySlug'])->name('libraries.show.slug');
    // Старый маршрут для обратной совместимости
    Route::get('/libraries/{library}', [LibraryController::class, 'show'])->name('libraries.show');
});
