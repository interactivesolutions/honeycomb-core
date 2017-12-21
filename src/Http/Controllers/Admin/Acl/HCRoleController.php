<?php
/**
 * @copyright 2017 interactivesolutions
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Contact InteractiveSolutions:
 * E-mail: hello@interactivesolutions.lt
 * http://www.interactivesolutions.lt
 */

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombNewCore\Http\Controllers\Admin\Acl;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use InteractiveSolutions\HoneycombNewCore\Helpers\HCFrontendResponse;
use InteractiveSolutions\HoneycombNewCore\Http\Controllers\HCBaseController;
use InteractiveSolutions\HoneycombNewCore\Http\Requests\HCRoleRequest;
use InteractiveSolutions\HoneycombNewCore\Services\Acl\HCRoleService;

/**
 * Class HCRoleController
 * @package InteractiveSolutions\HoneycombNewCore\Http\Controllers\Admin\Acl
 */
class HCRoleController extends HCBaseController
{
    /**
     * @var HCRoleService
     */
    private $service;
    /**
     * @var HCFrontendResponse
     */
    private $response;

    /**
     * HCRoleController constructor.
     * @param HCRoleService $service
     * @param HCFrontendResponse $response
     */
    public function __construct(HCRoleService $service, HCFrontendResponse $response)
    {
        $this->service = $service;
        $this->response = $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $config = [
            'title' => trans('HCNewCore::acl_access.page_title'),
            'roles' => json_encode($this->service->getRolesWithPermissions()),
            'permissions' => json_encode($this->service->getAllPermissions()),
            'updateUrl' => route('admin.api.acl.access.update'),
        ];

        return view('HCNewCore::admin.roles', ['config' => $config]);
    }

    /**
     * Update role permissions
     *
     * @param HCRoleRequest $request
     * @return JsonResponse
     */
    public function updatePermissions(HCRoleRequest $request): JsonResponse
    {
        try {
            $message = $this->service->updateRolePermissions(
                $request->input('role_id'),
                $request->input('permission_id')
            );
        } catch (\Exception $exception) {
            return $this->response->error($exception->getMessage());
        }

        return $this->response->success($message);
    }
}
