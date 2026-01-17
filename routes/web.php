<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('dashboard', [ProfileController::class, 'show'])->middleware(['auth', 'verified'])->name('dashboard');


// Book rating routes (должны быть ПЕРЕД другими маршрутами)
Route::middleware(['auth'])->group(function () {
    Route::post('/books/{book:slug}/rating', [BookController::class, 'updateRating'])->name('books.rating.update');
    Route::get('/books/{book:slug}/rating', [BookController::class, 'getUserRating'])->name('books.rating.get');
});

// Authors routes
Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
Route::get('/authors/{author:slug}', [AuthorController::class, 'show'])->name('authors.show');
Route::get('/authors/{author:slug}/reviews', [AuthorController::class, 'reviews'])->name('authors.reviews.index');
Route::get('/authors/{author:slug}/quotes', [AuthorController::class, 'quotes'])->name('authors.quotes.index');
Route::get('/authors/{author:slug}/facts', [AuthorController::class, 'facts'])->name('authors.facts.index');

// Users routes
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// Public profile routes (new design)
Route::get('/users/{username}', [UserController::class, 'publicProfile'])->name('users.public.profile');
Route::get('/users/{username}/library', [UserController::class, 'publicLibrary'])->name('users.public.library');
Route::get('/users/{username}/reviews', [UserController::class, 'publicReviews'])->name('users.public.reviews');
Route::get('/users/{username}/discussions', [UserController::class, 'publicDiscussions'])->name('users.public.discussions');
Route::get('/users/{username}/quotes', [UserController::class, 'publicQuotes'])->name('users.public.quotes');
Route::get('/users/{username}/collections', [UserController::class, 'publicCollections'])->name('users.public.collections');
Route::get('/users/{username}/awards', [UserController::class, 'publicAwards'])->name('users.public.awards');

// Profile routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    
    // Profile management routes
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::put('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications.update');
    Route::put('/profile/privacy', [ProfileController::class, 'updatePrivacy'])->name('profile.privacy.update');
    Route::get('/profile/export', [ProfileController::class, 'export'])->name('profile.export');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Public profile routes (with username)
    Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.user.show');
    
    // Drafts routes
    Route::prefix('drafts')->name('drafts.')->group(function () {
        Route::post('/{type}/{id}/publish', [DraftController::class, 'publish'])->name('publish');
        Route::delete('/{type}/{id}', [DraftController::class, 'destroy'])->name('destroy');
    });
});

// Notification routes
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [\App\Http\Controllers\NotificationController::class, 'page'])->name('page');
    Route::get('/api', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::get('/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::post('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    Route::delete('/read/all', [\App\Http\Controllers\NotificationController::class, 'deleteAllRead'])->name('delete-all-read');
});

// Public library routes
Route::get('/users/{username}/libraries', [LibraryController::class, 'publicLibraries'])->name('libraries.public');

// Hashtags routes
Route::get('/hashtags/{slug}', [ReviewController::class, 'searchByHashtag'])->name('hashtags.show');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/books.php';
require __DIR__.'/discussions.php';
require __DIR__.'/libraries.php';
