<?php

namespace interactivesolutions\honeycombcore\http\controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use DB;
use HCLog;

abstract class HCBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Default records per page
     *
     * @var int
     */
    protected $recordsPerPage = 50;

    /**
     *  Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        return auth()->user();
    }

    /**
     * Single function for unknown action error generation
     *
     * @param $action
     * @return mixed
     */
    protected function unknownAction($action)
    {
        return HCLog::info('CORE-0001', trans('HCTranslations::core.unknown_action' . $action), 501);
    }

    /**
     * Returning Input data
     */
    protected function getInputData()
    {
        return request()->all();
    }

    /**
     * adminView
     *
     * @return mixed
     */
    public function adminView()
    {
        return $this->unknownAction('AdminView');
    }

    /**
     * Getting a list records for API call
     *
     * @return mixed
     */
    public function listData()
    {
        return $this->unknownAction('listData');
    }

    /**
     * Function which will be overridden by class which will use this one,
     *
     * Actual getting of a single record
     *
     * @param $id
     * @return mixed
     */
    public function getSingleRecord(string $id)
    {
        return $this->unknownAction('single');
    }

    /**
     * Function which will be overridden by class which will use this one,
     *
     * @return mixed
     */
    public function createQuery ()
    {
        return $this->unknownAction('createQuery');
    }

    //***************************************************** CREATE START **********************************************/

    /**
     * Function which will create new record
     *
     * @param array|null $data
     * @return mixed
     */
    public function create(array $data = null)
    {
        DB::beginTransaction();

        try {
            $record = $this->__create($data);
        } catch (\Exception $e) {
            DB::rollback();

            return HCLog::error('CORE-0002' . $e->getCode(), $e->getMessage());
        }

        DB::commit();

        return $record;
    }

    /**
     * Function which will be overridden by class which will use this one,
     * to create new record
     *
     * @param array|null $data
     * @return mixed
     */
    protected function __create(array $data = null)
    {
        return $this->unknownAction('__create');
    }

    //***************************************************** CREATE END ************************************************/

    //***************************************************** UPDATE START **********************************************/

    /**
     * Function which will update record
     *
     * @param $id
     * @return mixed
     */
    public function update(string $id)
    {
        DB::beginTransaction();

        try {
            $record = $this->__update($id);
        } catch (\Exception $e) {
            DB::rollback();

            return HCLog::error('CORE-0003' . $e->getCode(), $e->getMessage());
        }

        DB::commit();

        return $record;
    }

    /**
     * Function which will be overridden by class which will use this one,
     * to create new record
     *
     * @param $id
     * @return mixed
     */
    protected function __update(string $id)
    {
        return $this->unknownAction('Update');
    }

    //***************************************************** UPDATE END ************************************************/

    //***************************************************** UPDATE STRICT START ***************************************/

    /**
     * Function which will update specific values of the record
     *
     * @param $id
     * @return mixed
     */
    public function updateStrict(string $id)
    {
        DB::beginTransaction();

        try {
            $record = $this->__updateStrict($id);
        } catch (\Exception $e) {
            DB::rollback();

            return HCLog::error('CORE-0003' . $e->getCode(), $e->getMessage());
        }

        DB::commit();

        return $record;
    }

    /**
     * Function which will be overridden by class which will use this one,
     * to update specific values of the record
     *
     * @param $id
     * @return mixed
     */
    protected function __updateStrict(string $id)
    {
        return $this->unknownAction('UpdateStrict');
    }

    //***************************************************** UPDATE END ************************************************/

    //***************************************************** DELETE START **********************************************/

    /**
     * Function which will be overridden by class which will use this one,
     * Deletes items from database by given id's
     * Just need to set wanted Model name with list parameter
     * Also force deletes if it's set to true
     *
     * @param array $list
     * @return mixed
     */
    protected function __delete(array $list)
    {
        return $this->unknownAction('delete');
    }

    /**
     * Deletes items from database by given id's.
     *
     * @param $id
     * @return mixed
     */
    public function delete(string $id = null)
    {
        return $this->initializeDelete($id, true);
    }

    //***************************************************** DELETE END ************************************************/

    /**
     * Function which will actually call deletion function
     *
     * @param string $id
     * @param bool $soft
     * @return array
     * @internal param $callback
     */
    private function initializeDelete(string $id = null, bool $soft)
    {
        if ($id)
            $list = [$id];
        else
            $list = request()->input('list');

        if (sizeOf($list) <= 0)
            return HCLog::info('CORE-0004', trans('HCTranslations::core.nothing_to_delete'));

        DB::beginTransaction();

        try {
            if ($soft)
                $response = $this->__delete($list);
            else
                $response = $this->__forceDelete($list);
        } catch (\Exception $e) {
            DB::rollback();

            return HCLog::error('CORE-0005' . $e->getCode(), $e->getMessage());
        }

        DB::commit();

        if (isset($response))
            return $response;

        return ['success' => true];
    }

    //*********************************************** FORCE DELETE START **********************************************/

    /**
     * Function which will be overridden by class which will use this one,
     * Deletes items from database by given id's
     * Just need to set wanted Model name with list parameter
     * Also force deletes if it's set to true
     *
     * @param array $list
     * @return mixed
     */
    protected function __forceDelete(array $list)
    {
        return $this->unknownAction('delete');
    }

    /**
     * Force deletes items from database by given id's.
     *
     * @param string $id
     * @return mixed
     */
    public function forceDelete(string $id = null)
    {
        return $this->initializeDelete($id, false);
    }

    //********************************************** FORCE DELETE END *************************************************/

    //****************************************** RESTORE START ********************************************************/

    /**
     * Recovers items from database by given id's
     * Just need to set wanted Model name with list parameter
     *
     * @return mixed
     */
    public function restore()
    {
        $toRestore = request()->input('list');

        if (sizeOf($toRestore) <= 0)
            return HCLog::info('CORE-0006', trans('HCTranslations::core.nothing_to_restore'));

        $response = $this->__restore($toRestore);

        if (isset($response))
            return $response;

        return ["success" => true];
    }

    /**
     * Function which will be overridden by class which will use this one,
     * this method will be used from list view to restore multiple records at a time
     * This function will be called only if header as updateType:restore
     *
     * @param array $list
     * @return mixed
     */
    protected function __restore(array $list)
    {
        return $this->unknownAction('restore');
    }

    //****************************************** RESTORE END **********************************************************/

    //****************************************** MERGE START **********************************************************/

    protected function __merge()
    {
        try {
            return $this->merge();
        } catch (\Exception $e) {
            return HCLog::error('CORE-0007' . $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Merge prepare function
     *
     * @return mixed
     */
    private function merge()
    {
        $this->unknownAction('Merge');
    }

    //******************************************** MERGE END **********************************************************/

    //****************************************** DUPLICATE START ******************************************************/
    /**
     * Duplicate function
     *
     * @param string $id
     * @return mixed
     */
    public function duplicate(string $id)
    {
        return $this->unknownAction('');
    }

    /**
     * @param string $id
     * @return mixed
     */
    protected function __duplicate(string $id)
    {
        try {
            return $this->duplicate($id);
        } catch (\Exception $e) {
            return HCLog::error('CORE-0008' . $e->getCode(), $e->getMessage());
        }
    }

    //****************************************** DUPLICATE END ********************************************************/

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
                $query = $query->orderBy($sortBy, $sortOrder);

        return $query;
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
     * Creating data list
     * @return mixed
     */
    public function pageData ()
    {
        return $this->createQuery ()->paginate ($this->recordsPerPage)->appends($this->getRequestParametersRaw());
    }

    /**
     * Creating data list based on search
     * @return mixed
     */
    public function search ()
    {
        if (!request ('q'))
            return [];

        //TODO set limit to start search

        return $this->list ();
    }

    /**
     * Creating data list
     * @return mixed
     */
    public function list()
    {
        return $this->createQuery ()->get ();
    }
}