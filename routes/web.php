<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::resource('products', ProductController::class);

Route::post('/products/update-order', [ProductController::class, 'updateOrder'])
    ->name('products.update-order');

Route::post('/products/bulk-action', [ProductController::class, 'bulkAction'])
    ->name('products.bulk-action');

Route::post('/products/reset-order', [ProductController::class, 'resetOrder'])
    ->name('products.reset-order');
