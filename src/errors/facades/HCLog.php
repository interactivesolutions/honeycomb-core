<?php

namespace interactivesolutions\honeycombcore\errors\facades;

use Illuminate\Support\Facades\Facade;

class HCLog extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'hclog';
    }

}