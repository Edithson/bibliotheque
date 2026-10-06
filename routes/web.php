<?php

use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $books = Book::with('category')->where('is_published', true)->get();
    $categories = Category::all();

    return view('shop.index', compact('books', 'categories'));
});

Route::get('/admin', function () {
    $books = Book::with('category')->get();
    $categories = Category::all();

    return view('admin.index', compact('books', 'categories'));
});
