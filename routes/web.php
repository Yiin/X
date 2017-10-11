<?php

/**
 * Serve a front-end application.
 */
Route::middleware('web-auth')->group(function () {
    Route::name('user.confirmation')->get('email-confirmation/{token}', 'Web\WebController@confirmUser');
    Route::name('frontend-application')->get('{any?}', 'Web\WebController@serveApplication');
});
