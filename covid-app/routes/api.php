<?php
/*
 * File name: api.php
 * Last modified: 2021.08.11 at 01:13:13
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('provider')->group(function () {
    Route::post('login', 'API\EProvider\UserAPIController@login');
    Route::post('register', 'API\EProvider\UserAPIController@register');
    Route::post('send_reset_link_email', 'API\UserAPIController@sendResetLinkEmail');
    Route::get('user', 'API\EProvider\UserAPIController@user');
    Route::get('logout', 'API\EProvider\UserAPIController@logout');
    Route::get('settings', 'API\EProvider\UserAPIController@settings');
});


Route::post('login', 'API\UserAPIController@login');
Route::post('register', 'API\UserAPIController@register');
Route::post('send_reset_link_email', 'API\UserAPIController@sendResetLinkEmail');
Route::get('user', 'API\UserAPIController@user');
Route::get('logout', 'API\UserAPIController@logout');
Route::get('settings', 'API\UserAPIController@settings');


Route::resource('e_provider_types', 'API\EProviderTypeAPIController');
Route::resource('e_providers', 'API\EProviderAPIController');
Route::resource('availability_hours', 'API\AvailabilityHourAPIController');
Route::resource('awards', 'API\AwardAPIController');
Route::resource('experiences', 'API\ExperienceAPIController');

Route::resource('faq_categories', 'API\FaqCategoryAPIController');
Route::resource('faqs', 'API\FaqAPIController');
Route::resource('custom_pages', 'API\CustomPageAPIController');

Route::resource('categories', 'API\CategoryAPIController');

Route::resource('e_services', 'API\EServiceAPIController');
Route::resource('galleries', 'API\GalleryAPIController');
Route::get('e_service_reviews/{id}', 'API\EServiceReviewAPIController@show')->name('e_service_reviews.show');
Route::get('e_service_reviews', 'API\EServiceReviewAPIController@index')->name('e_service_reviews.index');

Route::resource('currencies', 'API\CurrencyAPIController');
Route::resource('slides', 'API\SlideAPIController')->except(['show']);
Route::resource('booking_statuses', 'API\BookingStatusAPIController')->except(['show']);
Route::resource('option_groups', 'API\OptionGroupAPIController');
Route::resource('options', 'API\OptionAPIController');

// Authenticatable routes
Route::middleware('auth:api')->group(function () {
    // Routes accessible by `Provider`
    Route::group(['middleware' => ['role:provider']], function () {
        Route::prefix('provider')->group(function () {
            Route::post('users/{user}', 'API\UserAPIController@update');
            Route::get('dashboard', 'API\DashboardAPIController@provider');
            Route::resource('e_providers', 'API\EProvider\EProviderAPIController');
            Route::resource('notifications', 'API\NotificationAPIController');
            Route::get('e_service_reviews', 'API\EServiceReviewAPIController@index')->name('e_service_reviews.index');
            Route::get('e_services', 'API\EServiceAPIController@index')->name('e_services.index');
            Route::put('payments/{id}', 'API\PaymentAPIController@update')->name('payments.update');

            // Custom covid certificate routes
            Route::post('certificate', 'API\CertificateAPIController@store');
            Route::get('certificate/my-certificate', 'API\CertificateAPIController@myCertificate');
            Route::get('certificate/{certificate:ref}', 'API\CertificateAPIController@show');
        });
    });

    // Routes accessible by `Admin`
    Route::group(['middleware' => ['role:admin']], function () {
        Route::prefix('admin')->group(function () {
            // Custom covid certificate routes
            Route::get('certificate', 'API\CertificateAPIController@index');
            Route::put('certificate/{certificate:ref}', 'API\CertificateAPIController@update');
            Route::patch('certificate/{certificate:ref}', 'API\CertificateAPIController@update');
            Route::delete('certificate/{certificate:ref}', 'API\CertificateAPIController@delete');
        });
    });

    // Routes accessible by all authenticated users

    // Uploads manager
    Route::post('uploads/store', 'API\UploadAPIController@store');
    Route::post('uploads/clear', 'API\UploadAPIController@clear');

    // Profile management
    Route::post('users/{user}', 'API\UserAPIController@update');

    // Payment management
    Route::get('payments/byMonth', 'API\PaymentAPIController@byMonth')->name('payments.byMonth');
    Route::post('payments/wallets/{id}', 'API\PaymentAPIController@wallets')->name('payments.wallets');
    Route::post('payments/cash', 'API\PaymentAPIController@cash')->name('payments.cash');
    Route::resource('payment_methods', 'API\PaymentMethodAPIController')->only(['index']);

    Route::resource('favorites', 'API\FavoriteAPIController'); // Favorites
    Route::resource('addresses', 'API\AddressAPIController'); // Addresses

    // Notifications
    Route::get('notifications/count', 'API\NotificationAPIController@count');
    Route::resource('notifications', 'API\NotificationAPIController');

    Route::resource('bookings', 'API\BookingAPIController'); // Bookings
    Route::resource('earnings', 'API\EarningAPIController'); // Earning (e.g referrals)

    // Coupon code management
    Route::resource('coupons', 'API\CouponAPIController')->except(['show']);

    // Wallet management
    Route::resource('wallets', 'API\WalletAPIController')->except(['show', 'create', 'edit']);
    Route::get('wallet_transactions', 'API\WalletTransactionAPIController@index')->name('wallet_transactions.index');

    // Unknown
    Route::resource('e_provider_payouts', 'API\EProviderPayoutAPIController');
    Route::post('e_service_reviews', 'API\EServiceReviewAPIController@store')->name('e_service_reviews.store');
});

Route::any('/{any?}', function () {
    return response()->json([
        'success' => false,
        'message' => 'The request URL was not found!'
    ], 404);
})->where('any', '.*');