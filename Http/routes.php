<?php

Route::middleware('web')->namespace('Modules\OpenId\Http\Controllers')->group(function () {
    Route::get('login', 'LoginController@login')->name('login');

    Route::get('logout', 'LoginController@logout')->name('logout');

    Route::prefix('openid')->as('openid.')->group(function () {
        Route::get('callback', 'OpenIdController@callback')->name('callback');
    });
});