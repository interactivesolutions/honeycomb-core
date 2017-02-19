<?php

namespace interactivesolutions\honeycombcore\http\controllers;

use Cache;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use interactivesolutions\honeycombcore\errors\facades\HCLog;

class HCFormManagerController extends Controller
{
    /**
     * Get form structure as json object
     *
     * @param null $key
     * @return mixed
     */
    public function getFormStructure($key)
    {
        return $this->getForm($key);
    }

    /**
     * Get form structure as a json string
     *
     * @param null $key
     * @return mixed
     */
    public function getFormStructureString($key)
    {
        return json_encode($this->getForm($key));
    }

    /**
     * Get form from cache or get it from class and than store it to cache
     *
     * @param $key
     * @return mixed
     */
    private function getForm($key)
    {
        if (!Cache::has('hc-forms'))
            Artisan::call('hc:forms');

        $list = Cache::get('hc-forms');

        $new = substr ($key, 0, -4);
        $edit = substr($key, 0, -5);

        if (isset($list[$new]))
        {
            $form = new $list[$new]();
            return $form->createForm();
        }

        if (isset($list[$edit]))
        {
            $form = new $list[$edit]();
            return $form->createForm(true);
        }

        return HCLog::error('CORE-0010', 'Form not found: ' . $key);
    }

}