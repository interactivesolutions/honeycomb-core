<?php

Route::get(env('HC_ADMIN_URL', 'admin'), ['middleware' => 'auth', 'as' => 'admin.index', 'uses' => 'HCAdminController@index']);
