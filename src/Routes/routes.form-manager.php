<?php

Route::get(config('hc.admin_url') . '/api/form-manager/{id}', [
    'middleware' => ['web', 'auth'],
    'as' => 'admin.api.form-manager',
    'uses' => 'HCFormManagerController@getStructure',
]);
Route::get('api/public/form-manager/{id}', [
    'middleware' => ['web'],
    'as' => 'public.api.form-manager',
    'uses' => 'HCFormManagerController@getStructure',
]);
