<?php

Route::prefix('instamojo')->middleware(['auth','student'])->group(function() {
    Route::get('/test-success', 'InstamojoController@testSuccess')->name('instamojoTestSuccess');
    Route::get('/deposit-success', 'InstamojoController@depositSuccess')->name('instamojoDepositSuccess');
    Route::get('/payment-success', 'InstamojoController@paymentSuccess')->name('instamojoPaymentSuccess');
});
