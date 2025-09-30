<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authors routes
Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
Route::get('/authors/{author:slug}', [AuthorController::class, 'show'])->name('authors.show');

// Users routes
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

// Profile routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    
    // Public profile routes (with username)
    Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.user.show');
    Route::get('/profile/{username}/library', [ProfileController::class, 'library'])->name('profile.library');
    Route::get('/profile/{username}/reviews', [ProfileController::class, 'reviews'])->name('profile.reviews');
    Route::get('/profile/{username}/discussions', [ProfileController::class, 'discussions'])->name('profile.discussions');
    Route::get('/profile/{username}/quotes', [ProfileController::class, 'quotes'])->name('profile.quotes');
    Route::get('/profile/{username}/collections', [ProfileController::class, 'collections'])->name('profile.collections');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/forum.php';
require __DIR__.'/books.php';
