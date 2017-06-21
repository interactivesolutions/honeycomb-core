<?php

namespace interactivesolutions\honeycombcore\models;


class HCTranslationsModel extends HCModel
{
    /**
     * Disabling timestamps
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot ()
    {
        parent::boot ();

        static::creating( function ($model) {
            $model->setCreatedAt($model->freshTimestamp());
        });
    }
}