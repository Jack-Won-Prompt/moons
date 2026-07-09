<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
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
| 콘텐츠 (공지 · FAQ · 기획전)
|--------------------------------------------------------------------------
*/
Route::get('/notices', [ContentController::class, 'notices'])->name('content.notices');
Route::get('/notices/{notice}', [ContentController::class, 'notice'])->name('content.notice');
Route::get('/faq', [ContentController::class, 'faqs'])->name('content.faqs');
Route::get('/promotion/{promotion}', [ContentController::class, 'promotion'])->name('content.promotion');
Route::get('/reviews', [Customer\ReviewController::class, 'gallery'])->name('reviews.gallery');

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

    // SNS 로그인 (시뮬레이션 · Socialite 대체 지점)
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::post('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

    // 비밀번호 찾기
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

// 이메일 인증
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')->name('verification.verify');
Route::middleware('auth:web')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])->name('verification.send');
});

Route::middleware('auth:web')->group(function () {
    Route::view('/mypage', 'storefront.mypage')->name('mypage');

    // 판매하기 (위탁/매입 접수) — 고객이 본사/지점에 직접 판매
    Route::get('/sell', [Customer\SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [Customer\SellController::class, 'store'])->name('sell.store');
    Route::get('/sell/history', [Customer\SellController::class, 'history'])->name('sell.history');
    Route::get('/sell/{sellRequest}', [Customer\SellController::class, 'show'])->name('sell.show');
    Route::post('/sell/{sellRequest}/approve', [Customer\SellController::class, 'approve'])->name('sell.approve');

    // 실시간 채팅 (고객)
    Route::get('/chat', [Customer\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/widget', [Customer\ChatController::class, 'widget'])->name('chat.widget');
    Route::post('/chat/start', [Customer\ChatController::class, 'start'])->name('chat.start');
    Route::get('/chat/{conversation}', [Customer\ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/send', [Customer\ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/{conversation}/poll', [Customer\ChatController::class, 'poll'])->name('chat.poll');

    // 장바구니 · 주문 · 결제 (고객)
    Route::get('/cart', [Customer\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [Customer\CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{cartItem}', [Customer\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [Customer\CartController::class, 'remove'])->name('cart.remove');
    Route::get('/checkout', [Customer\OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [Customer\OrderController::class, 'place'])->name('orders.place');
    Route::get('/orders', [Customer\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [Customer\OrderController::class, 'show'])->name('orders.show');

    // 알림 (고객)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

    // 멤버십 (등급 · 포인트 · 쿠폰)
    Route::get('/membership', [Customer\MembershipController::class, 'index'])->name('membership.index');
    Route::post('/membership/coupons/{coupon}/claim', [Customer\MembershipController::class, 'claim'])->name('membership.claim');

    // 위시리스트 (관심상품)
    Route::get('/wishlist', [Customer\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [Customer\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{wishlist}', [Customer\WishlistController::class, 'remove'])->name('wishlist.remove');

    // 리뷰 작성
    Route::post('/product/{product}/reviews', [Customer\ReviewController::class, 'store'])->name('reviews.store');
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

        // 상담 모니터링 (본사)
        Route::get('chat', [Admin\ChatController::class, 'index'])->name('chat.index');
        Route::get('chat/{conversation}', [Admin\ChatController::class, 'show'])->name('chat.show');
        Route::post('chat/{conversation}/send', [Admin\ChatController::class, 'send'])->name('chat.send');
        Route::get('chat/{conversation}/poll', [Admin\ChatController::class, 'poll'])->name('chat.poll');

        // 주문 관리 (본사)
        Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');

        // 알림 (본사)
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
        Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
        Route::post('notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

        // 멤버십 정책 · 쿠폰
        Route::get('membership', [Admin\MembershipController::class, 'index'])->name('membership.index');
        Route::post('membership/coupons', [Admin\MembershipController::class, 'storeCoupon'])->name('membership.coupons.store');
        Route::post('membership/coupons/{coupon}/toggle', [Admin\MembershipController::class, 'toggleCoupon'])->name('membership.coupons.toggle');

        // 멀티지점 재고 현황
        Route::get('inventory', [Admin\InventoryController::class, 'index'])->name('inventory.index');

        // 콘텐츠 관리
        Route::get('content', [Admin\ContentController::class, 'index'])->name('content.index');
        Route::post('content/banners', [Admin\ContentController::class, 'storeBanner'])->name('content.banners.store');
        Route::post('content/promotions', [Admin\ContentController::class, 'storePromotion'])->name('content.promotions.store');
        Route::post('content/notices', [Admin\ContentController::class, 'storeNotice'])->name('content.notices.store');
        Route::post('content/faqs', [Admin\ContentController::class, 'storeFaq'])->name('content.faqs.store');
        Route::delete('content/{type}/{id}', [Admin\ContentController::class, 'destroy'])->name('content.destroy');

        // 통계
        Route::get('stats', [Admin\StatController::class, 'index'])->name('stats.index');

        // 정산 관리
        Route::get('settlements', [Admin\SettlementController::class, 'index'])->name('settlements.index');
        Route::post('settlements/{partner}/pay', [Admin\SettlementController::class, 'payStore'])->name('settlements.pay');
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

        // 채팅 (지점)
        Route::get('chat', [Partner\ChatController::class, 'index'])->name('chat.index');
        Route::get('chat/{conversation}', [Partner\ChatController::class, 'show'])->name('chat.show');
        Route::post('chat/{conversation}/send', [Partner\ChatController::class, 'send'])->name('chat.send');
        Route::get('chat/{conversation}/poll', [Partner\ChatController::class, 'poll'])->name('chat.poll');

        // 알림 (지점)
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
        Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
        Route::post('notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

        // 멀티지점 재고 공유 · 이동
        Route::get('inventory', [Partner\InventoryController::class, 'index'])->name('inventory.index');
        Route::get('inventory/stores', [Partner\InventoryController::class, 'stores'])->name('inventory.stores');
        Route::get('inventory/transfers', [Partner\InventoryController::class, 'transfers'])->name('inventory.transfers');
        Route::post('inventory/request', [Partner\InventoryController::class, 'requestTransfer'])->name('inventory.request');
        Route::post('inventory/transfers/{stockTransfer}/act', [Partner\InventoryController::class, 'act'])->name('inventory.act');

        // 판매 정산
        Route::get('settlements', [Partner\SettlementController::class, 'index'])->name('settlements.index');
    });
});
