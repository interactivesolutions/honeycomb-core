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

namespace InteractiveSolutions\HoneycombNewCore\Providers;

use Illuminate\Routing\Router;
use InteractiveSolutions\HoneycombCore\Providers\HCBaseServiceProvider;
use InteractiveSolutions\HoneycombNewCore\Repositories\HCUserRepository;

/**
 * Class HCNewCoreServiceProvider
 * @package InteractiveSolutions\HoneycombNewCore\Providers
 */
class HCNewCoreServiceProvider extends HCBaseServiceProvider
{
    /**
     * @var string
     */
    protected $homeDirectory = __DIR__;

    /**
     * Console commands
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Namespace
     *
     * @var string
     */
    protected $namespace = 'InteractiveSolutions\HoneycombNewCore\Http\Controllers';

    /**
     * Provider facade name
     *
     * @var string
     */
    protected $serviceProviderNameSpace = 'HCNewCore';

    /**
     * @param Router $router
     */
    protected function registerRoutes(Router $router): void
    {
        $routes = [
            $this->modulePath('Routes/Admin/04_routes.users.php'),
        ];

        foreach ($routes as $route) {
            $router->group(['namespace' => $this->namespace], function($router) use ($route) {
                require $route;
            });
        }
    }

    /**
     *
     */
    protected function loadViews(): void
    {
        $this->loadViewsFrom($this->homeDirectory . '/../resources/views', $this->serviceProviderNameSpace);
    }

    /**
     *
     */
    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom($this->homeDirectory . '/../Database/Migrations');
    }

    /**
     *
     */
    protected function loadTranslations(): void
    {
        $this->loadTranslationsFrom($this->homeDirectory . '/../resources/lang', $this->serviceProviderNameSpace);
    }

    /**
     * @param string $path
     * @return string
     */
    private function modulePath(string $path): string
    {
        return __DIR__ . '/../' . $path;
    }

}