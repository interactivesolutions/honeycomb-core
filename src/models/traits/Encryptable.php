<?php

namespace interactivesolutions\honeycombcore\models\traits;

/**
 * Class Encryptable
 *
 * IMPORTANT: for successfully encrypting and decrypting recommended to use TEXT field type in database table
 *
 * @package interaktyvussprendimai\ocv3core\models\traits
 */
trait Encryptable
{
    /**
     * Encrypt field value before inserting into database
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if( $this->valid($key, $value) ) {
            $value = encrypt($value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Get decrypted field values after getting from database
     *
     * @param $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if( $this->valid($key, $value) ) {
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

        foreach ( $attributes as $key => $value ) {
            if( $this->valid($key, $value) ) {
                $attributes[$key] = decrypt($value);
            }
        }

        return $attributes;
    }

    /**
     * Check if key and value is valid and able to crypt or decrypt
     *
     * @param $key
     * @param $value
     * @return bool
     */
    protected function valid($key, $value)
    {
        return in_array($key, $this->encryptable) && ! is_null($value) && ! empty($value);
    }

}