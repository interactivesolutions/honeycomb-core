<?php

namespace interactivesolutions\honeycombcore\models;

class HCMultiLanguageModel extends HCUuidModel
{
    /**
     * Default translation class namespace
     *
     * @var
     */
    protected $translationsClass;

    /**
     * Translations
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        if (is_null($this->translationsClass))
            $this->translationsClass = get_class($this) . 'Translations';

        return $this->hasMany($this->translationsClass, 'record_id', 'id');
    }

    /**
     * Single translation only
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function translation()
    {
        if (is_null($this->translationsClass))
            $this->translationsClass = get_class($this) . 'Translations';

        return $this->hasOne($this->translationsClass, 'record_id', 'id');
    }

    /**
     * Update translations
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateTranslation(array $data)
    {
        $translations = $this->translations()->where([
            'record_id'     => $this->id,
            'language_code' => array_get($data, 'language_code'),
        ])->first();

        if (is_null($translations))
            $translations = $this->translations()->create($data);
        else
            $translations->update($data);

        return $translations;
    }

    /**
     * Update multiple translations at once
     *
     * @param array $data
     */
    public function updateTranslations(array $data = [])
    {
        foreach ($data as $translationsData) {
            $this->updateTranslation($translationsData);
        }
    }

    /**
     * Get translated id -> value names
     *
     * @param string $nameKey
     * @return mixed
     */
    public static function translatedList(string $nameKey = "name")
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

    /**
     * Getting translation value
     *
     * @param string $key
     * @return mixed
     */
    public function getTranslationValue(string $key = "name")
    {
        return get_translation_name(
            $key, app()->getLocale(), $this->translations
        );
    }
}