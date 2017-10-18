<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Providers;

use File;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use InteractiveSolutions\HoneycombCore\Errors\HCLog;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider;

class HCCoreServiceProvider extends ServiceProvider
{
    /**
     * Commands
     *
     * @var array
     */
    protected $commands = [];

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'InteractiveSolutions\HoneycombCore\Http\Controllers';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'HCCore');
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'HCCore');

        if (!$this->app->routesAreCached()) {
            \Route::group([
                'middleware' => 'web',
                'namespace' => $this->namespace,
            ], function($router) {
                require __DIR__ . '/../Routes/routes.admin.php';
                require __DIR__ . '/../Routes/routes.form-manager.php';
                require __DIR__ . '/../Routes/routes.logs.php';
                require __DIR__ . '/../Routes/routes.welcome.php';
            });
        }

        // register oc log class facade
        AliasLoader::getInstance()->alias('HCLog', \InteractiveSolutions\HoneycombCore\Errors\Facades\HCLog::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/services.php', 'services');

        $this->registerProviders();

        $this->app->bind('hclog', function() {
            return new HCLog;
        });

        // register artisan commands
        $this->commands($this->commands);

        $this->registerHelpers();
    }

    /**
     * Register helper function
     */
    private function registerHelpers()
    {
        include_once __DIR__ . '/../Helpers/helpers.php';
    }

    /**
     * Register service providers
     */
    protected function registerProviders()
    {
        // register rollbar service provider
        if (class_exists(HCRollbarServiceProvider::class)) {
            $this->app->register(HCRollbarServiceProvider::class);
        }

        // register LogViewer service provider
        if (class_exists(LaravelLogViewerServiceProvider::class)) {
            $this->app->register(LaravelLogViewerServiceProvider::class);
        }
    }
}
