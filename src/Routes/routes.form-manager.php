<?php

Route::prefix(config('hc.admin_url'))
    ->group(function () {
        Route::get('api/form-manager/{id}', 'HCFormManagerController@getStructure')
            ->name('admin.api.form-manager')
            ->middleware(['web', 'auth']);
    });
