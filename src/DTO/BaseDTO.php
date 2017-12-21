<?php
/**
 * @copyright C AgroTrade 2017
 *
 * This software is the property of AgroTrade
 * and is protected by copyright law – it is NOT freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * Contact AgroTrade:
 * E-mail: vytautas.rimeikis@gmail.com
 * http://www.agrotrade.lt
 */

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\DTO;


use JsonSerializable;

/**
 * Class BaseDTO
 * @package InteractiveSolutions\HoneycombCore\DTO
 */
abstract class BaseDTO implements JsonSerializable
{

    /**
     * @see JsonSerializable::jsonSerialize
     * @return array
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
