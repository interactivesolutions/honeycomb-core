<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Errors\Facades;

use Illuminate\Support\Facades\Facade;


/**
 * Class HCLog
 * @package InteractiveSolutions\HoneycombCore\errors\facades
 */
class HCLog extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'hclog';
    }

}