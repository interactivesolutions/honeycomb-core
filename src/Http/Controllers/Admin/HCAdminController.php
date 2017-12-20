<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombNewCore\Http\Controllers\Admin;

use Illuminate\View\View;
use InteractiveSolutions\HoneycombNewCore\Http\Controllers\HCController;

/**
 * Class HCAdminController
 * @package InteractiveSolutions\HoneycombNewCore\Http\Controllers\Admin
 */
class HCAdminController extends HCController
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
