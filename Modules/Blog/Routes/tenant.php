<?php


use Illuminate\Support\Facades\Route;

Route::prefix('manage')->middleware(['auth', 'RoutePermissionCheck:blogs.index'])->group(function () {
    Route::resource('blogs', 'BlogController')->except('update', 'destroy');
    Route::post('blogs/update', 'BlogController@update')->name('blogs.update');
    Route::post('blogs/destroy', 'BlogController@destroy')->name('blogs.destroy');

    Route::resource('blog-category', 'BlogCategoryController')->except('update', 'destroy');
    Route::post('blog-category/update', 'BlogCategoryController@update')->name('blog-category.update');
    Route::get('blog-category/destroy/{id}', 'BlogCategoryController@destroy')->name('blog-category.destroy');
});

