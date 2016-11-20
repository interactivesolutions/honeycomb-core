<?php

namespace interaktyvussprendimai\ocv3core\models;

class HCMultiLanguageModel extends HCUuidModel
{
    /**
     * Default content class namespace
     *
     * @var
     */
    protected $hcTranslationsClass;

    /**
     * Translations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        if( is_null($this->hcTranslationsClass) )
            $this->hcTranslationsClass = get_class($this) . 'Translations';

        return $this->hasMany($this->hcTranslationsClass, 'record_id', 'id');
    }

    /**
     * Update translations
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateTranslations(array $data)
    {
        $translations = $this->translations()->where([
            'record_id'     => $this->id,
            'language_code' => array_get($data, 'language_code'),
        ])->first();

        if( is_null($translations) ) {
            $translations = $this->translations()->create($data);
        } else {
            $translations->update($data);
        }

        return $translations;
    }

    /**
     * Update multi language content
     *
     * @param array $data
     */
    public function updateMultipleContent(array $data)
    {
        foreach ($data as $contentData) {
            $this->updateTranslations($contentData);
        }
    }

    /**
     * Get translated id -> value names
     *
     * @param string $nameKey
     * @return mixed
     */
    public static function translatedList($nameKey = 'name')
    {
        return (new static())->with('translations')->get()->map(function ($item, $key) use ($nameKey) {
            return [
                'id'    => $item->id,
                'label' => get_translation_name(
                    $nameKey, app()->getLocale(), array_get($item, 'translations')
                ),
            ];
        });
    }

    public function getContentName($nameKey = 'name')
    {
        return get_translation_name(
            $nameKey, app()->getLocale(), $this->translations
        );
    }
}