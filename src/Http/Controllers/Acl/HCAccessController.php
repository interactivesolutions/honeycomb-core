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

namespace InteractiveSolutions\HoneycombNewCore\Http\Controllers\Acl;

use Illuminate\View\View;
use InteractiveSolutions\HoneycombNewCore\Models\Acl\HCAclPermission;
use InteractiveSolutions\HoneycombNewCore\Models\Acl\Roles;
use InteractiveSolutions\HoneycombNewCore\Models\Acl\RolesPermissionsConnections;
use InteractiveSolutions\HoneycombNewCore\Http\Controllers\HCBaseController;

/**
 * Class HCAccessController
 * @package InteractiveSolutions\HoneycombNewCore\Http\Controllers\Acl
 */
class HCAccessController extends HCBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function adminIndex(): View
    {
        $config = [
            'title' => trans('HCACL::acl_access.page_title'),
            'roles' => $this->getRolesWithPermissions(),
            'permissions' => $this->getAllPermissions(),
            'updateUrl' => route('admin.api.acl.access.update'),
        ];

        return view('HCACL::admin.roles', ['config' => $config]);
    }

    /**
     * Update permissions
     * @return array
     * @throws \Exception
     */
    public function updateAccess(): array
    {
        $data = request()->only('role_id', 'permission_id');

        $superAdmin = Roles::superAdmin()->first();

        if ($data['role_id'] == $superAdmin->id) {
            return \HCLog::info('ACCESS-0001', trans('HCACL::validator.roles.cant_update_super'));
        }

        if (!auth()->user()->hasRole('admin') && !auth()->user()->isSuperAdmin()) {
            return \HCLog::info('ACCESS-0001', trans('HCACL::validator.roles.cant_update_roles'));
        }

        $record = RolesPermissionsConnections::where($data)->first();

        if (is_null($record)) {
            RolesPermissionsConnections::create($data);
            $message = 'created';
        } else {
            RolesPermissionsConnections::where($data)->delete();
            $message = 'deleted';
        }

        // clear permissions and menu items cache!
        cache()->forget('hc-admin-menu');
        cache()->forget('hc-permissions');

        return ['success' => true, 'message' => $message];
    }

    /**
     * Get roles with permissions
     *
     * @return string
     */
    public function getRolesWithPermissions(): string
    {
        $roles = Roles::with('permissions')
            ->notSuperAdmin()
            ->orderBy('name')
            ->get()->map(function(Roles $role) {
                return [
                    'id' => $role->id,
                    'role' => $role->name,
                    'slug' => $role->slug,
                    'permissions' => $role->permissions->pluck('id')->all(),
                ];
            });

        return json_encode($roles);
    }

    /**
     * Get roles and permissions
     *
     * @return string
     */
    private function getAllPermissions()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $permissions = HCAclPermission::select('id', 'name', 'action', 'created_at')->where('name', '!=',
                'admin.acl.roles')->get();
        } elseif ($user->isSuperAdmin()) {
            $permissions = HCAclPermission::select('id', 'name', 'action', 'created_at')->get();
        } else {
            $permissions = collect([]);
        }

        $permissions = $permissions->sortBy('name')->groupBy('name');

        return json_encode($permissions);
    }

}
