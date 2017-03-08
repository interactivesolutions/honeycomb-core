<?php

namespace interactivesolutions\honeycombcore\http\controllers;

use Rap2hpoutre\LaravelLogViewer\LogViewerController;

class HCLogViewerController extends LogViewerController
{
    /**
     * Add checking for log route permissions
     *
     * @return array
     */
    public function index()
    {
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return parent::index();
        }

        //TODO translate
        return ['success' => false, 'message' => 'Permission denied'];
    }
}
