<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombNewCore\Contracts;

/**
 * Interface HCFormContract
 * @package InteractiveSolutions\HoneycombNewCore\Http\Controllers\Interfaces
 */
interface HCFormContract
{
    /**
     * @param bool $edit
     * @return array
     */
    public function createForm(bool $edit = false): array;
}
