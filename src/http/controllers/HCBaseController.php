<?php

namespace interactivesolutions\honeycombcore\http\controllers;

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
    public function getSingleRecord($id)
    {
        return $this->unknownAction('single');
    }

    /**
     * Function which will search JSON objects in the database and returns it,
     * mostly for search tag input field
     *
     * @return mixed
     */
    public function search()
    {
        return $this->unknownAction('search');
    }

    //***************************************************** CREATE START **********************************************/

    /**
     * Function which will create new record
     *
     * @param null $data
     * @return mixed
     */
    public function create($data = null)
    {
        DB::beginTransaction();

        try
        {
            $record = $this->__create($data);
        } catch (\Exception $e)
        {
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
     * @param null $data
     * @return mixed
     */
    protected function __create($data = null)
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
    public function update($id)
    {
        DB::beginTransaction();

        try
        {
            $record = $this->__update($id);
        } catch (\Exception $e)
        {
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
    protected function __update($id)
    {
        return $this->unknownAction('Update');
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
    public function delete($id = null)
    {
        return $this->initializeDelete($id, $this->__delete);
    }

    //***************************************************** DELETE END ************************************************/

    /**
     * Function which will actually call deletion function
     *
     * @param $id
     * @param $callback
     * @return array
     */
    private function initializeDelete($id, $callback)
    {
        if ($id)
            $list = [$id];
        else
            $list = request()->input('list');

        if (sizeOf($list) <= 0)
            return HCLog::info('CORE-0004', trans('HCTranslations::core.nothing_to_delete'));

        DB::beginTransaction();

        try
        {
            $response = $callback($list);
        } catch (\Exception $e)
        {
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
     * @param $id
     * @return mixed
     */
    public function forceDelete($id = null)
    {
        return $this->initializeDelete($id, $this->__forceDelete);
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
        try
        {
            return $this->merge();
        } catch (\Exception $e)
        {
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
     * @param $id
     * @return mixed
     */
    public function duplicate($id)
    {
        return $this->unknownAction('');
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function __duplicate($id)
    {
        try
        {
            return $this->duplicate($id);
        } catch (\Exception $e)
        {
            return HCLog::error('CORE-0008' . $e->getCode(), $e->getMessage());
        }
    }

    //****************************************** DUPLICATE END ********************************************************/

    /**
     * Get only valid request params for records filtering
     *
     * @param $availableFields - Model available fields to select
     * @return mixed
     */
    protected function getRequestParameters($availableFields)
    {
        $except = ['page', 'q', 'd', '_order'];

        $givenFields = request()->except($except);

        foreach ($givenFields as $fieldName => $value)
            if (!in_array($fieldName, $availableFields))
                array_forget($givenFields, $fieldName);

        return $givenFields;
    }

    /**
     * Check for deleted records option
     *
     * @param $list
     * @return mixed
     */
    protected function checkForDeleted($list)
    {
        if (request()->has('d') && request()->input('d') === '1')
            $list = $list->onlyTrashed();

        return $list;
    }
}