<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
});

// CSV Import & Export routes (must be before resource route to avoid collision)
Route::get('/products/export/csv', [ProductController::class, 'exportCsv'])
    ->name('products.export-csv');

Route::post('/products/import/csv', [ProductController::class, 'importCsv'])
    ->name('products.import-csv');

Route::get('/products/catalog/pdf', [ProductController::class, 'downloadPdfCatalog'])
    ->name('products.catalog-pdf');

// Drag & Drop Order Update
Route::post('/products/update-order', [ProductController::class, 'updateOrder'])
    ->name('products.update-order');

// Bulk Actions
Route::post('/products/bulk-action', [ProductController::class, 'bulkAction'])
    ->name('products.bulk-action');

// Reset Order
Route::post('/products/reset-order', [ProductController::class, 'resetOrder'])
    ->name('products.reset-order');

// Inline Quick Edit
Route::post('/products/{product}/quick-update', [ProductController::class, 'quickUpdate'])
    ->name('products.quick-update');

// Recycle Bin & Soft Deletes
Route::post('/products/{id}/restore', [ProductController::class, 'restore'])
    ->name('products.restore');

Route::delete('/products/{id}/force-delete', [ProductController::class, 'forceDelete'])
    ->name('products.force-delete');

Route::post('/products/trash/empty', [ProductController::class, 'emptyTrash'])
    ->name('products.empty-trash');

// Main Resource Route
Route::resource('products', ProductController::class);
