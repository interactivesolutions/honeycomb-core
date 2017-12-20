<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombNewCore\DTO;

/**
 * Class HCBaseDTO
 * @package InteractiveSolutions\HoneycombNewCore\DTO
 */
abstract class HCBaseDTO implements \JsonSerializable
{
    /**
     * @return array|mixed
     */
    final public function jsonSerialize()
    {
        return $this->jsonData();
    }

    /**
     * @return array
     */
    abstract protected function jsonData(): array;
}
