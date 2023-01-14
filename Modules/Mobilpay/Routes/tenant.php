<?php

Route::prefix('mobilpay')->middleware(['auth', 'student'])->group(function () {
    Route::get('/return', 'MobilpayController@return')->middleware(['auth', 'student']);
});

Route::post('mobilpay/confirm/deposit', 'MobilpayController@confirmDeposit');
Route::post('mobilpay/confirm/payment', 'MobilpayController@confirmPayment');
