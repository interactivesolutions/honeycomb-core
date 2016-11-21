<?php

Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
    Route::get('logs', ['as' => 'admin.logs', 'uses' => 'HCLogViewerController@index']);
});
