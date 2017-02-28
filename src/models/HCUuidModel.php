<?php namespace interactivesolutions\honeycombcore\models;

use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string)$model->generateNewId();
        });
    }

    /**
     * Get a new version 4 (random) UUID.
     *
     * @return \Ramsey\Uuid\UuidInterface
     */
    public function generateNewId()
    {
        if (isset($this->attributes['id'])) {
            return $this->attributes['id'];
        }

        return uuid4();
    }
}