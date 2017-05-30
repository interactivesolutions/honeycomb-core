<?php

namespace interactivesolutions\honeycombcore\http\controllers;

use DB;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use interactivesolutions\honeycombcore\errors\facades\HCLog;
use interactivesolutions\honeycombcore\http\controllers\traits\HCQueryMaking;

abstract class HCBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, HCQueryMaking;

    /**
     * Default records per page
     *
     * @var int
     */
    protected $recordsPerPage = 50;

    /**
     * @return \interactivesolutions\honeycombacl\app\models\HCUsers|null
     */
    protected function user ()
    {
        return auth()->user();
    }

    /**
     * Single function for unknown action error generation
     *
     * @param $action
     * @return mixed
     */
    protected function unknownAction(string $action)
    {
        return HCLog::info('CORE-0001', trans('HCTranslations::core.unknown_action' . $action), 501);
    }

    /**
     * adminView
     *
     * @return mixed
     */
    public function adminIndex()
    {
        return $this->unknownAction('adminIndex');
    }

    /**
     * Getting a list records for API call
     *
     * @return mixed
     */
    public function apiIndex()
    {
        return $this->createQuery()->get();
    }

    /**
     * Creating data list
     * @return mixed
     */
    public function apiIndexPaginate()
    {
        return $this->createQuery()->paginate($this->recordsPerPage)->appends($this->getRequestParametersRaw());
    }

    /**
     * Getting a list records for API call
     *
     * @return mixed
     */
    public function apiIndexSync()
    {
        return $this->unknownAction('apiIndexSync');
    }

    /**
     * Function which will be overridden by class which will use this one,
     *
     * Actual getting of a single record
     *
     * @param $id
     * @return mixed
     */
    public function apiShow(string $id)
    {
        return $this->unknownAction('apiShow');
    }

    //***************************************************** STORE START ***********************************************/

    /**
     * Function which will store new record
     *
     * @return mixed
     */
    public function apiStore()
    {
        DB::beginTransaction();

        try {
            $record = $this->__apiStore();
        } catch (\Exception $e) {
            DB::rollback();

            return HCLog::error('CORE-0002' . $e->getCode(), $e->getMessage());
        }

        DB::commit();

        return response($record);
    }

    /**
     * Function which will be overridden by class which will use this one,
     * to create new record
     *
     * @return mixed
     */
    protected function __apiStore()
    {
        return $this->unknownAction('__apiStore');
    }

    //***************************************************** CREATE END ************************************************/

    //***************************************************** UPDATE START **********************************************/

    /**
     * Function which will update record
     *
     * @param $id
     * @return mixed
     */
    public function apiUpdate(string $id)
    {
        DB::beginTransaction();

        try {
            $record = $this->__apiUpdate($id);
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
    protected function __apiUpdate(string $id)
    {
        return $this->unknownAction('__apiUpdate');
    }

    //***************************************************** UPDATE END ************************************************/

    //***************************************************** UPDATE STRICT START ***************************************/

    /**
     * Function which will update specific values of the record
     *
     * @param $id
     * @return mixed
     */
    public function apiUpdateStrict(string $id)
    {
        DB::beginTransaction();

        try {
            $record = $this->__apiUpdateStrict($id);
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
    protected function __apiUpdateStrict(string $id)
    {
        return $this->unknownAction('__apiUpdateStrict');
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
    protected function __apiDestroy(array $list)
    {
        return $this->unknownAction('__apiDestroy');
    }

    /**
     * Deletes items from database by given id's.
     *
     * @param $id
     * @return mixed
     */
    public function apiDestroy(string $id = null)
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
                $response = $this->__apiDestroy($list);
            else
                $response = $this->__apiForceDelete($list);
        } catch (\Exception $e) {
            DB::rollback();

            return HCLog::error('CORE-0005' . $e->getCode(), $e->getMessage());
        }

        DB::commit();

        if (isset($response))
            return $response;

        return ['success' => true, 'list' => $list];
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
    protected function __apiForceDelete(array $list)
    {
        return $this->unknownAction('__apiForceDelete');
    }

    /**
     * Force deletes items from database by given id's.
     *
     * @param string $id
     * @return mixed
     */
    public function apiForceDelete(string $id = null)
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
    public function apiRestore()
    {
        $toRestore = request()->input('list');

        if (sizeOf($toRestore) <= 0)
            return HCLog::info('CORE-0006', trans('HCTranslations::core.nothing_to_restore'));

        $response = $this->__apiRestore($toRestore);

        if (isset($response))
            return $response;

        return ['success' => true, 'list' => $toRestore];
    }

    /**
     * Function which will be overridden by class which will use this one,
     * this method will be used from list view to restore multiple records at a time
     *
     * @param array $list
     * @return mixed
     */
    protected function __apiRestore(array $list)
    {
        return $this->unknownAction('__apiRestore');
    }

    //****************************************** RESTORE END **********************************************************/

    //****************************************** MERGE START **********************************************************/

    /**
     * Function which will gather records which will be merged
     *
     * @return mixed
     */
    public function apiMergePrepare()
    {
        return $this->unknownAction('apiPrepareMerge');
    }

    /**
     * Function will can be used to merge multiple records into one
     *
     * @return mixed
     */
    public function apiMerge()
    {
        DB::beginTransaction();

        try {
            $record = $this->__apiMerge();
        } catch (\Exception $e) {
            DB::rollback();

            return HCLog::error('CORE-0007' . $e->getCode(), $e->getMessage());
        }

        DB::commit();

        return $record;
    }

    /**
     * Function which will be overridden by class which will use this one,
     * Here the actual merging will happen
     *
     * @return mixed
     */
    protected function __apiMerge()
    {
        $this->unknownAction('__apiMerge');
    }

    //******************************************** MERGE END **********************************************************/

    //****************************************** DUPLICATE START ******************************************************/
    /**
     * Duplicate function
     *
     * @param string $id
     * @return mixed
     */
    public function apiDuplicate(string $id)
    {
        DB::beginTransaction();

        try {
            $record = $this->__apiDuplicate($id);
        } catch (\Exception $e) {
            DB::rollback();

            return HCLog::error('CORE-0008' . $e->getCode(), $e->getMessage());
        }

        DB::commit();

        return $record;
    }

    /**
     * Function which will be overridden by class which will use this one,
     * Here the actual duplication will happen
     *
     * @param string $id
     * @return mixed
     */
    protected function __apiDuplicate(string $id)
    {
        return $this->unknownAction('__apiDuplicate');
    }

    //****************************************** DUPLICATE END ********************************************************/
}