<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('shop.index');
});

Route::get('/admin', function () {
    return view('admin.index');
});
