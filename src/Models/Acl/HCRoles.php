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

namespace InteractiveSolutions\HoneycombNewCore\Models\Acl;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use InteractiveSolutions\HoneycombNewCore\Models\HCUsers;
use InteractiveSolutions\HoneycombCore\Models\HCUuidModel;

/**
 * Class Roles
 *
 * @package InteractiveSolutions\HoneycombNewCore\Models\Acl
 * @property string $id
 * @property int $count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $name
 * @property string $slug
 * @property-read Collection|HCPermissions[] $permissions
 * @property-read Collection|HCUsers[] $users
 * @method static Builder|HCRoles notSuperAdmin()
 * @method static Builder|HCRoles superAdmin()
 * @method static Builder|HCRoles whereCount($value)
 * @method static Builder|HCRoles whereCreatedAt($value)
 * @method static Builder|HCRoles whereDeletedAt($value)
 * @method static Builder|HCRoles whereId($value)
 * @method static Builder|HCRoles whereName($value)
 * @method static Builder|HCRoles whereSlug($value)
 * @method static Builder|HCRoles whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HCRoles extends HCUuidModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hc_acl_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'slug',
    ];

    /**
     * A role may be given various permissions.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            HCPermissions::class,
            HCRolesPermissionsConnections::getTableName(),
            'role_id',
            'permission_id'
        );
    }

    /**
     * Grant the given permission to a role.
     *
     * @param HCPermissions $permission
     * @return mixed
     */
    public function givePermissionTo(HCPermissions $permission)
    {
        return $this->permissions()->save($permission);
    }

    /**
     * A role may be given various users.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(HCUsers::class, HCRolesUsersConnections::getTableName(), 'role_id', 'user_id');
    }

    /**
     * Get super admin
     *
     * @param $query
     */
    public function scopeSuperAdmin($query)
    {
        return $query->select('id', 'slug', 'name')->where('slug', 'super-admin');
    }

    /**
     * Get super admin
     *
     * @param $query
     */
    public function scopeNotSuperAdmin($query)
    {
        return $query->select('id', 'slug', 'name')->where('slug', '!=', 'super-admin');
    }

}
