<?php

Route::get('admin/api/form-manager/{id}', ['middleware' => ['web', 'auth'], 'as' => 'admin.api.form-manager', 'uses' => 'HCFormManagerController@getFormStructure']);
Route::get('api/public/form-manager/{id}', ['middleware' => ['web'], 'as' => 'public.api.form-manager', 'uses' => 'HCFormManagerController@getFormStructure']);;
