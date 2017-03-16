<?php

namespace interactivesolutions\honeycombcore\providers;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class HCBaseServiceProvider extends ServiceProvider
{
    /**
     * Register commands
     *
     * @var array
     */
    protected $commands = [];

    protected $namespace = 'some\test\app\http\controllers';

    /**
     * Bootstrap the application services.
     * @param Gate $gate
     * @param Router $router
     */
    public function boot (Gate $gate, Router $router)
    {
        // register artisan commands
        $this->commands($this->commands);

        // loading views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'Test');

        // loading translations
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'Test');

        // registering elements to publish
        $this->registerPublishElements();

        // registering helpers
        $this->registerHelpers();

        // registering routes
        $this->registerRoutes();

        //register providers
        $this->registerProviders();

        //registering router items
        $this->registerRouterItems($router);

        //registering gate items
        $this->registerGateItems($gate);
    }

    /**
     * Register helper function
     */
    protected function registerHelpers()
    {
        $filePath = __DIR__ . '/../http/helpers.php';

        if (file_exists($filePath))
            require_once $filePath;
    }

    /**
     *  Registering all vendor items which needs to be published
     */
    protected function registerPublishElements ()
    {
        $directory = __DIR__ . '/../../database/migrations/';

        // Publish your migrations
        if (file_exists ($directory))
            $this->publishes ([
                __DIR__ . '/../../database/migrations/' => database_path ('/migrations'),
            ], 'migrations');

        $directory = __DIR__ . '/../public';

        // Publishing assets
        if (file_exists ($directory))
            $this->publishes ([
                __DIR__ . '/../public' => public_path ('honeycomb'),
            ], 'public');
    }

    /**
     * Registering routes
     */
    protected function registerRoutes()
    {
        $filePath = __DIR__ . '/../../app/honeycomb/routes.php';

        if (file_exists($filePath))
            \Route::group (['namespace' => $this->namespace], function ($router) use ($filePath) {
                require $filePath;
            });
    }

    /**
     * Registering 3rd party providers which are required for this package to run
     */
    protected function registerProviders()
    {
    }

    /**
     * Registering 3rd party providers which are requires router
     * @param Router $router
     */
    protected function registerRouterItems (Router $router)
    {
    }

    /**
     * Registering 3rd party providers which are requires gate
     * @param Gate $gate
     */
    protected function registerGateItems (Gate $gate)
    {
    }
}
