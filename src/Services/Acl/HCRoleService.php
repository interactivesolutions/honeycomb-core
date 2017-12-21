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

namespace InteractiveSolutions\HoneycombNewCore\Services\Acl;

use Illuminate\Support\Collection;
use InteractiveSolutions\HoneycombNewCore\Models\Acl\HCAclRole;
use InteractiveSolutions\HoneycombNewCore\Repositories\Acl\HCPermissionRepository;
use InteractiveSolutions\HoneycombNewCore\Repositories\Acl\HCRoleRepository;

/**
 * Class HCRoleService
 * @package InteractiveSolutions\HoneycombNewCore\Services\Acl
 */
class HCRoleService
{
    /**
     * @var HCRoleRepository
     */
    private $roleRepository;
    /**
     * @var HCPermissionRepository
     */
    private $permissionRepository;

    /**
     * HCRoleService constructor.
     * @param HCRoleRepository $roleRepository
     * @param HCPermissionRepository $permissionRepository
     */
    public function __construct(HCRoleRepository $roleRepository, HCPermissionRepository $permissionRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Get roles with permissions
     *
     * @return Collection
     */
    public function getRolesWithPermissions(): Collection
    {
        $roles = $this->roleRepository->makeQuery()
            ->with('permissions')
            ->notSuperAdmin()
            ->orderBy('name')
            ->get()->map(function (HCAclRole $role) {
                return [
                    'id' => $role->id,
                    'role' => $role->name,
                    'slug' => $role->slug,
                    'permissions' => $role->permissions->pluck('id')->all(),
                ];
            });

        return $roles;
    }

    /**
     * Get roles and permissions
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection
    {
        $user = auth()->user();

        if ($user->hasRole($this->roleRepository::ROLE_PA)) {
            $permissions = $this->permissionRepository->makeQuery()
                ->select('id', 'name', 'action', 'created_at')
                ->where('name', '!=', 'admin.acl.roles')
                ->get();
        } elseif ($user->isSuperAdmin()) {
            $permissions = $this->permissionRepository->makeQuery()
                ->select('id', 'name', 'action', 'created_at')->get();
        } else {
            $permissions = collect([]);
        }

        $permissions = $permissions->sortBy('name')->groupBy('name');

        return $permissions;
    }

    /**
     * @param string $roleId
     * @param string $permissionId
     * @return string
     * @throws \Exception
     */
    public function updateRolePermissions(string $roleId, string $permissionId): string
    {
        if ($roleId == $this->roleRepository->getRoleSuperAdminId()) {
            throw new \Exception(trans('HCNewCore::validator.roles.cant_update_super'));
        }

        if (!auth()->user()->hasRole([$this->roleRepository::ROLE_SA, $this->roleRepository::ROLE_PA])) {
            throw new \Exception(trans('HCNewCore::validator.roles.cant_update_roles'));
        }

        $message = $this->roleRepository->updateOrCreatePermission($roleId, $permissionId);

        // clear permissions and menu items cache!
        cache()->forget('hc-admin-menu');
        cache()->forget('hc-permissions');

        return $message;
    }
}
