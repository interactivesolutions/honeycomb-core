<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\DTO;

/**
 * Class HCBaseDTO
 * @package InteractiveSolutions\HoneycombCore\DTO
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
