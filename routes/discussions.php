<?php

use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\DiscussionLikeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Discussion Routes
|--------------------------------------------------------------------------
*/

Route::prefix('discussions')->name('discussions.')->group(function () {
    // Public routes
    Route::get('/', [DiscussionController::class, 'index'])->name('index');
    
    // Auth required routes - specific routes first to avoid conflicts
    Route::middleware(['auth'])->group(function () {
        // Discussion management - specific routes first
        Route::get('/create', [DiscussionController::class, 'create'])->name('create');
        Route::post('/', [DiscussionController::class, 'store'])->name('store');
        
        // Discussion status management
        Route::post('/{discussion:slug}/close', [DiscussionController::class, 'close'])->name('close');
        Route::post('/{discussion:slug}/reopen', [DiscussionController::class, 'reopen'])->name('reopen');
        
        // Discussion replies - most specific routes first
        Route::get('/{discussion:slug}/replies/{reply}', [DiscussionController::class, 'showReply'])->name('replies.show');
        Route::post('/{discussion:slug}/replies', [DiscussionController::class, 'storeReply'])->name('replies.store');
        Route::put('/{discussion:slug}/replies/{reply}', [DiscussionController::class, 'updateReply'])->name('replies.update');
        Route::delete('/{discussion:slug}/replies/{reply}', [DiscussionController::class, 'destroyReply'])->name('replies.destroy');
        
        // Discussion likes
        Route::post('/{discussion:slug}/like', [DiscussionLikeController::class, 'toggleDiscussion'])->name('like');
        Route::post('/{discussion:slug}/replies/{reply}/like', [DiscussionLikeController::class, 'toggleReply'])->name('replies.like');
        
        // Discussion CRUD - put after most specific routes to avoid conflicts
        Route::get('/{discussion:slug}/edit', [DiscussionController::class, 'edit'])->name('edit');
        Route::put('/{discussion:slug}', [DiscussionController::class, 'update'])->name('update');
        Route::delete('/{discussion:slug}', [DiscussionController::class, 'destroy'])->name('destroy');
    });
    
    // Public discussion show route - put at the end to avoid conflicts with /create
    Route::get('/{discussion:slug}', [DiscussionController::class, 'show'])->name('show');
});