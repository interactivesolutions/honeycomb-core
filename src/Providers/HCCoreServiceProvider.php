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

use InteractiveSolutions\HoneycombCore\Console\HCAdminMenu;
use InteractiveSolutions\HoneycombCore\Console\HCAdminURL;
use InteractiveSolutions\HoneycombCore\Console\HCForms;
use InteractiveSolutions\HoneycombCore\Console\HCPermissions;
use InteractiveSolutions\HoneycombCore\Console\HCSuperAdmin;
use InteractiveSolutions\HoneycombCore\Repositories\HCBaseRepository;
use InteractiveSolutions\HoneycombCore\Repositories\HCUserRepository;
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
        HCPermissions::class,
        HCAdminMenu::class,
        HCForms::class,
        HCAdminURL::class,
        HCSuperAdmin::class,
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

        'Routes/Admin/routes.index.php',
        'Routes/Admin/routes.roles.php',
        'Routes/Admin/routes.users.php',

        'Routes/Frontend/routes.auth.php',
        'Routes/Frontend/routes.password.php',
    ];

    /**
     *
     */
    public function register(): void
    {
        // register LogViewer service provider
        if (class_exists(LaravelLogViewerServiceProvider::class)) {
            $this->app->register(LaravelLogViewerServiceProvider::class);
        }

        $this->registerRepositories();

        $this->registerServices();
    }

    /**
     *
     */
    private function registerRepositories(): void
    {
        $this->app->singleton(HCBaseRepository::class);

        $this->app->singleton(HCUserRepository::class);
    }

    /**
     *
     */
    private function registerServices(): void
    {
        $this->app->singleton(HCUserService::class);
        $this->app->singleton(HCUserActivationService::class);
    }
}
