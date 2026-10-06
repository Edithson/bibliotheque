<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookController::class, 'shopIndex'])->name('shop.index');
Route::get('/books/{book:slug}/download', [BookController::class, 'download'])->name('books.download');

Route::prefix('admin')->group(function () {
    Route::get('/', [BookController::class, 'adminIndex'])->name('admin.index');
    Route::get('/books/create', [BookController::class, 'create'])->name('admin.books.create');
    Route::post('/books', [BookController::class, 'store'])->name('admin.books.store');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('admin.books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('admin.books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('admin.books.destroy');
});
