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
  // Eqhit filter routes
    Route::get('eqhit/fig/search', [EqhitController::class, 'searchFig'])->name('eqhit.fig.search');
    Route::get('eqhit/pno/search', [EqhitController::class, 'searchPno'])->name('eqhit.pno.search');
    Route::get('eqhit/name/search', [EqhitController::class, 'searchName'])->name('eqhit.name.search');
    Route::get('eqhit/model/search', [EqhitController::class, 'searchModel'])->name('eqhit.model.search');

});
