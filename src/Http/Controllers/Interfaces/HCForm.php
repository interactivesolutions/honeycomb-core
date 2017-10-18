<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Http\Controllers\Interfaces;

/**
 * Interface HCForm
 * @package InteractiveSolutions\HoneycombCore\http\controllers\interfaces
 */
interface HCForm
{
    /**
     * @return mixed
     */
    public function createForm();

    /**
     * @return mixed
     */
    public function getNewStructure();

    /**
     * @param bool $append
     * @return mixed
     */
    public function getEditStructure(bool $append = false);
}