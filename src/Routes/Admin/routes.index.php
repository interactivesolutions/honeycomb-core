<?php

Route::get(config('hc.admin_url'), [
    'middleware' => 'auth',
    'as' => 'admin.index',
    'uses' => 'Admin\HCAdminController@index',
]);
