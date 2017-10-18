<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Http\Controllers;

use Illuminate\View\View;

/**
 * Class HCAdminController
 * @package InteractiveSolutions\HoneycombCore\Http\Controllers
 */
class HCAdminController extends HCBaseController
{
    /**
     * Admin dashboard
     *
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function index(): View
    {
        return hcview('HCCoreUI::admin.dashboard');
    }
}