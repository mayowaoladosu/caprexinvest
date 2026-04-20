<?php

use App\Http\Controllers\Auth\LoginController as UserLoginController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\DepositController;
use App\Http\Controllers\User\NotificationPermissionController;
use App\Http\Controllers\User\PayoutController;
use App\Http\Controllers\ManualRecaptchaController;
use App\Http\Controllers\khaltiPaymentController;
use App\Http\Controllers\User\PurchasePlanController;
use App\Http\Controllers\User\RatingController;
use App\Http\Controllers\User\WishlistController;
use App\Models\BasicControl;
use App\Models\Page;
use App\Models\PageDetail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InAppNotificationController;
use App\Http\Controllers\User\SupportTicketController;
use App\Http\Controllers\User\TradingController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\User\VerificationController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\User\KycVerificationController;
use App\Http\Controllers\TwoFaSecurityController;


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

$basicControl = basicControl();
Route::get('language', [FrontendController::class,'language'])->name('language');

Route::get('maintenance-mode', function () {
    if (!basicControl()->is_maintenance_mode) {
        return redirect(route('page'));
    }
    $data['maintenanceMode'] = \App\Models\MaintenanceMode::first();
    return view(template() . 'maintenance', $data);
})->name('maintenance');

Route::get('select/home/style/{val}', function ($val) {
//    if (!config('demo.IS_DEMO')) abort(404);
    if (!in_array($val, ['home_one', 'home_two'])) {
        abort(404);
    }
    $home_sections = config('home.'.$val);
    $page = Page::where('slug','/')->first();
    if(!$page){
        $page = Page::first();
    }
    $pageDetails = PageDetail::where('page_id',$page->id)->get();
    foreach ($pageDetails as $pageDetail) {
        $pageDetail->content = $home_sections['content'];
        $pageDetail->sections = $home_sections['sections'];
        $pageDetail->save();
    }

    $basicControl = BasicControl::firstOrFail();
    $basicControl->home_style = $val;
    $basicControl->update();
    Artisan::call('cache:clear');
    return redirect()->route('page');
});

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPassword'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset')->middleware('guest');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset.update');

Route::get('instruction/page', function () {
    return view('instruction-page');
})->name('instructionPage');

