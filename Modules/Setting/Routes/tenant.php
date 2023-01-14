<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'setting', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', 'SettingController@index')->name('setting.index');
    Route::get('/activation', 'SettingController@activation')->name('setting.activation')->middleware('RoutePermissionCheck:setting.activation');
    Route::get('/general-settings', 'SettingController@general_settings')->name('setting.general_settings')->middleware('RoutePermissionCheck:setting.general_settings');
    Route::get('/email-configaration', 'SettingController@email_setup')->name('setting.email_setup')->middleware('RoutePermissionCheck:setting.email_setup');
    Route::get('/seo-setup', 'SettingController@seo_setting')->name('setting.seo_setting')->middleware('RoutePermissionCheck:setting.seo_setting');
    Route::get('/payment-setup', 'SettingController@paymentSetup')->name('setting.paymentSetup')->middleware('RoutePermissionCheck:route_name');
    Route::get('/social-login-setup', 'SettingController@social_login_setup')->name('setting.social_login_setup')->middleware('RoutePermissionCheck:setting.social_login_setup');
    Route::post('/update-activation-status', 'SettingController@update_activation_status')->name('update_activation_status')->middleware('RoutePermissionCheck:settings.ChangeActivationStatus');
    Route::post('general-settings/update', 'GeneralSettingsController@update')->name('company_information_update')->middleware('RoutePermissionCheck:settings.general_setting_update');
    Route::post('smtp-gateway-credentials/update', 'GeneralSettingsController@smtp_gateway_credentials_update')->name('smtp_gateway_credentials_update');
    Route::post('/test-mail/send', 'GeneralSettingsController@test_mail_send')->name('test_mail.send')->middleware('RoutePermissionCheck:setting.send_test_mail');
    Route::post('/social_login', 'GeneralSettingsController@socialCreditional')->name('socialCreditional')->middleware('RoutePermissionCheck:setting.setting.social_login_setup_update');
    Route::post('/seo-setup', 'GeneralSettingsController@seoSetting')->name('seo_setup')->middleware('RoutePermissionCheck:setting.seo_setting_update');

    Route::get('/student-setup', 'SettingController@student_setup')->name('settings.student_setup')->middleware('RoutePermissionCheck:settings.student_setup');
    Route::post('/student-setup', 'SettingController@student_setup_update')->name('settings.student_setup_update')->middleware('RoutePermissionCheck:settings.student_setup_update');

    Route::get('/instructor-setup', 'SettingController@instructor_setup')->name('settings.instructor_setup')->middleware('RoutePermissionCheck:settings.instructor_setup');
    Route::post('/instructor-setup', 'SettingController@instructor_setup_update')->name('settings.instructor_setup_update')->middleware('RoutePermissionCheck:settings.instructor_setup_update');


    Route::get('/footerEmailConfig', 'GeneralSettingsController@footerEmailConfig')->name('footerEmailConfig')->middleware('RoutePermissionCheck:footerEmailConfig');
    Route::get('/EmailTemp', 'GeneralSettingsController@EmailTemp')->name('EmailTemp')->middleware('RoutePermissionCheck:EmailTemp');


    Route::resource('currencies', 'CurrencyController')->except('destroy')->middleware('RoutePermissionCheck:currencies.index');
    Route::post('currency-edit-modal', 'CurrencyController@edit_modal')->name('currencies.edit_modal')->middleware('RoutePermissionCheck:currencies.edit_modal');
    Route::get('/currencies/destroy/{id}', 'CurrencyController@destroy')->name('currencies.destroy')->middleware('RoutePermissionCheck:currencies.destroy');


    Route::get('/aboutSystem', 'GeneralSettingsController@aboutSystem')->name('setting.aboutSystem')->middleware('RoutePermissionCheck:setting.aboutSystem');
    Route::get('/updateSystem', 'UpdateController@updateSystem')->name('setting.updateSystem')->middleware('RoutePermissionCheck:setting.updateSystem', 'saasAdmin');
    Route::post('/updateSystem', 'UpdateController@updateSystemSubmit')->name('setting.updateSystem.submit')->middleware('RoutePermissionCheck:setting.updateSystem.submit', 'saasAdmin');


    Route::resource('ipBlock', 'IpBlockController')->except('destroy')->middleware('RoutePermissionCheck:ipBlock.index', 'saasAdmin');
    Route::post('ipBlock-delete', 'IpBlockController@destroy')->name('ipBlockDelete')->middleware('RoutePermissionCheck:ipBlock.index', 'saasAdmin');

    Route::get('/geo-location', 'GeoLocationController@index')->name('setting.geoLocation')->middleware('RoutePermissionCheck:setting.geoLocation');
    Route::get('/geo-location-data', 'GeoLocationController@data')->name('setting.geoLocation.data')->middleware('RoutePermissionCheck:setting.geoLocation');
    Route::post('/geo-location-delete', 'GeoLocationController@destroy')->name('setting.geoLocation.delete')->middleware('RoutePermissionCheck:setting.geoLocation');
    Route::post('/geo-location-empty', 'GeoLocationController@EmptyLog')
        ->name('setting.geoLocation.empty')
        ->middleware('RoutePermissionCheck:setting.geoLocation');


    Route::get('/cron-job', 'CornJobController@index')->name('setting.cronJob')->middleware('RoutePermissionCheck:setting.cronJob');


    Route::get('/cookie-setting', 'CookieSettingController@index')
        ->name('setting.cookieSetting')
        ->middleware('RoutePermissionCheck:setting.cookieSetting');

    Route::post('/cookie-setting', 'CookieSettingController@store')
        ->name('setting.cookieSettingStore')
        ->middleware('RoutePermissionCheck:setting.cookieSettingStore');


    Route::get('/cache-setting', 'CacheSettingController@index')
        ->name('setting.cacheSetting')
        ->middleware('RoutePermissionCheck:setting.cacheSetting');

    Route::post('/cache-setting', 'CacheSettingController@store')
        ->name('setting.cacheSettingStore')
        ->middleware('RoutePermissionCheck:setting.cacheSettingStore');

    Route::resource('timezone', 'TimezoneController')->except('destroy')->middleware('RoutePermissionCheck:timezone.index');
    Route::post('timezone-edit-modal', 'TimezoneController@edit_modal')->name('timezone.edit_modal')->middleware('RoutePermissionCheck:timezone.edit_modal');
    Route::get('/timezone/destroy/{id}', 'TimezoneController@destroy')->name('timezone.destroy')->middleware('RoutePermissionCheck:timezone.destroy');


    Route::resource('city', 'CityController')->except('destroy')->middleware('RoutePermissionCheck:city.index');
    Route::post('city-edit-modal', 'CityController@edit_modal')->name('city.edit_modal')->middleware('RoutePermissionCheck:city.edit_modal');
    Route::get('/city/destroy/{id}', 'CityController@destroy')->name('city.destroy')->middleware('RoutePermissionCheck:city.destroy');


    Route::get('/maintenance', 'SettingController@maintenance')
        ->name('setting.maintenance')
        ->middleware('RoutePermissionCheck:setting.maintenance', 'saasAdmin');

    Route::post('/maintenance', 'SettingController@maintenanceAction')
        ->middleware('RoutePermissionCheck:setting.maintenance', 'saasAdmin');


    Route::get('/utilities', 'UtilitiesController@index')
        ->name('setting.utilities')
        ->middleware('RoutePermissionCheck:setting.utilities', 'saasAdmin');

    Route::post('utilities/reset-database', 'UtilitiesController@resetDatabase')->name('utilities.resetDatabase')->middleware(['RoutePermissionCheck:utilities.resetDatabase']);
    Route::post('utilities/import-demo-database', 'UtilitiesController@importDemoDatabase')->name('utilities.importDemoDatabase')->middleware(['RoutePermissionCheck:utilities.importDemoDatabase']);


    Route::get('/captcha', 'SettingController@captcha')
        ->name('setting.captcha')
        ->middleware('RoutePermissionCheck:setting.captcha');

    Route::post('/captcha', 'SettingController@captchaStore')
        ->middleware('RoutePermissionCheck:setting.captcha');


    Route::get('/socialLogin', 'SettingController@socialLogin')
        ->name('setting.socialLogin')
        ->middleware('RoutePermissionCheck:setting.socialLogin');

    Route::post('/socialLogin', 'SettingController@socialLoginStore')
        ->middleware('RoutePermissionCheck:setting.socialLogin');


    Route::get('/error_log', 'ErrorLogController@index')
        ->name('setting.error_log')
        ->middleware('RoutePermissionCheck:setting.error_log');


    Route::get('/error_log_data', 'ErrorLogController@getAllErrorLogData')
        ->name('setting.getAllErrorLogData')
        ->middleware('RoutePermissionCheck:setting.getAllErrorLogData');

    Route::post('/error_log_data', 'ErrorLogController@DeleteErrorLog')
        ->name('setting.error_log.delete')
        ->middleware('RoutePermissionCheck:setting.DeleteErrorLog');

    Route::post('/error_log_empty', 'ErrorLogController@EmptyErrorLog')
        ->name('setting.error_log.empty')
        ->middleware('RoutePermissionCheck:setting.DeleteErrorLog');


    Route::get('/push-notification', 'PushNotificationController@pushNotification')
        ->name('setting.pushNotification')
        ->middleware('RoutePermissionCheck:setting.pushNotification');

    Route::post('/push-notification', 'PushNotificationController@pushNotificationSubmit')
        ->middleware('RoutePermissionCheck:setting.pushNotification');

    Route::get('/queue-setting', 'QueueSettingController@index')
        ->name('setting.queueSetting')
        ->middleware('RoutePermissionCheck:setting.queueSetting');

    Route::post('/queue-setting', 'QueueSettingController@store')
        ->name('setting.queueSettingStore')
        ->middleware('RoutePermissionCheck:setting.queueSettingStore');

    Route::get('/preloader-setting', 'PreloaderSettingController@index')
        ->name('setting.preloader')
        ->middleware('RoutePermissionCheck:setting.preloader');

    Route::post('/preloader-setting', 'PreloaderSettingController@store')
        ->name('setting.preloaderStore')
        ->middleware('RoutePermissionCheck:setting.preloaderStore');


    Route::get('/gdrive', 'GoogleDriveController@index')->name('gdrive.setting');
    Route::post('/gdrive', 'GoogleDriveController@update')->name('gdrive.setting.update');
    Route::get('/login/google', 'GoogleDriveController@redirecttogoogleprovider')->name('setting.google.login');
    Route::get('/logout/google', 'GoogleDriveController@googlelogout')->name('setting.google.logout');
    Route::get('login/google/callback', 'GoogleDriveController@handleProviderGoogleCallback')->name('setting.google.callback');

