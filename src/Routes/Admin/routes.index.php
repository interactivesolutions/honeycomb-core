<?php

Route::prefix(config('hc.admin_url'))
    ->namespace('Admin')
    ->group(function () {
        Route::get('/', 'HCAdminController@index')
            ->name('admin.index')
            ->middleware('auth');
    });
