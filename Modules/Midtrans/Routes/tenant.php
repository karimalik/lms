<?php

Route::prefix('midtrans')->middleware(['auth','student'])->group(function() {
    Route::get('/callback-success', 'MidtransController@paymentSuccess')->name('midtransPaymentSuccess');
    Route::get('/callback-pending', 'MidtransController@paymentPending')->name('midtransPaymentPending');
    Route::get('/callback-failed', 'MidtransController@paymentFailed')->name('midtransPaymentfailed');
});
