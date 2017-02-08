<?php namespace interactivesolutions\honeycombcore\models;

use DB;
use Illuminate\Database\Eloquent\Model;

class HCModel extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

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