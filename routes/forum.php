<?php

use App\Http\Controllers\Forum\CategoryController;
use App\Http\Controllers\Forum\LikeController;
use App\Http\Controllers\Forum\PostController;
use App\Http\Controllers\Forum\TopicController;
use Illuminate\Support\Facades\Route;

Route::prefix('forum')->name('forum.')->group(function () {
    // Categories
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

    // Topics
    Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');
    Route::get('/topics/{topic}', [TopicController::class, 'show'])->name('topics.show');
    
    // Auth required routes
    Route::middleware('auth')->group(function () {
        Route::get('/categories/{category}/topics/create', [TopicController::class, 'create'])->name('topics.create');
        Route::post('/categories/{category}/topics', [TopicController::class, 'store'])->name('topics.store');
        Route::get('/topics/{topic}/edit', [TopicController::class, 'edit'])->name('topics.edit');
        Route::put('/topics/{topic}', [TopicController::class, 'update'])->name('topics.update');
        Route::delete('/topics/{topic}', [TopicController::class, 'destroy'])->name('topics.destroy');

        // Posts
        Route::post('/topics/{topic}/posts', [PostController::class, 'store'])->name('posts.store');
        Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
        Route::post('/posts/{post}/solution', [PostController::class, 'markAsSolution'])->name('posts.solution');

        // Likes
        Route::post('/likes/toggle', [LikeController::class, 'toggle'])->name('likes.toggle');
    });
});
