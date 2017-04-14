<?php

namespace interactivesolutions\honeycombcore\providers;

use Cog\Ownership\Providers\OwnershipServiceProvider;
use File;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use interactivesolutions\honeycombcore\errors\HCLog;
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
    protected $namespace = 'interactivesolutions\honeycombcore\http\controllers';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'HCCore');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'HCCore');

        if (!$this->app->routesAreCached()) {
            \Route::group([
                'middleware' => 'web',
                'namespace'  => $this->namespace,
            ], function ($router) {
                require __DIR__ . '/../http/routes/routes.form-manager.php';
                require __DIR__ . '/../http/routes/routes.logs.php';
            });
        }

        // register oc log class facade
        AliasLoader::getInstance()->alias('HCLog', \interactivesolutions\honeycombcore\errors\facades\HCLog::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/services.php', 'services');

        $this->registerProviders();

        $this->app->bind('hclog', function () {
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
        $filePath = __DIR__ . '/../helpers/helpers.php';

        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }

    /**
     * Register service providers
     */
    protected function registerProviders()
    {
        // register rollbar service provider
        $this->app->register(HCRollbarServiceProvider::class);

        // register LogViewer service provider
        $this->app->register(LaravelLogViewerServiceProvider::class);

        //register ownership service provider
        $this->app->register(OwnershipServiceProvider::class);
    }
}
