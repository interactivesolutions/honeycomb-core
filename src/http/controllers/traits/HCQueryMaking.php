<?php

namespace interactivesolutions\honeycombcore\http\controllers\traits;

use Illuminate\Database\Eloquent\Builder;

trait HCQueryMaking
{
    /**
     * Function which will be overridden by class which will use this one,
     *
     * @return Builder | mixed
     */
    protected function createQuery()
    {
        return $this->unknownAction('createQuery');
    }

    /**
     * Keys which can be updated by strict method
     *
     * @var array
     */
    protected $strictUpdateKeys = [];

    /**
     * Minimum search input length
     *
     * @var int
     */
    protected $minimumSearchInputLength = 0;

    /**
     * Get only valid request params for records filtering
     *
     * @param Builder $query
     * @param array $availableFields - Model available fields to select
     * @return mixed
     */
    protected function getRequestParameters(Builder $query, array $availableFields)
    {
        $givenFields = $this->getRequestParametersRaw();

        foreach ($givenFields as $fieldName => $value) {

            $from = substr($fieldName, 0, -5);
            $to = substr($fieldName, 0, -3);

            if (in_array($from, $availableFields) && $value != '')
                $query->where($from, '>=', $value);

            if (in_array($to, $availableFields) && $value != '')
                $query->where($to, '<=', $value);

            if (in_array($fieldName, $availableFields))
                if (is_array($value))
                    $query->whereIn($fieldName, $value);
                else
                    $query->where($fieldName, '=', $value);
        }

        return $query;
    }

    /**
     * Gathering all request parameters
     *
     * @return array
     */
    protected function getRequestParametersRaw()
    {
        return request()->except(['page', 'q', 'deleted', 'sort_by', 'sort_order']);
    }

    /**
     * Gathering all allowed request parameters for strict update
     *
     * @return array
     */
    protected function getStrictRequestParameters()
    {
        $data = [];

        foreach ($this->strictUpdateKeys as $value)
            if (request()->has($value))
                $data[$value] = request($value);

        return $data;
    }

    /**
     * Ordering content
     *
     * @param Builder $query
     * @param array $availableFields
     * @return mixed
     */
    protected function orderData(Builder $query, array $availableFields)
    {
        $sortBy = request()->input('sort_by');
        $sortOrder = request()->input('sort_order');

        if (in_array($sortBy, $availableFields))
            if (in_array(strtolower($sortOrder), ['asc', 'desc']))
                return $query->orderBy($sortBy, $sortOrder);

        return $query->orderBy('created_at', 'desc');
    }


    /**
     * Creating data list based on search
     * @param Builder $query
     * @return mixed
     */
    protected function search(Builder $query)
    {
        $q = request('q');

        if (!$q || strlen($q) < $this->minimumSearchInputLength)
            return $query;

        return $this->searchQuery($query, $q);
    }

    /**
     * Returning Input data
     */
    protected function getInputData()
    {
        return request()->all();
    }

    /**
     * Check for deleted records option
     *
     * @param Builder $query
     * @return mixed
     */
    protected function checkForDeleted(Builder $query)
    {
        if (request()->has('deleted') && request()->input('deleted') === '1')
            $query = $query->onlyTrashed();

        return $query;
    }

    /**
     * Adding search query to request, this function should be overwritten by child class
     *
     * @param Builder $query
     * @param string $phrase
     * @return Builder
     */
    protected function searchQuery(Builder $query, string $phrase)
    {
        return $query;
    }
}