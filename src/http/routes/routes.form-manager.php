<?php

Route::get('admin/api/form-manager/{id}', ['middleware' => ['web', 'auth'], 'as' => 'admin.api.form-manager', 'uses' => 'HCFormManagerController@getFormStructure']);
Route::get('api/form-manager/{id}', ['middleware' => ['web'], 'as' => 'api.form-manager', 'uses' => 'HCFormManagerController@getFormStructure']);
