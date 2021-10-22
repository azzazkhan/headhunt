<?php
/*
 * File name: web.php
 * Last modified: 2021.08.10 at 18:04:14
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Login routes
Route::get('login/{service}', 'Auth\LoginController@redirectToProvider');
Route::get('login/{service}/callback', 'Auth\LoginController@handleProviderCallback');
Auth::routes();

/**
 * Payment Routes
 * 1. Razor Pay
 * 2. Stripe
 * 3. Stripe FPX
 * 4. Flutter Wave
 * 5. PayStack
 * 6. PayPal
 */
// Payment failure
Route::get('payments/failed', 'PayPalController@index')->name('payments.failed');
// Razor Pay
Route::get('payments/razorpay/checkout', 'RazorPayController@checkout');
Route::post('payments/razorpay/pay-success/{bookingId}', 'RazorPayController@paySuccess');
Route::get('payments/razorpay', 'RazorPayController@index');
// Stripe
Route::get('payments/stripe/checkout', 'StripeController@checkout');
Route::get('payments/stripe/pay-success/{bookingId}/{paymentMethodId}', 'StripeController@paySuccess');
Route::get('payments/stripe', 'StripeController@index');
// Stripe FPX
Route::get('payments/stripe-fpx/checkout', 'StripeFPXController@checkout');
Route::get('payments/stripe-fpx/pay-success/{bookingId}', 'StripeFPXController@paySuccess');
Route::get('payments/stripe-fpx', 'StripeFPXController@index');
// Flutter Wave
Route::get('payments/flutterwave/checkout', 'FlutterWaveController@checkout');
Route::get('payments/flutterwave/pay-success/{bookingId}/{transactionId}', 'FlutterWaveController@paySuccess');
Route::get('payments/flutterwave', 'FlutterWaveController@index');
// PayStack
Route::get('payments/paystack/checkout', 'PayStackController@checkout');
Route::get('payments/paystack/pay-success/{bookingId}/{reference}', 'PayStackController@paySuccess');
Route::get('payments/paystack', 'PayStackController@index');
// PayPal
Route::get('payments/paypal/express-checkout', 'PayPalController@getExpressCheckout')->name('paypal.express-checkout');
Route::get('payments/paypal/express-checkout-success', 'PayPalController@getExpressCheckoutSuccess');
Route::get('payments/paypal', 'PayPalController@index')->name('paypal.index');


// Firebase Service Worker
Route::get('firebase/sw-js', 'AppSettingController@initFirebase');

// Conversation Uploads
Route::get('storage/app/public/{id}/{conversion}/{filename?}', 'UploadController@storage');

/**
 * Routes accessible by authenticated users only
 */
