<?php

Route::group(['prefix' => env('HC_ADMIN_URL'), 'middleware' => ['auth']], function () {
    Route::get('logs', ['as' => 'admin.logs', 'uses' => 'HCLogViewerController@index']);
});
