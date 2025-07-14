<?php

use Illuminate\Support\Facades\Route;
use Botble\Base\Facades\BaseHelper;

Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => ['web', 'core']], function () {
    Route::get('settings/eqhit', [
        'as' => 'settings.eqhit',
        'uses' => 'Botble\Eqhit\Http\Controllers\SettingsController@edit',
        'permission' => 'eqhit.settings', // optional but good for security
    ]);

    Route::post('settings/eqhit', [
        'as' => 'settings.eqhit.update',
        'uses' => 'Botble\Eqhit\Http\Controllers\SettingsController@update',
        'permission' => 'eqhit.settings',
    ]);
});