Route::group(['middleware' => ['maintenanceMode']], function () use ($basicControl) {
    Route::group(['middleware' => ['guest']], function () {
        Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [UserLoginController::class, 'login'])->name('login.submit');
    });

    Route::group(['middleware' => ['auth'], 'prefix' => 'user', 'as' => 'user.'], function () {

        Route::get('check', [VerificationController::class, 'check'])->name('check');
        Route::get('resend_code', [VerificationController::class, 'resendCode'])->name('resend.code');
        Route::post('mail-verify', [VerificationController::class, 'mailVerify'])->name('mail.verify');
        Route::post('sms-verify', [VerificationController::class, 'smsVerify'])->name('sms.verify');
        Route::post('twoFA-Verify', [VerificationController::class, 'twoFAverify'])->name('twoFA-Verify');

        Route::middleware('userCheck')->group(function () {

            /* Get Chart Data */
            Route::get('/invest/history',[HomeController::class,'investHistory'])->name('invest.history');
            Route::get('/deposit-payout/history',[HomeController::class,'depositPayout'])->name('depositPayout.history');
            Route::get('transaction/history',[HomeController::class,'transactionHistory'])->name('transaction.history');
            Route::get('gold/chart/data',[HomeController::class,'goldChartData'])->name('gold.chart.data');

            Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');
            Route::middleware(['kyc'])->group(function () {
                Route::get('trading', [TradingController::class, 'index'])->name('trading.index');
                Route::get('trading/quote', [TradingController::class, 'quote'])->name('trading.quote')->middleware('throttle:240,1');
                Route::get('trading/snapshot', [TradingController::class, 'snapshot'])->name('trading.snapshot')->middleware('throttle:120,1');
                Route::get('trading/orderbook', [TradingController::class, 'orderbook'])->name('trading.orderbook')->middleware('throttle:120,1');
                Route::post('trading/order', [TradingController::class, 'placeOrder'])->name('trading.order')->middleware('throttle:60,1');
            });
            Route::post('save-token', [HomeController::class, 'saveToken'])->name('save.token');
            Route::get('transactions', [HomeController::class, 'transactions'])->name('transaction.list');

            Route::middleware('kyc')->group(function () {
                Route::get('add-fund', [HomeController::class, 'addFund'])->name('add.fund');
                Route::get('funds', [HomeController::class, 'fund'])->name('fund.index');

                /* PAYMENT REQUEST BY USER */
                Route::get('payout-list', [PayoutController::class, 'index'])->name('payout.index');

                Route::get('payout', [PayoutController::class, 'payout'])->name('payout');
                Route::get('payout-supported-currency', [PayoutController::class, 'payoutSupportedCurrency'])->name('payout.supported.currency');
                Route::get('payout-check-amount', [PayoutController::class, 'checkAmount'])->name('payout.checkAmount');
                Route::post('request-payout', [PayoutController::class, 'payoutRequest'])->name('payout.request');

                Route::match(['get', 'post'], 'confirm-payout/{trx_id}', [PayoutController::class, 'confirmPayout'])->name('payout.confirm');
                Route::post('confirm-payout/flutterwave/{trx_id}', [PayoutController::class, 'flutterwavePayout'])->name('payout.flutterwave');
                Route::post('confirm-payout/paystack/{trx_id}', [PayoutController::class, 'paystackPayout'])->name('payout.paystack');
                Route::get('payout-check-limit', [PayoutController::class, 'checkLimit'])->name('payout.checkLimit');
                Route::post('payout-bank-form', [PayoutController::class, 'getBankForm'])->name('payout.getBankForm');
                Route::post('payout-bank-list', [PayoutController::class, 'getBankList'])->name('payout.getBankList');
            });

            /* ===== Manage Two Step ===== */
            Route::get('two-step-security', [TwoFaSecurityController::class, 'twoStepSecurity'])->name('twostep.security');
            Route::post('twoStep-enable', [TwoFaSecurityController::class, 'twoStepEnable'])->name('twoStepEnable');
            Route::post('twoStep-disable', [TwoFaSecurityController::class, 'twoStepDisable'])->name('twoStepDisable');
            Route::post('twoStep/re-generate', [TwoFaSecurityController::class, 'twoStepRegenerate'])->name('twoStepRegenerate');


            /* ===== Push Notification ===== */
            Route::get('push-notification-show', [InAppNotificationController::class, 'show'])->name('push.notification.show');
            Route::get('push-notification-readAll', [InAppNotificationController::class, 'readAll'])->name('push.notification.readAll');
            Route::get('push-notification-readAt/{id}', [InAppNotificationController::class, 'readAt'])->name('push.notification.readAt');

            Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {
                Route::get('/', [SupportTicketController::class, 'index'])->name('list');
                Route::get('/create', [SupportTicketController::class, 'create'])->name('create');
                Route::post('/create', [SupportTicketController::class, 'store'])->name('store');
                Route::get('/view/{ticket}', [SupportTicketController::class, 'ticketView'])->name('view');
                Route::put('/reply/{ticket}', [SupportTicketController::class, 'reply'])->name('reply');
                Route::get('/download/{ticket}', [SupportTicketController::class, 'download'])->name('download');
                Route::post('close/{ticket}', [SupportTicketController::class, 'close'])->name('close');
            });


            Route::post('kyc/submit', [KycVerificationController::class, 'kycVerificationSubmit'])->name('kyc.verification.submit');
            Route::get('verification/center',[KycVerificationController::class,'getUserKyc'])->name('show.user.kyc');

            Route::get('profile', [HomeController::class, 'profile'])->name('profile');
            Route::post('profile-update', [HomeController::class, 'profileUpdate'])->name('profile.update');
            Route::post('profile-update/image', [HomeController::class, 'profileUpdateImage'])->name('profile.update.image');
            Route::post('update/password', [HomeController::class, 'updatePassword'])->name('updatePassword');

            /* ===== Wishlist ===== */
            Route::post('add/to/wishlist',[WishlistController::class,'addToWishlist'])->name('addToWishList');
            Route::post('remove/from/wishlist',[WishlistController::class,'removeFromWishlist'])->name('removeFromWishlist');
            Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist');
            /* ===== Plan List ===== */
            Route::get('investment-plan',[FrontendController::class,'plans'])->name('plans');

            /* Purchase Plan */
            Route::post('invest/plan',[HomeController::class,'investPlan'])->name('investPlan')->middleware(('throttle:1,0.15'));
            Route::get('payment', [PaymentController::class, 'index'])->name('payment');
            Route::post('plan/purchase/request',[PurchasePlanController::class,'paymentRequest'])->name('plan.purchase.request');
            Route::get('/plan/investment',[HomeController::class,'investment'])->name('plan.investment');

            Route::get('orders', [HomeController::class, 'orders'])->name('orders');
            Route::get('order-items/{id}', [HomeController::class, 'orderItems'])->name('orderItems');

            /* ===== Referral ===== */
            Route::get('/referral',[HomeController::class,'referral'])->name('referral');
            Route::get('/referral/bonus',[HomeController::class,'referralBonus'])->name('referral.bonus');
            Route::get('referral/bonus/history',[HomeController::class,'getReferralsBonus'])->name('referral.bonus.history');
            Route::post('get-referral-user',[HomeController::class,'getReferralUser'])->name('myGetDirectReferralUser');

            /* ===== Badges ===== */
            Route::get('/badges', [HomeController::class, 'badges'])->name('badges');

            /* user notification permission */
            Route::get('notification/permission', [NotificationPermissionController::class, 'index'])->name('notification.permission');
            Route::post('notification/permission/update', [NotificationPermissionController::class, 'notifyPermissionUpdate'])->name('notification.permission.update');

            /*======== ADD RATING ON PRODUCT ITEMS ========*/
            Route::post('/rating', [RatingController::class, 'store'])->name('addRating');
        });
    });




    Route::get('captcha', [ManualRecaptchaController::class, 'reCaptCha'])->name('captcha');

    /* Manage User Deposit */
    Route::get('supported-currency', [DepositController::class, 'supportedCurrency'])->name('supported.currency');
    Route::post('payment-request', [DepositController::class, 'paymentRequest'])->name('payment.request');
    Route::get('deposit-check-amount', [DepositController::class, 'checkAmount'])->name('deposit.checkAmount');

    Route::get('payment-process/{trx_id}', [PaymentController::class, 'depositConfirm'])->name('payment.process');
    Route::post('addFundConfirm/{trx_id}', [PaymentController::class, 'fromSubmit'])->name('addFund.fromSubmit');
    Route::match(['get', 'post'], 'success/{id?}', [PaymentController::class, 'success'])->name('success');
    Route::match(['get', 'post'], 'failed', [PaymentController::class, 'failed'])->name('failed');

    Route::post('khalti/payment/verify/{trx}', [\App\Http\Controllers\khaltiPaymentController::class, 'verifyPayment'])->name('khalti.verifyPayment');
    Route::post('khalti/payment/store', [khaltiPaymentController::class, 'storePayment'])->name('khalti.storePayment');

    Route::get('blogs/{category_id?}', [BlogController::class, 'blog'])->name('blog');
    Route::get('blog-details/{slug}', [BlogController::class, 'blogDetails'])->name('blog.details');
    Route::get('auth/{socialite}', [SocialiteController::class, 'socialiteLogin'])->name('socialiteLogin');
    Route::get('auth/callback/{socialite}', [SocialiteController::class, 'socialiteCallback'])->name('socialiteCallback');

    /*===== Contact ======*/
    Route::post('send/contact/info',[FrontendController::class,'sentContactInfo'])->name('sent.contact.info');

    /*===== Shop ======*/
    Route::get('shop', [FrontendController::class, 'shop'])->name('shop');
    Route::get('products', [FrontendController::class, 'products'])->name('products');
    Route::get('product/{slug}',[FrontendController::class,'productDetails'])->name('product.details');

    /* ===== Cart ===== */
    Route::post('add-cart', [CartController::class, 'addToCart'])->name('addToCart');
    Route::delete('remove-cart', [CartController::class, 'remove'])->name('removeToCart');
    Route::delete('empty-cart', [CartController::class, 'empty'])->name('emptyCart');
    Route::put('cart', [CartController::class, 'updateQuantity'])->name('cartUpdate');
    /*======== Cart View ========*/
    Route::get('view-cart',[CartController::class,'cart'])->name('cart');

    /*======== Apply Coupon ========*/
    Route::post('coupon-apply', [FrontendController::class, 'applyCoupon'])->name('applyCoupon');

    /* ===== Order ===== */
    Route::get('checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('shipping-charge', [OrderController::class, 'shippingCharge'])->name('shipping.charge');
    Route::post('order', [OrderController::class, 'order'])->name('order');
    Route::get('payment', [OrderController::class, 'payment'])->name('payment');
    Route::get('payment-check-amount', [PaymentController::class, 'checkAmount'])->name('payment.checkAmount');
    Route::get('order/complete/{id}', [OrderController::class, 'orderComplete'])->name('order.complete');
    Route::post('order/payment/request', [OrderController::class, 'orderPaymentRequest'])->name('order.payment.request');

    // subscribe
    Route::post('/subscribe', [FrontendController::class, 'subscribe'])->name('subscribe');

    Auth::routes();
    /*= Frontend Manage Controller =*/
    Route::get("/{slug?}", [FrontendController::class, 'page'])->name('page');
});


