<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Partner;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront (customer facing)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [CatalogController::class, 'all'])->name('catalog.all');
Route::get('/category/{category:slug}', [CatalogController::class, 'category'])->name('catalog.category');
Route::get('/product/{product:slug}', [CatalogController::class, 'product'])->name('catalog.product');

/*
|--------------------------------------------------------------------------
| Customer authentication (web guard)
|--------------------------------------------------------------------------
*/
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register']);
});
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

Route::middleware('auth:web')->group(function () {
    Route::view('/mypage', 'storefront.mypage')->name('mypage');
});

/*
|--------------------------------------------------------------------------
| Admin area (admin guard)  ->  /admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [Admin\AuthController::class, 'login']);
    Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', Admin\ProductController::class)->except('show');
        Route::get('partners', [Admin\PartnerController::class, 'index'])->name('partners.index');
        Route::patch('partners/{partner}/status', [Admin\PartnerController::class, 'updateStatus'])->name('partners.status');
    });
});

/*
|--------------------------------------------------------------------------
| Partner area (partner guard)  ->  /partner
|--------------------------------------------------------------------------
*/
Route::prefix('partner')->name('partner.')->group(function () {
    Route::get('login', [Partner\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [Partner\AuthController::class, 'login']);
    Route::get('register', [Partner\AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [Partner\AuthController::class, 'register']);
    Route::post('logout', [Partner\AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:partner')->group(function () {
        Route::get('/', [Partner\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', Partner\ProductController::class)->except('show');
    });
});
