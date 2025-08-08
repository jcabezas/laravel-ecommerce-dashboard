<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopifyController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // Si el usuario ya está autenticado, lo redirigimos al dashboard.
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    // Si no, mostramos la página de bienvenida.
    return Inertia::render('Welcome');
})->name('welcome');


Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/connect-store', [StoreController::class, 'create'])->name('store.create');
    Route::post('/connect-store', [StoreController::class, 'store'])->name('store.store');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('/shopify/redirect', [ShopifyController::class, 'redirect'])->name('shopify.redirect');
    Route::get('/shopify/callback', [ShopifyController::class, 'callback'])->name('shopify.callback');
});

require __DIR__ . '/auth.php';
