<?php

Route::prefix('popup-content')->group(function () {
    Route::get('/', 'PopupContentController@index')->name('popup-content.index');
    Route::post('/update', 'PopupContentController@update')->name('popup-content.update');
});
