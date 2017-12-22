<?php

Route::prefix(config('hc.admin_url'))
    ->middleware(['web', 'auth'])
    ->namespace('Admin')
    ->group(function () {
        Route::get('logs', 'HCLogViewerController@index')
            ->name('admin.logs');
    });
