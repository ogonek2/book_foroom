<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Book rating routes (должны быть ПЕРЕД другими маршрутами)
Route::middleware(['auth'])->group(function () {
    Route::post('/books/{book:slug}/rating', [BookController::class, 'updateRating'])->name('books.rating.update');
    Route::get('/books/{book:slug}/rating', [BookController::class, 'getUserRating'])->name('books.rating.get');
});

// Authors routes
Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
Route::get('/authors/{author:slug}', [AuthorController::class, 'show'])->name('authors.show');

// Users routes
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// Public profile routes (new design)
Route::get('/users/{username}', [UserController::class, 'publicProfile'])->name('users.public.profile');
Route::get('/users/{username}/library', [UserController::class, 'publicLibrary'])->name('users.public.library');
Route::get('/users/{username}/reviews', [UserController::class, 'publicReviews'])->name('users.public.reviews');
Route::get('/users/{username}/discussions', [UserController::class, 'publicDiscussions'])->name('users.public.discussions');
Route::get('/users/{username}/quotes', [UserController::class, 'publicQuotes'])->name('users.public.quotes');
Route::get('/users/{username}/collections', [UserController::class, 'publicCollections'])->name('users.public.collections');

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
    Route::get('/profile/{username}/collections/{libraryId}/books', [ProfileController::class, 'getLibraryBooks'])->name('profile.collections.books');
});

// Library routes
Route::middleware(['auth'])->group(function () {
    Route::resource('libraries', LibraryController::class);
    Route::post('/libraries/{library}/add-book', [LibraryController::class, 'addBook'])->name('libraries.addBook');
    Route::delete('/libraries/{library}/books/{book}', [LibraryController::class, 'removeBook'])->name('libraries.removeBook');
});

// Public library routes
Route::get('/users/{username}/libraries', [LibraryController::class, 'publicLibraries'])->name('libraries.public');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/forum.php';
require __DIR__.'/books.php';
require __DIR__.'/discussions.php';
