<?php

declare(strict_types = 1);

return [

    /*
    |--------------------------------------------------------------------------
    | Admin url prefix for all admin routes
    |--------------------------------------------------------------------------
    |
    | You can add custom prefix for admin urls. Instead of "/admin" you can set this to any name that you want
    | By default admin url is "/admin"
    |
    */
    'admin_url' => env('HC_ADMIN_URL', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Registration page
    |--------------------------------------------------------------------------
    |
    | You can allow users to register their account by themselves
    |
    */
    'allow_registration' => false,

    /*
    |---------------------------------------------------------------------------
    | Redirect url
    |---------------------------------------------------------------------------
    */
    'auth_redirect' => 'auth/login',


    /*
    |---------------------------------------------------------------------------
    | Google map api key
    |---------------------------------------------------------------------------
    */

    'google_map_api_key' => env('GOOGLE_MAP_API_KEY', ''),

];
