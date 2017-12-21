<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombNewCore\Http\Controllers\Admin;

use Illuminate\View\View;
use InteractiveSolutions\HoneycombNewCore\Http\Controllers\HCBaseController;

/**
 * Class HCAdminController
 * @package InteractiveSolutions\HoneycombNewCore\Http\Controllers\Admin
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
        return view('HCNewCore::admin.dashboard');
    }
}
