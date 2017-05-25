<?php

Route::get(env('HC_ADMIN_URL'), ['middleware' => ['auth'], 'as' => 'admin.index'], function (){

});
