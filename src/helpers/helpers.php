<?php

if( ! function_exists('get_translation_name') ) {
    /**
     * Get translation name from
     *
     * @param string $key
     * @param string $lang
     * @param array $data
     * @return mixed
     */
    function get_translation_name(string $key, string $lang, array $data)
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

if( ! function_exists('uuid4') ) {
    /**
     * Generates uuid4 id
     *
     * @param bool $toString
     * @return \Ramsey\Uuid\UuidInterface|string
     */
    function uuid4(bool $toString = false)
    {
        $uuid4 = Ramsey\Uuid\Uuid::uuid4();

        if( $toString )
            $uuid4 = $uuid4->toString();

        return $uuid4;
    }
}

if( ! function_exists('pluralizeLT') ) {
    /**
     * Returns the correct lithuanian word form for given count.
     *
     * @param array $words [žodis, žodžiai, žodžių]
     * @param int $n
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    function pluralizeLT(array $words, int $n)
    {
        if( count($words) != 3 ) {
            throw new \InvalidArgumentException("Words array must contain 3 values!");
        }

        if( ! is_int($n) ) {
            throw new \InvalidArgumentException("n must be an integer!");
        }

        if( $n % 10 == 0 || floor($n / 10) == 1 ) {
            return $words[2];
        } elseif( $n % 10 == 1 ) {
            return $words[0];
        } else {
            return $words[1];
        }
    }
}

if( ! function_exists('isPackageEnabled') ) {
    /**
     * Check if package is registered at config/app.php file
     *
     * @param $provider
     * @return bool
     */
    function isPackageEnabled($provider)
    {
        $registeredProvidersArray = array_keys(app()->getLoadedProviders());

        return in_array($provider, $registeredProvidersArray);
    }
}

if( ! function_exists('settings') ) {
    //TODO create settings service
    function settings($key)
    {
        return $key;
    }
}

if( ! function_exists('sanitizeString') ) {

    /**
     * Returns a sanitized string, typically for URLs.
     * http://stackoverflow.com/questions/2668854/sanitizing-strings-to-make-them-url-and-filename-safe
     *
     * @param string $string - The string to sanitize.
     * @param bool $forceLowerCase - Force the string to lowercase?
     * @param bool $onlyLetter - If set to *true*, will remove all non-alphanumeric characters.
     *
     * @return mixed|string
     */
    function sanitizeString(string $string, bool $forceLowerCase = false, bool $onlyLetter = false)
    {
        $strip = [
            "~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", ",", "<", ".", ">", "/", "?",
        ];

        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = ($onlyLetter) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean;

        return ($forceLowerCase) ?
            (function_exists('mb_strtolower')) ?
                mb_strtolower($clean, 'UTF-8') :
                strtolower($clean) :
            $clean;
    }
}

if( ! function_exists('formManagerYesNo') ) {

    /**
     * @param string $type - checkBoxList | dropDownList
     * @param string $id
     * @param string $trans
     * @param int $required
     * @param int $requiredVisible
     * @param string $tabID
     * @param bool $showNo
     * @param bool $showYes
     * @return array
     */
    function formManagerYesNo(string $type = 'dropDownList', string $id, string $trans, int $required = 0, int $requiredVisible = 0, string $tabID = null, bool $showNo = true, bool $showYes = true)
    {
        $structure = [
            "tabID"           => $tabID,
            "type"            => $type,
            "fieldID"         => $id,
            "label"           => $trans,
            "required"        => $required,
            "requiredVisible" => $requiredVisible,
            "options"         => [],
        ];

        if( $showYes && $showNo ) {
            $structure['options'][] = ['id' => '1', 'label' => trans('HCTranslations::core.checkbox.yes')];
            $structure['options'][] = ['id' => '0', 'label' => trans('HCTranslations::core.checkbox.no')];
        } else {
            if( $showYes ) {
                $structure['label'] = " ";
                $structure['options'][] = ['id' => '1', 'label' => $trans];
            } else if( $showNo ) {
                $structure['label'] = " ";
                $structure['options'][] = ['id' => '0', 'label' => $trans];
            }
        }

        return $structure;
    }
}

if( ! function_exists('formManagerCheckBox') ) {

    /**
     * @param string $id
     * @param string $trans
     * @param int $required
     * @param int $requiredVisible
     * @param string|null $tabID
     * @return array
     */
    function formManagerCheckBox(string $id, string $trans, int $required = 0, int $requiredVisible = 0, string $tabID = null)
    {
        return [
            "type"            => "checkBoxList",
            "fieldID"         => $id,
            "label"           => ' ',
            "tabID"           => $tabID,
            "required"        => $required,
            "requiredVisible" => $requiredVisible,
            "options"         => [['id' => '1', 'label' => $trans]],
        ];
    }
}

if( ! function_exists('addAllOptionToDropDownList') ) {

    /**
     * Adding All options to Drop down list
     *
     * @param array $fieldData
     * @return array
     */
    function addAllOptionToDropDownList(array $fieldData)
    {
        array_unshift($fieldData['options'], ['id' => '', $fieldData['showNodes'][0] => trans('HCTranslations::core.all')]);

        return $fieldData;
    }
}

if( ! function_exists('createTranslationKey') ) {
    //TODO move to Translations package
    //TODO improve removal of ,/'?[][\ and etc...
    /**
     * From given string creates a translations string
     *
     * @param string $string
     * @return mixed
     */
    function createTranslationKey(string $string)
    {
        return str_replace(' ', '_', strtolower($string));
    }
}

if( ! function_exists('hcview') ) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array $data
     * @param  array $mergeData
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    function hcview($view = null, $data = [], $mergeData = [])
    {
        if( ! array_key_exists($view, config('hc.views')) ) {
            return view($view, $data, $mergeData);
        }

        return view(array_get(config('hc.views'), $view), $data, $mergeData);
    }
}

if( ! function_exists('checkActiveMenuItems') ) {

    /**
     * Check if menu item has active sub menu element
     *
     * @param array $item
     * @param $routeName
     * @return bool
     */
    function checkActiveMenuItems(array $item, $routeName)
    {
        if( $item['route'] == $routeName ) {
            return true;
        }

        if( array_key_exists('children', $item) ) {
            foreach ( $item['children'] as $child ) {
                $found = checkActiveMenuItems($child, $routeName);

                if( $found ) {
                    return true;
                }
            }
        }

        return false;
    }
}