Route::middleware('auth')->group(function () {
    //* Certificate routes
    Route::resource('certificates', 'CertificateController')
         ->except([
             'create', 'store'
         ])
         ->names(['destroy' => 'delete']);

    // TODO: Laravel app logs (must be accessible by admin only!)
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

    // Dashboard
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');

    // Uploads
    Route::post('uploads/store', 'UploadController@store')->name('medias.create');

    // User/Profile
    Route::resource('users', 'UserController');
    Route::get('users/profile', 'UserController@profile')->name('users.profile');
    Route::post('users/remove-media', 'UserController@removeMedia');

    //* Media management/operations
    Route::group(['middleware' => ['permission:medias']], function () {
        Route::get('medias', 'UploadController@index')->name('medias');
        Route::get('uploads/all/{collection?}', 'UploadController@all');
        Route::get('uploads/collectionsNames', 'UploadController@collectionsNames');
        //! Destructive operations!
        Route::post('uploads/clear', 'UploadController@clear')->name('medias.delete');
        Route::get('uploads/clear-all', 'UploadController@clearAll');
    });

    //* Permission management
    Route::group(['middleware' => ['permission:permissions.index']], function () {
        Route::get('permissions/role-has-permission', 'PermissionController@roleHasPermission');
        Route::get('permissions/refresh-permissions', 'PermissionController@refreshPermissions');
        Route::post('permissions/give-permission-to-role', 'PermissionController@givePermissionToRole');
        Route::post('permissions/revoke-permission-to-role', 'PermissionController@revokePermissionToRole');
    });

    //* Global application settings
    Route::group(['middleware' => ['permission:app-settings']], function () {
        Route::prefix('settings')->group(function () {
            // Full CRUD routes (resource routes)
            Route::resource('permissions', 'PermissionController');
            Route::resource('roles', 'RoleController');
            Route::resource('customFields', 'CustomFieldController');
            Route::resource('currencies', 'CurrencyController')->except(['show']);
            Route::resource('taxes', 'TaxController')->except(['show']);

            // Login as other user (mimic roles)
            Route::get('users/login-as-user/{id}', 'UserController@loginAsUser')->name('users.login-as-user');

            // Update app settings, translation settings
            Route::patch('update', 'AppSettingController@update');
            Route::patch('translate', 'AppSettingController@translate');

            // Translation sync, cache dumping
            Route::get('sync-translation', 'AppSettingController@syncTranslation');
            Route::get('clear-cache', 'AppSettingController@clearCache');

            // Update framework
            Route::get('check-update', 'AppSettingController@checkForUpdates');

            // Disable special character and number in route params
            Route::get('/{type?}/{tab?}', 'AppSettingController@index')
                 ->where('type', '[A-Za-z]*')
                 ->where('tab', '[A-Za-z]*')
                 ->name('app-settings');
        });
    });

    // Provider Type CRUD (resource route)
    Route::resource('eProviderTypes', 'EProviderTypeController')->except(['show']);
    // Remove Provider Media
    Route::post('eProviders/remove-media', 'EProviderController@removeMedia');
    // Provider CRUD (resource route)
    Route::resource('eProviders', 'EProviderController')->except(['show']);

    // Request Provider
    Route::get('requestedEProviders', 'EProviderController@requestedEProviders')->name('requestedEProviders.index');

    // Address, awards, experiences, availability hours CRUD (resource routes)
    Route::resource('addresses', 'AddressController')->except(['show']);
    Route::resource('awards', 'AwardController');
    Route::resource('experiences', 'ExperienceController');
    Route::resource('availabilityHours', 'AvailabilityHourController')->except(['show']);

    // EService CRUD, and EService Media route (resource route)
    Route::post('eServices/remove-media', 'EServiceController@removeMedia');
    Route::resource('eServices', 'EServiceController')->except(['show']);

    // Faq categories, Faqs CRUD (resource routes)
    Route::resource('faqCategories', 'FaqCategoryController')->except(['show']);
    Route::resource('faqs', 'FaqController')->except(['show']);

    Route::post('categories/remove-media', 'CategoryController@removeMedia');

    // Categories & Booking statuses CRUD (resource routes)
    Route::resource('categories', 'CategoryController')->except(['show']);
    Route::resource('bookingStatuses', 'BookingStatusController')->except(['show']);

    // Gallery media and CRUD route (resource route)
    Route::post('galleries/remove-media', 'GalleryController@removeMedia');
    Route::resource('galleries', 'GalleryController')->except(['show']);

    // EService reviews CRUD (resource route)
    Route::resource('eServiceReviews', 'EServiceReviewController')->except(['show']);

    // Payments, Payment Method, Payment Statuses CRUD and payment method (resource routes0)
    Route::resource('payments', 'PaymentController')->except(['create', 'store', 'edit', 'update', 'destroy']);
    Route::post('paymentMethods/remove-media', 'PaymentMethodController@removeMedia');
    Route::resource('paymentMethods', 'PaymentMethodController')->except(['show']);
    Route::resource('paymentStatuses', 'PaymentStatusController')->except(['show']);
    
    // Favorites, notifications, bookings, earnings CURD (resource routes)
    Route::resource('favorites', 'FavoriteController')->except(['show']);
    Route::resource('notifications', 'NotificationController')->except(['create', 'store', 'update', 'edit',]);
    Route::resource('bookings', 'BookingController');
    Route::resource('earnings', 'EarningController')->except(['show', 'edit', 'update']);

    // EProvider CRUD (resource routes)
    Route::get('eProviderPayouts/create/{id}', 'EProviderPayoutController@create')->name('eProviderPayouts.create');
    Route::resource('eProviderPayouts', 'EProviderPayoutController')->except(['show', 'edit', 'update', 'create']);

    // Options, Option Group CRUD and option media route (resource route)
    Route::resource('optionGroups', 'OptionGroupController')->except(['show']);
    Route::post('options/remove-media', 'OptionController@removeMedia');
    Route::resource('options', 'OptionController')->except(['show']);

    // Coupons CRUD (resource route)
    Route::resource('coupons', 'CouponController')->except(['show']);

    // Slides CRUD and slides media (resource route)
    Route::post('slides/remove-media', 'SlideController@removeMedia');
    Route::resource('slides', 'SlideController')->except(['show']);

    // Custom pages, Wallets, Transactions CRUD (resource routes)
    Route::resource('customPages', 'CustomPageController');
    Route::resource('wallets', 'WalletController')->except(['show']);
    Route::resource('walletTransactions', 'WalletTransactionController')->except([
        'show', 'edit', 'update', 'destroy'
    ]);
});
