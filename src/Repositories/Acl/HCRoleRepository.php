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

namespace InteractiveSolutions\HoneycombCore\Repositories\Acl;

use InteractiveSolutions\HoneycombCore\Models\Acl\HCAclRole;
use InteractiveSolutions\HoneycombCore\Repositories\HCBaseRepository;

class HCRoleRepository extends HCBaseRepository
{
    /**
     *
     */
    const ROLE_SA = 'super-admin';

    /**
     *
     */
    const ROLE_PA = 'project-admin';

    /**
     *
     */
    const ROLE_U = 'user';

    /**
     * @return string
     */
    public function model(): string
    {
        return HCAclRole::class;
    }

    /**
     * @return string
     */
    public function getRoleSuperAdminId(): string
    {
        return $this->getIdBySlug(self::ROLE_SA);
    }

    /**
     * @return string
     */
    public function getRoleProjectAdminId(): string
    {
        return $this->getIdBySlug(self::ROLE_PA);
    }

    /**
     * @return string
     */
    public function getRoleUserId(): string
    {
        return $this->getIdBySlug(self::ROLE_U);
    }

    /**
     * @param string $roleId
     * @param string $permissionId
     * @return string
     */
    public function updateOrCreatePermission(string $roleId, string $permissionId): string
    {
        $rolePermission = [
            'role_id' => $roleId,
            'permission_id' => $permissionId,
        ];

        $permissionExists = \DB::table('hc_acl_role_permissions')->where($rolePermission)->exist();

        if ($permissionExists) {
            \DB::table('hc_acl_role_permissions')->where($rolePermission)->delete();
            $message = 'deleted';
        } else {
            \DB::table('hc_acl_role_permissions')->create($rolePermission);
            $message = 'deleted';
        }

        return $message;
    }

    /**
     * @param string $slug
     * @return string
     */
    private function getIdBySlug(string $slug): string
    {
        return $this->makeQuery()
            ->where('slug', '=', $slug)
            ->value('id');
    }
}
