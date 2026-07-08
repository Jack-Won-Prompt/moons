<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Partner;
use App\Http\Controllers\VerifyController;
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
| 감정서 / DPP 공개 검증 (QR)
|--------------------------------------------------------------------------
*/
Route::get('/verify', [VerifyController::class, 'index'])->name('verify.index');
Route::get('/verify/{code}', [VerifyController::class, 'show'])->name('verify.show');
Route::get('/verify/{code}/qr', [VerifyController::class, 'qr'])->name('verify.qr');

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

    // 판매하기 (위탁/매입 접수) — 고객이 본사/지점에 직접 판매
    Route::get('/sell', [Customer\SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [Customer\SellController::class, 'store'])->name('sell.store');
    Route::get('/sell/history', [Customer\SellController::class, 'history'])->name('sell.history');
    Route::get('/sell/{sellRequest}', [Customer\SellController::class, 'show'])->name('sell.show');
    Route::post('/sell/{sellRequest}/approve', [Customer\SellController::class, 'approve'])->name('sell.approve');
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

        // 감정 관리 / 접수 현황 / 경매
        Route::get('sell-requests', [Admin\AppraisalController::class, 'index'])->name('sell.index');
        Route::get('sell-requests/{sellRequest}', [Admin\AppraisalController::class, 'show'])->name('sell.show');
        Route::post('sell-requests/{sellRequest}/appraise', [Admin\AppraisalController::class, 'appraise'])->name('sell.appraise');
        Route::post('sell-requests/{sellRequest}/certificate', [Admin\AppraisalController::class, 'issueCertificate'])->name('sell.certificate');

        // 블록체인 감정서
        Route::get('certificates', [Admin\CertificateController::class, 'index'])->name('certificates.index');
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

        // 판매 접수 / 정품 감정 / 경매 입찰 / 입고
        Route::get('intakes', [Partner\IntakeController::class, 'index'])->name('intakes.index');
        Route::get('intakes/{sellRequest}', [Partner\IntakeController::class, 'show'])->name('intakes.show');
        Route::post('intakes/{sellRequest}/appraise', [Partner\IntakeController::class, 'appraise'])->name('intakes.appraise');
        Route::post('intakes/{sellRequest}/bid', [Partner\IntakeController::class, 'bid'])->name('intakes.bid');
        Route::post('intakes/{sellRequest}/inbound', [Partner\IntakeController::class, 'inbound'])->name('intakes.inbound');
    });
});
