<?php

use Illuminate\Support\Facades\Route;

if (isModuleActive('LmsSaas')) {
    Route::group(['middleware' => ['subdomain']], function ($routes) {
        require('tenant.php');
    });
} else {
    require('tenant.php');
}
