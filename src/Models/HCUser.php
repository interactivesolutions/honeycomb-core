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

namespace InteractiveSolutions\HoneycombNewCore\Models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use InteractiveSolutions\HoneycombNewCore\Models\Acl\HCAclRole;
use InteractiveSolutions\HoneycombNewCore\Models\Traits\HCActivateUser;
use InteractiveSolutions\HoneycombNewCore\Models\Traits\HCUserRoles;
use InteractiveSolutions\HoneycombNewCore\Models\Users\HCUserPersonalInfo;
use InteractiveSolutions\HoneycombNewCore\Notifications\HCAdminWelcomeEmail;
use InteractiveSolutions\HoneycombNewCore\Notifications\HCResetPassword;

/**
 * Class HCUser
 *
 * @package InteractiveSolutions\HoneycombNewCore\Models
 * @property int $count
 * @property string $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $email
 * @property string $password
 * @property string|null $activated_at
 * @property string|null $remember_token
 * @property string|null $last_login
 * @property string|null $last_visited
 * @property string|null $last_activity
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read \InteractiveSolutions\HoneycombNewCore\Models\Users\HCUserPersonalInfo $personal
 * @property-read Collection|HCAclRole[] $roles
 * @method static Builder|HCUser whereActivatedAt($value)
 * @method static Builder|HCUser whereCount($value)
 * @method static Builder|HCUser whereCreatedAt($value)
 * @method static Builder|HCUser whereDeletedAt($value)
 * @method static Builder|HCUser whereEmail($value)
 * @method static Builder|HCUser whereId($value)
 * @method static Builder|HCUser whereLastActivity($value)
 * @method static Builder|HCUser whereLastLogin($value)
 * @method static Builder|HCUser whereLastVisited($value)
 * @method static Builder|HCUser wherePassword($value)
 * @method static Builder|HCUser whereRememberToken($value)
 * @method static Builder|HCUser whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HCUser extends HCUuidModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable, HCUserRoles, HCActivateUser;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'hc_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'activated_at',
        'last_login',
        'last_visited',
        'last_activity',
        'email',
        'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'activated_at',
        'last_login',
        'last_visited',
        'last_activity',

    ];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'string',
    ];

    /**
     * Update last login timestamp
     *
     * @param null|string $time
     */
    public function updateLastLogin(string $time = null): void
    {
        $this->timestamps = false;
        $this->last_login = $time ? $time : $this->freshTimestamp();
        $this->save();

        $this->updateLastActivity();
    }

    /**
     * Update last activity timestamp
     *
     * @param null|string $time
     */
    public function updateLastActivity(string $time = null): void
    {
        $this->timestamps = false;
        $this->last_activity = $time ? $time : $this->freshTimestamp();
        $this->save();
    }

    /**
     * Override default password notification sending mail template
     *
     * @param  string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new HCResetPassword($token));
    }

    /**
     * Welcome email
     */
    public function sendWelcomeEmail(): void
    {
        $this->notify((new HCAdminWelcomeEmail()));
    }

    /**
     * Welcome email with password
     *
     * @param string $password
     */
    public function sendWelcomeEmailWithPassword(string $password): void
    {
        $this->notify(
            (new HCAdminWelcomeEmail())->withPassword($password)
        );
    }

    /**
     * @return HasOne
     */
    public function personal(): HasOne
    {
        return $this->hasOne(HCUserPersonalInfo::class, 'user_id', 'id');
    }
}
