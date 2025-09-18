<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\HomeController;
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

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/forum.php';
require __DIR__.'/books.php';
