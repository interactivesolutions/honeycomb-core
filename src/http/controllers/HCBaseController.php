<?php

namespace interactivesolutions\honeycombcore\http\controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use HCLog;

abstract class HCBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Single function for unknown action error generation
     *
     * @param $action
     * @return mixed
     */
    protected function unknownAction($action)
    {
        return HCLog::info('CORE-0000', trans('core::core.unknown_action' . $action), 501);
    }

    /**
     * Returning Input data
     */
    protected function getInputData()
    {
        return Request::all();
    }
}