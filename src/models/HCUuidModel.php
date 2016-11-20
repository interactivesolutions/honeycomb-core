<?php namespace interaktyvussprendimai\ocv3core\models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HCUuidModel extends Model
{

    use SoftDeletes;

    /**
     * Soft delete database field.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

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
     * Function which gets table name
     *
     * @return mixed
     */
    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    /**
     * Function which gets fillable fields array
     *
     * @return array
     */
    public static function getFillableFields()
    {
        return with(new static)->getFillable();
    }

    /**
     * Get a new version 4 (random) UUID.
     *
     * @return \Ramsey\Uuid\UuidInterface
     */
    public function generateNewId()
    {
        if( isset($this->attributes['id']) ) {
            return $this->attributes['id'];
        }

        return uuid4();
    }

    /**
     * Get table enum field list with translations or not
     *
     * @param $field
     * @param $translationCode
     * @param null $labelKey
     * @return array
     */
    public static function getTableEnumList($field, $labelKey = null, $translationCode = null)
    {
        $type = DB::select(
            DB::raw('SHOW COLUMNS FROM ' . self::getTableName() . ' WHERE Field = "' . $field . '"')
        )[0]->Type;

        preg_match('/^enum\((.*)\)$/', $type, $matches);

        $values = [];

        foreach ( explode(',', $matches[1]) as $value ) {
            $value = trim($value, "'");

            if( is_null($labelKey) ) {
                $values[] = $value;
            } else {
                $values[] = [
                    'id'      => $value,
                    $labelKey => trans($translationCode . $value),
                ];
            }
        }

        return $values;
    }
}