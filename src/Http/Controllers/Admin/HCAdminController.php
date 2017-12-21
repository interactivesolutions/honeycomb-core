<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Http\Controllers\Admin;

use Illuminate\View\View;
use InteractiveSolutions\HoneycombCore\Http\Controllers\HCBaseController;

/**
 * Class HCAdminController
 * @package InteractiveSolutions\HoneycombCore\Http\Controllers\Admin
 */
class HCAdminController extends HCBaseController
{
    /**
     * Admin dashboard
     *
     * @return View
     */
    public function index(): View
    {
        return view('HCCore::admin.dashboard');
    }
}
