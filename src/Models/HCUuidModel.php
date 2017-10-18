<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Models;

use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class HCUuidModel
 * @package InteractiveSolutions\HoneycombCore\Models
 */
class HCUuidModel extends HCModel
{

    use SoftDeletes;

    /**
     * Soft delete database field.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * Attach to the 'creating' Model Event to provide a UUID
         * for the `id` field (provided by $model->getKeyName())
         */
        static::creating(function($model) {
            if (!isset($model->attributes['id'])) {
                $model->attributes['id'] = uuid4();
            }

            $model->{$model->getKeyName()} = $model->attributes['id'];
        });
    }
}