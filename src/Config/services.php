<?php

return [
    'rollbar' => [
        'access_token'           => env('ROLLBAR_BE_ACCESS_TOKEN', ''),
        'front_end_access_token' => env('ROLLBAR_FE_ACCESS_TOKEN', ''),
        'level'                  => env('ROLLBAR_LEVEL'),
    ],
];