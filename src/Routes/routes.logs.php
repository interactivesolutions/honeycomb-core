<?php

Route::prefix(config('hc.admin_url'))
    ->middleware('auth')
    ->group(function () {
        Route::get('logs', 'HCLogViewerController@index')
            ->name('admin.logs');
    });
