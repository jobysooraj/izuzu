<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;
use Botble\Ecommerce\Http\Controllers\EqhitController;

Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => ['web', 'core']], function () {
    Route::get('settings/eqhit', [
        'as'         => 'settings.eqhit',
        'uses'       => 'Botble\Eqhit\Http\Controllers\SettingsController@edit',
        'permission' => 'eqhit.settings', // optional but good for security
    ]);

    Route::post('settings/eqhit', [
        'as'         => 'settings.eqhit.update',
        'uses'       => 'Botble\Eqhit\Http\Controllers\SettingsController@update',
        'permission' => 'eqhit.settings',
    ]);

  Route::get('google', [
        'as'         => 'settings.google',
        'uses'       => 'Botble\Eqhit\Http\Controllers\GoogleSettingsController@edit',
        'permission' => 'eqhit.settings', // reuse or create a new permission
    ]);

    Route::post('google', [
        'as'         => 'settings.google.update',
        'uses'       => 'Botble\Eqhit\Http\Controllers\GoogleSettingsController@update',
        'permission' => 'eqhit.settings',
    ]);
});
