<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::resource('products', ProductController::class);

Route::post('/products/update-order', [ProductController::class, 'updateOrder'])
    ->name('products.update-order');