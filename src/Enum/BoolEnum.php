<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombNewCore\Enum;


/**
 * Class BoolEnum
 * @package InteractiveSolutions\HoneycombNewCore\Enum
 */
class BoolEnum extends Enumerable
{
    /**
     * @return BoolEnum|Enumerable
     */
    final public static function no(): BoolEnum
    {
        return self::make(0, trans('HCCore::enum.bool.no'));
    }

    /**
     * @return BoolEnum|Enumerable
     */
    final public static function yes(): BoolEnum
    {
        return self::make(1, trans('HCCore::enum.bool.yes'));
    }
}
