<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public shop & download routes
Route::get('/', [BookController::class, 'shopIndex'])->name('shop.index');
Route::get('/books/{book:slug}/download', [BookController::class, 'download'])->name('books.download');

// Guest Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Google Socialite OAuth
    Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Authenticated Logout Route
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Admin Panel Routes (Author level 2 and above)
Route::middleware(['auth', 'role:author'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [BookController::class, 'adminIndex'])->name('index');
    Route::get('/books', [BookController::class, 'adminIndex'])->name('books.index');
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    // Gérant Level (3 and above): Book validation toggle & Categories management
    Route::middleware('role:gerant')->group(function () {
        Route::patch('/books/{book}/toggle-publish', [BookController::class, 'togglePublish'])->name('books.toggle-publish');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Admin Level (4): User Account & Role Management
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
