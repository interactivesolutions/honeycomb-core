<?php

namespace interactivesolutions\honeycombcore\http\controllers;

use DB;

class HCAdminController extends HCBaseController
{
    /**
     * Admin dashboard
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('HCCoreUI::admin.dashboard');
    }
}