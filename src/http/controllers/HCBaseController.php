<?php

namespace interactivesolutions\honeycombcore\http\controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use DB;
use OCLog;
use Request;

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
     * Single function for unknown action error generation
     *
     * @param $action
     * @return mixed
     */
    protected function unknownAction($action)
    {
        return HCLog::info('CORE-000', trans('core::core.unknown_action' . $action), 501);
    }

    /**
     * Returning Input data
     */
    protected function getInputData()
    {
        return Request::all();
    }

    /**
     * Returning admin view
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
        return $this->unknownAction('');
    }

    /**
     * Function which will be overridden by class which will use this one,
     *
     * Actual getting of a single record
     *
     * @param $id
     * @return mixed
     */
    public function single($id)
    {
        return $this->unknownAction('Single');
    }

    /**
     * Function which will be overridden by class which will use this one,
     * here the database will be searched and JSON objects will be returned,
     * mostly for search tag input field
     *
     * @return mixed
     */
    public function search()
    {
        return $this->unknownAction('Search');
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

        try {
            $record = $this->create($data);
        } catch (\Exception $e) {
            DB::rollback();

            return OCLog::error('CORE-000' . $e->getCode(), $e->getMessage());
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
     * Function which will be overridden by class which will use this one,
     * to create new record
     *
     * @param $id
     * @return mixed
     */
    private function update($id)
    {
        DB::beginTransaction();

        try {
            $record = $this->__update($id);
        } catch (\Exception $e) {
            DB::rollback();

            return OCLog::error('CORE-000' . $e->getCode(), $e->getMessage());
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
    public function __update($id)
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
     * @param $toDelete
     * @return mixed
     */
    protected function __delete($toDelete)
    {
        return $this->unknownAction('delete');
    }

    /**
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
            $toDelete = [$id];
        else
            $toDelete = request()->input('list');

        if (sizeOf($toDelete) <= 0)
            return OCLog::info('CORE-000', trans('core::core.nothing_to_delete'));

        DB::beginTransaction();

        try {
            $response = $callback($toDelete);
        } catch (\Exception $e) {
            DB::rollback();

            return OCLog::error('CORE-000' . $e->getCode(), $e->getMessage());
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
     * @param $toForceDelete
     * @return mixed
     */
    protected function __forceDelete($toForceDelete)
    {
        return $this->unknownAction('delete');
    }

    /**
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
     * Function which will be overridden by class which will use this one,
     * Recovers items from database by given id's
     * Just need to set wanted Model name with list parameter
     * 
     * @return mixed
     */
    public function restore()
    {
        $toRestore = request()->input('list');

        if( sizeOf($toRestore) <= 0 ) {
            return OCLog::info('CORE-000', trans('core::core.nothing_to_restore'));
        }

        $response = $this->__restore($toRestore);

        if( isset($response) ) {
            return $response;
        }

        return ["success" => true];
    }

    /**
     * Function which will be overridden by class which will use this one,
     * this method will be used from list view to restore multiple records at a time
     * This function will be called only if header as updateType:restore
     *
     * @param $toRestore
     * @return mixed
     */
    protected function __restore($toRestore)
    {
        return $this->unknownAction('restore');
    }

    //****************************************** RESTORE END **********************************************************/

    //****************************************** MERGE START **********************************************************/

    protected function __merge()
    {
        try {
            return $this->merge();
        } catch ( \Exception $e ) {
            return OCLog::error('CORE-0005-' . $e->getCode(), $e->getMessage());
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
        try {
            return $this->duplicate($id);
        } catch ( \Exception $e ) {
            return OCLog::error('CORE-0005-' . $e->getCode(), $e->getMessage());
        }
    }
}