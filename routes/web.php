<?php

use App\Http\Controllers\AplikasiController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageProcessingController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PromptController;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', [HomeController::class, 'index'])->name('home');

// Artikel
Route::prefix('artikel')->name('articles.')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('index');
    Route::get('/{article:slug}', [ArticleController::class, 'show'])->name('show');
});

// Produk
Route::get('/produk', fn () => view('products.index'))->name('products.index');

Route::get('/jasa-pembuatan-aplikasi', [AplikasiController::class, 'index'])->name('aplikasi.index');

Route::prefix('produk/prompt')->name('products.prompt.')->group(function () {
    Route::get('/', [PromptController::class, 'index'])->name('index');
    Route::get('/{prompt:slug}', [PromptController::class, 'show'])->name('show');
});

// Checkout & pembayaran
Route::post('/checkout/{type}/{slug}', [CheckoutController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('checkout.store');

Route::post('/midtrans/notification', [MidtransController::class, 'notification'])
    ->name('midtrans.notification');

Route::get('/api/order/{order:order_number}/status', [OrderStatusController::class, 'show'])
    ->middleware('throttle:30,1')
    ->name('order.status');

Route::get('/unduh/{order:download_token}', [PaymentController::class, 'download'])
    ->name('unduh.show');

// Akun klien
Route::get('/akun/login', [AuthController::class, 'showLogin'])
    ->middleware('guest')
    ->name('akun.login');

Route::post('/akun/login', [AuthController::class, 'login'])
    ->middleware(['guest', 'throttle:5,1'])
    ->name('akun.login.attempt');

Route::post('/akun/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('akun.logout');

Route::get('/akun/produk-saya', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('akun.produk-saya');

Route::post('/akun/pesanan/{order:order_number}/batalkan', [DashboardController::class, 'cancel'])
    ->middleware('auth')
    ->name('akun.pesanan.batalkan');

// Image processing (tidak terkait konten/produk)
Route::post('/remove-background', [ImageProcessingController::class, 'removeBackground']);
Route::get('/api/image/test', [ImageProcessingController::class, 'testEndpoint']);
