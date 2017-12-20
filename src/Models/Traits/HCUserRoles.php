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

namespace InteractiveSolutions\HoneycombNewCore\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use InteractiveSolutions\HoneycombNewCore\Models\Acl\HCPermission;
use InteractiveSolutions\HoneycombNewCore\Models\Acl\HCRole;
use InteractiveSolutions\HoneycombNewCore\Models\Acl\HCRolesUsersConnections;


trait HCUserRoles
{
    /**
     * A user may have multiple roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            HCRole::class,
            HCRolesUsersConnections::getTableName(),
            'user_id',
            'role_id'
        )->withTimestamps();
    }

    /**
     * Assign the given role to the user.
     *
     * @param string $role
     * @return mixed
     */
    public function assignRoleBySlug(string $role)
    {
        return $this->roles()->save(
            HCRole::where('slug', $role)->firstOrFail()
        );
    }

    /**
     * Create roles for user
     *
     * @param array $roles - role ids
     */
    public function assignRoles(array $roles): void
    {
        if (!empty($roles)) {
            $this->roles()->sync($roles);
        }
    }

    /**
     * Determine if the user has the given role.
     *
     * @param  mixed $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }

        foreach ($role as $r) {
            if (is_string($r)) {
                if ($this->hasRole($r)) {
                    return true;
                }
            } else {
                if ($this->hasRole($r->slug)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine if the user may perform the given permission.
     *
     * @param HCPermission $permission
     * @return bool
     */
    public function hasPermission(HCPermission $permission): bool
    {
        return $this->hasRole($permission->roles);
    }

    /**
     * Checking if user if super admin
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Checking if user if super admin
     *
     * @return bool
     */
    public function isNotSuperAdmin(): bool
    {
        return !$this->isSuperAdmin();
    }

    /**
     * Get all roles as string
     *
     * @return mixed
     */
    public function roleNames()
    {
        return $this->roles()->pluck('name')->implode(', ');
    }

    /**
     * Get all roles as slugs array
     *
     * @return mixed
     */
    public function currentRolesArray()
    {
        return $this->roles()->pluck('slug');
    }

    /**
     * Attach user role to user
     */
    public function roleUser(): void
    {
        HCRolesUsersConnections::create([
            'user_id' => $this->id,
            'role_id' => HCRole::where('slug', 'user')->firstOrFail()->id,
        ]);
    }

    /**
     * Attach super admin role to user
     */
    public function roleSuperAdmin(): void
    {
        HCRolesUsersConnections::create([
            'user_id' => $this->id,
            'role_id' => HCRole::where('slug', 'super-admin')->firstOrFail()->id,
        ]);
    }

    /**
     * Attach project admin role to user
     */
    public function roleProjectAdmin(): void
    {
        HCRolesUsersConnections::create([
            'user_id' => $this->id,
            'role_id' => HCRole::where('slug', 'project-admin')->firstOrFail()->id,
        ]);
    }

    /**
     * Attach editor role to user
     */
    public function roleEditor(): void
    {
        HCRolesUsersConnections::create([
            'user_id' => $this->id,
            'role_id' => HCRole::where('slug', 'editor')->firstOrFail()->id,
        ]);
    }

    /**
     * Attach author role to user
     */
    public function roleAuthor(): void
    {
        HCRolesUsersConnections::create([
            'user_id' => $this->id,
            'role_id' => HCRole::where('slug', 'author')->firstOrFail()->id,
        ]);
    }

    /**
     * Attach contributor role to user
     */
    public function roleContributor(): void
    {
        HCRolesUsersConnections::create([
            'user_id' => $this->id,
            'role_id' => HCRole::where('slug', 'contributor')->firstOrFail()->id,
        ]);
    }

    /**
     * Attach moderator role to user
     */
    public function roleModerator(): void
    {
        HCRolesUsersConnections::create([
            'user_id' => $this->id,
            'role_id' => HCRole::where('slug', 'moderator')->firstOrFail()->id,
        ]);
    }

    /**
     * Attach member role to user
     */
    public function roleMember(): void
    {
        HCRolesUsersConnections::create([
            'user_id' => $this->id,
            'role_id' => HCRole::where('slug', 'subscriber')->firstOrFail()->id,
        ]);
    }

    /**
     * Attach subscriber role to user
     */
    public function roleSubscriber(): void
    {
        HCRolesUsersConnections::create([
            'user_id' => $this->id,
            'role_id' => HCRole::where('slug', 'subscriber')->firstOrFail()->id,
        ]);
    }
}
