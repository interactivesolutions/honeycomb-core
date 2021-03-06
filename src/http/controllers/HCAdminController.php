<?php

namespace interactivesolutions\honeycombcore\http\controllers;

class HCAdminController extends HCBaseController
{
    /**
     * Admin dashboard
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return hcview('HCCoreUI::admin.dashboard');
    }
}