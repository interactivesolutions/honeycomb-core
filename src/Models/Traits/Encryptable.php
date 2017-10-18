<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Models\Traits;

/**
 * Trait Encryptable
 * IMPORTANT: for successfully encrypting and decrypting recommended to use TEXT field type in database table
 * @package InteractiveSolutions\HoneycombCore\Models\Traits
 */
trait Encryptable
{
    /**
     * Encrypt field value before inserting into database
     *
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function setAttribute(string $key, string $value)
    {
        if ($this->valid($key, $value)) {
            $value = encrypt($value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Get decrypted field values after getting from database
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        $value = parent::getAttribute($key);

        if ($this->valid($key, $value)) {
            return decrypt($value);
        }

        return $value;
    }

    /**
     * Decrypt given attributes
     *
     * @return mixed
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach ($attributes as $key => $value) {
            if ($this->valid($key, $value)) {
                $attributes[$key] = decrypt($value);
            }
        }

        return $attributes;
    }

    /**
     * Check if key and value is valid and able to crypt or decrypt
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    protected function valid(string $key, string $value)
    {
        return in_array($key, $this->encryptable) && !is_null($value) && !empty($value);
    }

}