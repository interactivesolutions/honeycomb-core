<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Contracts;

/**
 * Interface HCFormContract
 * @package InteractiveSolutions\HoneycombCore\Http\Controllers\Interfaces
 */
interface HCFormContract
{
    /**
     * @param bool $edit
     * @return array
     */
    public function createForm(bool $edit = false): array;
}
