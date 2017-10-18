<?php
declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Http\Controllers;

use Cache;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use InteractiveSolutions\HoneycombCore\Errors\Facades\HCLog;

/**
 * Class HCFormManagerController
 * @package InteractiveSolutions\HoneycombCore\http\controllers
 */
class HCFormManagerController extends Controller
{
    /**
     * Get form structure as json object
     *
     * @param string $key
     * @return mixed
     */
    public function getFormStructure(string $key)
    {
        return $this->getForm($key);
    }

    /**
     * Get form structure as a json string
     *
     * @param string $key
     * @return mixed
     */
    public function getFormStructureString(string $key)
    {
        return json_encode($this->getForm($key));
    }

    /**
     * Get form from cache or get it from class and than store it to cache
     *
     * @param string $key
     * @return mixed
     */
    private function getForm(string $key)
    {
        if (!Cache::has('hc-forms')) {
            Artisan::call('hc:forms');
        }

        $list = Cache::get('hc-forms');

        $new = substr($key, 0, -4);
        $edit = substr($key, 0, -5);

        if (isset($list[$new])) {
            $form = new $list[$new]();

            return $form->createForm();
        }

        if (isset($list[$edit])) {
            $form = new $list[$edit]();

            return $form->createForm(true);
        }

        return HCLog::error('CORE-0010', 'Form not found: ' . $key);
    }

}