//        Route::post('/google-drive/files', 'googledrivecontroller@listfiles')->name('drive.list.files');
//        Route::post('/google-drive/folders', 'googledrivecontroller@listfolders')->name('drive.list.folder');
//        Route::get('/google-drive/delete/{id}', 'googledrivecontroller@deletefileorfolder')->name('delete.drive.file');
//
//        Route::get('/drive', 'GoogleDriveController@getDrive'); // retreive folders
//
//        Route::get('/drive/upload', 'GoogleDriveController@uploadFile'); // File upload form
//
//        Route::post('/drive/upload', 'googledrivecontroller@uploadfile')->name('upload.drive'); // Upload file to Drive from Form
//
//        Route::get('/drive/create', 'GoogleDriveController@create'); // Upload file to Drive from Storage
//
//        Route::get('/drive/delete/{id}', 'GoogleDriveController@deleteFile'); // Delete file or folder
//
//        Route::get('dropbox/oauth', function(){
//            return Dropbox::connect();
//        })->name('dropbox.login');
//
//        Route::post('/backup/folder-create', 'backupcontroller@createbackupfolder')->name('create.new.folder'); // create folder
//        Route::post('/dropbox/upload', 'dropboxcontroller@uploadfile')->name('upload.dropbox'); // Upload file to Drive from Form
//        Route::get('/dropbox-drive/delete/{path}', 'dropboxcontroller@deletefileorfolder')->name('delete.dropbox.file');
//        Route::post('/storage/api-setting', 'backupcontroller@credentialsupdate')->name('storage.api.setting');

});
