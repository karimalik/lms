<?php

Route::prefix('paytm')->middleware(['auth','student'])->group(function () {
    Route::get('/', 'PaytmController@index');
    Route::post('/payment/status', 'PaytmController@paymentCallback')->name('paytmStatus');
    Route::post('/deposit/status', 'PaytmController@depositCallback')->name('paytmDepositStatus');
    Route::post('/test/status', 'PaytmController@depositCallback')->name('paytmTestStatus');
    Route::post('/subscription/status', 'PaytmController@subscriptionCallback')->name('paytmSubscriptionStatus');
});
