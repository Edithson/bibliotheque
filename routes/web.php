<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookController::class, 'shopIndex'])->name('shop.index');

Route::prefix('admin')->group(function () {
    Route::get('/', [BookController::class, 'adminIndex'])->name('admin.index');
    Route::post('/books', [BookController::class, 'store'])->name('admin.books.store');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('admin.books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('admin.books.destroy');
});
