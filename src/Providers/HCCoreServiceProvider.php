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

namespace InteractiveSolutions\HoneycombCore\Providers;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Routing\Router;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use InteractiveSolutions\HoneycombCore\Console\HCCreateSuperAdminCommand;
use InteractiveSolutions\HoneycombCore\Console\HCGenerateAdminMenuCommand;
use InteractiveSolutions\HoneycombCore\Console\HCGenerateFormsCommand;
use InteractiveSolutions\HoneycombCore\Console\HCScanRolePermissionsCommand;
use InteractiveSolutions\HoneycombCore\Console\HCSeedCommand;
use InteractiveSolutions\HoneycombCore\Http\Middleware\HCAclAdminMenu;
use InteractiveSolutions\HoneycombCore\Http\Middleware\HCAclAuthenticate;
use InteractiveSolutions\HoneycombCore\Http\Middleware\HCAclPermissionsMiddleware;
use InteractiveSolutions\HoneycombCore\Http\Middleware\HCLogLastActivity;
use InteractiveSolutions\HoneycombCore\Models\Acl\HCAclPermission;
use InteractiveSolutions\HoneycombCore\Models\HCUser;
use InteractiveSolutions\HoneycombCore\Repositories\Acl\HCPermissionRepository;
use InteractiveSolutions\HoneycombCore\Repositories\Acl\HCRoleRepository;
use InteractiveSolutions\HoneycombCore\Repositories\HCBaseRepository;
use InteractiveSolutions\HoneycombCore\Repositories\HCUserRepository;
use InteractiveSolutions\HoneycombCore\Repositories\Users\HCPersonalInfoRepository;
use InteractiveSolutions\HoneycombCore\Repositories\Users\HCUserActivationRepository;
use InteractiveSolutions\HoneycombCore\Services\Acl\HCRoleService;
use InteractiveSolutions\HoneycombCore\Services\HCUserActivationService;
use InteractiveSolutions\HoneycombCore\Services\HCUserService;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider;

/**
 * Class HCCoreServiceProvider
 * @package InteractiveSolutions\HoneycombCore\Providers
 */
class HCCoreServiceProvider extends HCBaseServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        HCScanRolePermissionsCommand::class,
        HCGenerateAdminMenuCommand::class,
        HCGenerateFormsCommand::class,
        HCCreateSuperAdminCommand::class,
        HCSeedCommand::class,
    ];

    /**
     * Namespace
     *
     * @var string
     */
    protected $namespace = 'InteractiveSolutions\HoneycombCore\Http\Controllers';

    /**
     * Provider name
     *
     * @var string
     */
    protected $packageName = 'HCCore';

    /**
     * List of route paths to load
     *
     * @var array
     */
    protected $routes = [
        // core
        'Routes/routes.form-manager.php',
        'Routes/routes.logs.php',
        'Routes/routes.welcome.php',

        'Routes/Admin/routes.admin.php',
        'Routes/Admin/routes.roles.php',
        'Routes/Admin/routes.users.php',

        'Routes/Frontend/routes.auth.php',
        'Routes/Frontend/routes.password.php',
    ];

    /**
     * @param Router $router
     * @throws \Exception
     */
    public function boot(Router $router): void
    {
        parent::boot($router);

        $this->registerGateItems(app()->make(Gate::class));

        $this->registerMiddleware($router);
    }

    /**
     *
     */
    public function register(): void
    {
        // register LogViewer service provider
        if (class_exists(LaravelLogViewerServiceProvider::class)) {
            $this->app->register(LaravelLogViewerServiceProvider::class);
        }

        $this->mergeConfigFrom(
            $this->packagePath('config/hc.php'), 'hc'
        );

        $this->registerRepositories();

        $this->registerServices();
    }

    /**
     * Register acl permissions
     *
     * @param Gate $gate
     * @throws \Exception
     */
    private function registerGateItems(Gate $gate): void
    {
        $gate->before(function (HCUser $user) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        $permissions = $this->getPermissions();

        if (!is_null($permissions)) {
            foreach ($permissions as $permission) {
                $gate->define($permission->action, function (HCUser $user) use ($permission) {
                    return $user->hasPermission($permission);
                });
            }
        }
    }

    /**
     * @param Router $router
     */
    private function registerMiddleware(Router $router): void
    {
        $router->aliasMiddleware('acl', HCACLPermissionsMiddleware::class);
        $router->aliasMiddleware('auth', HCACLAuthenticate::class);

        $router->pushMiddleWareToGroup('web', HCACLAdminMenu::class);
        $router->pushMiddleWareToGroup('web', HCLogLastActivity::class);
    }


    /**
     * Get permissions
     *
     * @return null|Collection
     * @throws \Exception
     */
    private function getPermissions(): ? Collection
    {
        if (!cache()->has('hc-permissions')) {
            try {
                if (class_exists(HCAclPermission::class) && Schema::hasTable(HCAclPermission::getTableName())) {
                    $expiresAt = Carbon::now()->addHour(12);

                    $permissions = HCAclPermission::with('roles')->get();

                    cache()->put('hc-permissions', $permissions, $expiresAt);
                }
            } catch (\Exception $e) {
                $msg = $e->getMessage();

                if ($e->getCode() != 1045) {
                    throw new \Exception($msg);
                }
            }
        }

        return cache()->get('hc-permissions');
    }

    /**
     *
     */
    private function registerRepositories(): void
    {
        $this->app->singleton(HCBaseRepository::class);

        $this->app->singleton(HCUserRepository::class);
        $this->app->singleton(HCRoleRepository::class);
        $this->app->singleton(HCPermissionRepository::class);
        $this->app->singleton(HCPersonalInfoRepository::class);
        $this->app->singleton(HCUserActivationRepository::class);
    }

    /**
     *
     */
    private function registerServices(): void
    {
        $this->app->singleton(HCUserService::class);
        $this->app->singleton(HCUserActivationService::class);
        $this->app->singleton(HCRoleService::class);
    }
}
