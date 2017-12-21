<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Contracts;

use Illuminate\Database\Eloquent\Builder;


/**
 * Interface RepositoryContract
 * @package InteractiveSolutions\HoneycombCore\Contracts
 */
interface HCRepositoryContract
{
    /**
     * @return string
     */
    public function model(): string;

    /**
     * @return Builder
     */
    public function makeQuery(): Builder;
}
