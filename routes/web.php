<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PublicShopController;
use Illuminate\Support\Facades\Route;


Route::get('/', [PublicShopController::class, 'index'])->name('home');
Route::get('/shops/{shop:slug}', [PublicShopController::class, 'show'])->name('public.shop');


Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/owners', [AdminController::class, 'owners'])->name('owners');
    Route::post('/owners', [AdminController::class, 'storeOwner'])->name('owners.store');
    Route::put('/owners/{owner}', [AdminController::class, 'updateOwner'])->name('owners.update');
    Route::delete('/owners/{owner}', [AdminController::class, 'destroyOwner'])->name('owners.destroy');
    Route::get('/shops', [AdminController::class, 'shops'])->name('shops');
    Route::post('/shops', [AdminController::class, 'storeShop'])->name('shops.store');
    Route::put('/shops/{shop}', [AdminController::class, 'updateShop'])->name('shops.update');
    Route::delete('/shops/{shop}', [AdminController::class, 'destroyShop'])->name('shops.destroy');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::post('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'destroyProduct'])->name('products.destroy');
});


Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/', [OwnerController::class, 'dashboard'])->name('dashboard');
    Route::get('/catalog', [OwnerController::class, 'catalog'])->name('catalog');
    Route::post('/catalog', [OwnerController::class, 'createProduct'])->name('products.store');
    Route::post('/catalog/{product}/list', [OwnerController::class, 'attach'])->name('catalog.attach');
    Route::get('/listings', [OwnerController::class, 'listings'])->name('listings');
    Route::post('/listings/{listing}', [OwnerController::class, 'updateListing'])->name('listings.update');
    Route::delete('/listings/{listing}', [OwnerController::class, 'detach'])->name('listings.destroy');
});
