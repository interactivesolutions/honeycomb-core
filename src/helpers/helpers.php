<?php

if( ! function_exists('get_content_name') ) {
    /**
     * Get translation name from
     *
     * @param $key
     * @param $lang
     * @param $data
     * @return mixed
     */
    function get_translation_name($key, $lang, $data)
    {
        if( is_array($data) ) {
            $data = collect($data);
        }

        $item = $data->where('language_code', $lang)->first();

        if( is_null($item) ) {
            $name = array_get($data, '0.' . $key);
        } else {
            $name = array_get($item, $key);
        }

        if( is_null($name) ) {
            $name = trans('core::core.no_translation');
        }

        return $name;
    }
}