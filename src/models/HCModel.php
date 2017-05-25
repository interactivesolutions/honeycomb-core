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
     * @param bool $join
     * @return array
     */
    public static function getFillableFields(bool $join = false)
    {
        $list = with(new static)->getFillable();

        if ($join)
        {
            //to use default sort_by command
            array_push($list, 'created_at');
            foreach ($list as &$value)
                $value = self::getTableName() . '.' . $value;
        }


        return $list;
    }

    /**
     * Get table enum field list with translations or not
     *
     * @param string $field
     * @param null|string $labelKey
     * @param string $translationCode
     * @return array
     */
    public static function getTableEnumList(string $field, string $labelKey = null, string $translationCode = null)
    {
        $type = DB::select(
            DB::raw('SHOW COLUMNS FROM ' . self::getTableName() . ' WHERE Field = "' . $field . '"')
        )[0]->Type;

        preg_match('/^enum\((.*)\)$/', $type, $matches);

        $values = [];

        foreach (explode(',', $matches[1]) as $value) {
            $value = trim($value, "'");

            if (is_null($labelKey)) {
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