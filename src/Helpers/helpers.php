<?php

if (!function_exists('get_translation_name')) {
    /**
     * Get translation name from
     *
     * @param string $key
     * @param string $lang
     * @param array $data
     * @param null $customNotFoundText
     * @return mixed
     */
    function getTranslationName(string $key, string $lang, array $data, $customNotFoundText = null)
    {
        if (is_array($data)) {
            $data = collect($data);
        }

        $item = $data->where('language_code', $lang)->first();

        if (is_null($item)) {
            $name = array_get($data, '0.' . $key);
        } else {
            $name = array_get($item, $key);
        }

        if (is_null($name)) {
            if (is_null($customNotFoundText)) {
                $name = trans('HCTranslations::core.no_translation');
            } else {
                $name = $customNotFoundText;
            }
        }

        return $name;
    }
}


if (!function_exists('uuid4')) {
    /**
     * Generates uuid4 id
     *
     * @param bool $toString
     * @return \Ramsey\Uuid\UuidInterface|string
     */
    function uuid4(bool $toString = false)
    {
        $uuid4 = Ramsey\Uuid\Uuid::uuid4();

        if ($toString) {
            $uuid4 = $uuid4->toString();
        }

        return $uuid4;
    }
}


if (!function_exists('pluralizeLT')) {
    /**
     * Returns the correct lithuanian word form for given count.
     *
     * @param array $words [žodis, žodžiai, žodžių]
     * @param int $number
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    function pluralizeLT(array $words, int $number): string
    {
        if (count($words) != 3) {
            throw new \InvalidArgumentException('Words array must contain 3 values!');
        }

        if (!is_int($number)) {
            throw new \InvalidArgumentException('number must be an integer!');
        }

        if ($number % 10 == 0 || floor($number / 10) == 1) {
            return $words[2];
        } elseif ($number % 10 == 1) {
            return $words[0];
        } else {
            return $words[1];
        }
    }
}


if (!function_exists('isPackageEnabled')) {
    /**
     * Check if package is registered at config/app.php file
     *
     * @param $provider
     * @return bool
     */
    function isPackageEnabled($provider)
    {
        $registeredProviders = array_keys(app()->getLoadedProviders());

        return in_array($provider, $registeredProviders);
    }
}


if (!function_exists('settings')) {
    //TODO create settings service
    function settings($key)
    {
        return $key;
    }
}


if (!function_exists('sanitizeString')) {

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
            '~',
            '`',
            '!',
            '@',
            '#',
            '$',
            '%',
            '^',
            '&',
            '*',
            '(',
            ')',
            '_',
            '=',
            '+',
            '[',
            '{',
            ']',
            '}',
            '\\',
            '|',
            ';',
            ':',
            '"',
            '\'',
            '&#8216;',
            '&#8217;',
            '&#8220;',
            '&#8221;',
            '&#8211;',
            '&#8212;',
            'â€”',
            'â€“',
            ',',
            '<',
            '.',
            '>',
            '/',
            '?',
        ];

        $clean = trim(str_replace($strip, '', strip_tags($string)));
        $clean = preg_replace('/\s+/', '-', $clean);
        $clean = ($onlyLetter) ? preg_replace('/[^a-zA-Z0-9]/', '', $clean) : $clean;

        return ($forceLowerCase) ?
            (function_exists('mb_strtolower')) ?
                mb_strtolower($clean, 'UTF-8') :
                strtolower($clean) :
            $clean;
    }
}


if (!function_exists('addAllOptionToDropDownList')) {

    /**
     * Adding All options to Drop down list
     *
     * @param array $fieldData
     * @return array
     */
    function addAllOptionToDropDownList(array $fieldData)
    {
        array_unshift(
            $fieldData['options'],
            ['id' => '', $fieldData['showNodes'][0] => trans('HCTranslations::core.all')]
        );

        return $fieldData;
    }
}


if (!function_exists('createTranslationKey')) {
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


if (!function_exists('checkActiveMenuItems')) {

    /**
     * Check if menu item has active sub menu element
     *
     * @param array $item
     * @param string $routeName
     * @return bool
     */
    function checkActiveMenuItems(array $item, string $routeName): bool
    {
        if ($item['route'] == $routeName) {
            return true;
        }

        if (array_key_exists('children', $item)) {
            foreach ($item['children'] as $child) {
                $found = checkActiveMenuItems($child, $routeName);

                if ($found) {
                    return true;
                }
            }
        }

        return false;
    }
}


if (!function_exists('stringToDouble')) {

    /**
     * Formatting 0,15 to 0.15 and etc
     *
     * @param string $value
     * @return mixed
     */
    function stringToDouble($value)
    {
        if (!$value) {
            $value = '0.0';
        }

        return str_replace(',', '.', $value);
    }
}


if (!function_exists('removeRecordsWithNoTranslation')) {

    /**
     * Removing records from array with no translation
     * used by Front-End
     *
     * @param array $list
     * @return array
     */
    function removeRecordsWithNoTranslation(array $list): array
    {
        $contentList = [];

        foreach ($list as $item) {
            if ($item['translation'] !== null) {
                array_push($contentList, $item);
            }
        }

        return $contentList;
    }
}


if (!function_exists('formManagerSeo')) {

    /**
     * Adding seo fields (title, description, keywords
     * used by Form-Managers
     *
     * @param array $list
     * @param bool $multiLanguage
     */
    function formManagerSeo(array &$list, bool $multiLanguage = true): void
    {
        $list['structure'] = array_merge(
            $list['structure'],
            [
                [
                    'type' => 'singleLine',
                    'fieldID' => 'translations.seo_title',
                    'label' => trans('HCTranslations::core.seo_title'),
                    'tabID' => trans('HCTranslations::core.seo'),
                    'multiLanguage' => $multiLanguage,
                ],
                [
                    'type' => 'textArea',
                    'fieldID' => 'translations.seo_description',
                    'label' => trans('HCTranslations::core.seo_description'),
                    'tabID' => trans('HCTranslations::core.seo'),
                    'multiLanguage' => $multiLanguage,
                    'rows' => 5,
                ],
                [
                    'type' => 'singleLine',
                    'fieldID' => 'translations.seo_keywords',
                    'label' => trans('HCTranslations::core.seo_keywords'),
                    'tabID' => trans('HCTranslations::core.seo'),
                    'multiLanguage' => $multiLanguage,
                ],
            ]
        );
    }
}
