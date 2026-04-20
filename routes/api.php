<?php


use App\Http\Controllers\API\V1\DepositController;
use App\Http\Controllers\API\V1\KycController;
use App\Http\Controllers\API\V1\ProductController;
use App\Http\Controllers\API\V1\SupportTicketController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PayoutLogController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\API\V1\UserAuthController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\InvestmentPlanController;
use App\Http\Controllers\API\V1\PayoutController;
use App\Http\Controllers\API\V1\ReferralController;
use App\Http\Controllers\API\V1\NotificationController;
use App\Http\Controllers\API\V1\PaymentController as ApiPaymentController;
use App\Http\Controllers\API\V1\TwoFAVerificationController;
use App\Http\Controllers\API\V1\VerificationController;
use App\Http\Controllers\API\V1\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::match(['get', 'post'], 'payment/{code}/{trx?}/{type?}', [PaymentController::class, 'gatewayIpn'])->name('ipn');
Route::post('payout/{code}', [PayoutLogController::class, 'payout'])->name('payout');

//manage login & registration
Route::post('register', [UserAuthController::class, 'register']);
Route::post('login', [UserAuthController::class, 'login']);

//mange recover password
Route::post('recovery-pass/get-email', [UserAuthController::class, 'getEmailForRecoverPass']);
Route::post('recovery-pass/get-code', [UserAuthController::class, 'getCodeForRecoverPass']);
Route::post('update-pass', [UserAuthController::class, 'updatePass']);

//get languages
Route::get('languages/{id?}',[UserController::class,'language']);


Route::middleware('auth:sanctum')->group(function (){

    //check user verification status
    Route::middleware('apiUserCheck')->group(function () {

        //logout
        Route::post('logout', [UserAuthController::class, 'logout']);
        Route::delete('delete/account', [UserController::class, 'deleteAccount']);

        // kyc middleware
        Route::middleware('apiKycCheck')->group(function () {
            //manage investment plan
            Route::get('investment-plans', [InvestmentPlanController::class, 'index']);
            Route::post('invest/plan',[InvestmentPlanController::class, 'investPlan']);
            Route::get('plan/invest/history',[InvestmentPlanController::class, 'investHistory']);

            //Card Payment
            Route::post('card/payment', [ApiPaymentController::class,'cardPayment']);

            //payment web view
            Route::get('payment-webview', [ApiPaymentController::class, 'paymentWebview']);

            //payment done
            Route::post('payment-done', [ApiPaymentController::class, 'paymentDone']);

            // payment gateway
            Route::get('gateways', [ApiPaymentController::class, 'gateways']);

            //manage deposit
            Route::post('deposit', [DepositController::class, 'paymentRequest']);
            Route::get('deposit/history', [DepositController::class, 'depositHistory']);

            //manual payment
            Route::post('manual/payment/submit/{trx_id?}', [ApiPaymentController::class,'manualPayment']);

            // manage payout
            Route::post('payout', [PayoutController::class, 'payout']);
            Route::get('payout-details/{trx_id}', [PayoutController::class, 'payoutDetails']);
            Route::post('confirm-payout/{trx_id}', [PayoutController::class, 'confirmPayout']);
            Route::post('paystack-payout/{trx_id}', [PayoutController::class, 'paystackPayout']);
            Route::post('flutterwave-payout/{trx_id}', [PayoutController::class, 'flutterwavePayout']);
            Route::post('get-bank-list', [PayoutController::class, 'getBankList']);
            Route::post('get-bank-form', [PayoutController::class, 'getBankForm']);
            Route::get('payout-method', [PayoutController::class, 'payoutMethod']);
            Route::get('payout/history', [PayoutController::class, 'payoutHistory']);

            // get user transaction history
            Route::get('transactions', [UserController::class, 'transactions'])->name('transaction.list');
        });


        // Notification Permission
        Route::get('notifications/template', [UserController::class, 'template']);
        Route::post('update/notification-permission', [UserController::class, 'updateNotificationPermission']);

        // manage referral
        Route::get('referral-users', [ReferralController::class, 'referralUsers']);
        Route::get('get-direct/referral-users', [ReferralController::class, 'getReferralUser']);
        Route::get('referral-bonus/history', [ReferralController::class, 'referralBonusHistory']);

        // manage support ticket
        Route::get('support-tickets', [SupportTicketController::class, 'index']);
        Route::post('support-ticket/store', [SupportTicketController::class, 'store']);
        Route::post('ticket-reply/{id}', [SupportTicketController::class, 'reply']);
        Route::get('close-ticket/{ticket}', [SupportTicketController::class, 'close']);
        Route::get('ticket/view/{ticket}', [SupportTicketController::class, 'ticketView']);

        // manage kyc
        Route::get('kyc', [KycController::class, 'index']);
        Route::post('submit-kyc', [KycController::class, 'submitKyc']);
        Route::get('user-kyc', [KycController::class, 'userKyc']);

        // manage user
        Route::get('profile', [UserController::class, 'profile']);
        Route::post('update-profile', [UserController::class, 'updateProfile']);
        Route::post('change-password', [UserController::class, 'changePassword']);

        // manage notification
        Route::get('push-notification-show', [NotificationController::class, 'show']);
        Route::get('push/notification/readAll', [NotificationController::class, 'readAll']);
        Route::get('push-notification-readAt/{id}', [NotificationController::class, 'readAt']);

        // manage two fa
        Route::get('two-step-security', [TwoFAVerificationController::class, 'twoStepSecurity']);
        Route::post('twoStep-enable', [TwoFAVerificationController::class, 'twoStepEnable']);
        Route::post('twoStep-disable', [TwoFAVerificationController::class, 'twoStepDisable']);
        Route::post('twoStep/re-generate', [TwoFAVerificationController::class, 'twoStepRegenerate']);

        // firebase config
        Route::post('firebase-token/save', [UserController::class, 'firebaseTokenSave']);

        // products
        Route::get('products', [ProductController::class, 'products']);
        Route::get('product/details/{id}', [ProductController::class, 'productDetails']);

        // Orders
        Route::post('order', [OrderController::class, 'order']);
        Route::post('order/make/payment', [OrderController::class, 'makePayment']);
        Route::get('orders', [UserController::class, 'orders']);
        Route::get('order/details/{id}', [UserController::class, 'orderDetails']);

        /*======== ADD RATING ON PRODUCT ITEMS ========*/
        Route::post('/rating', [ProductController::class, 'addRating']);

        /* ===== Wishlist ===== */
        Route::post('add/to/wishlist',[UserController::class,'addToWishlist']);
        Route::get('wishlist', [UserController::class, 'wishlist']);

        /* ===== Area ===== */
        Route::get('area', [UserController::class, 'area']);

        /*======== Apply Coupon ========*/
        Route::post('coupon-apply', [UserController::class, 'applyCoupon']);

        //pusher config
        Route::get('pusher-config', [UserController::class, 'pusherConfig']);

        //gold price rate history
        Route::get('gold/rates', [UserController::class, 'goldRates']);

    });

    //manage verification
    Route::get('api/user/check', [VerificationController::class, 'check'])->name('api.user.check');
    Route::get('resend_code', [VerificationController::class, 'resendCode']);
    Route::post('mail-verify', [VerificationController::class, 'mailVerify']);
    Route::post('sms-verify', [VerificationController::class, 'smsVerify']);
    Route::post('twoFA-Verify', [VerificationController::class, 'twoFAverify']);
});


Route::get('language/{id?}', [UserController::class, 'language']);



