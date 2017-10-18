<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Models\Traits;

/**
 * Trait CustomAppends
 * @package InteractiveSolutions\HoneycombCore\Models\Traits
 */
trait CustomAppends
{
    /**
     * Select custom appends attributes
     *
     * @var array
     */
    public static $customAppends = [];

    /**
     * Append attribute ignore
     *
     * @return array
     */
    protected function getArrayAbleAppends()
    {
        if (property_exists($this, 'customAppends')) {

            // you can set custom appends array
            if (is_array(self::$customAppends) && count(self::$customAppends)) {
                return self::$customAppends;
            } // or if you want to disable custom appends just write false i.e. Model::$customAppends = false;
            elseif (is_bool(self::$customAppends) && !self::$customAppends) {
                return [];
            }
        }

        return parent::getArrayAbleAppends();
    }
}