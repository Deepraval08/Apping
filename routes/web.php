<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// Routes for category manage
Route::controller(CategoryController::class)->prefix('/categories')->name('category.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/store', 'store')->name('store');
    Route::post('/edit', 'edit')->name('edit');
    Route::delete('/delete', 'delete')->name('delete');
});

// Routes for product manage
Route::controller(ProductController::class)->prefix('/products')->name('product.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/store', 'store')->name('store');
    Route::post('/edit', 'edit')->name('edit');
    Route::delete('/delete', 'delete')->name('delete');
});
