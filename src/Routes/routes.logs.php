<?php

Route::group(['prefix' => config('hc.admin_url'), 'middleware' => ['auth']], function() {
    Route::get('logs', ['as' => 'admin.logs', 'uses' => 'HCLogViewerController@index']);
});
