<?php

namespace interactivesolutions\honeycombcore\providers;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class HCBaseServiceProvider extends ServiceProvider
{
    /**
     * Home dir
     *
     * @var string
     */
    protected $homeDirectory = __DIR__;

    /**
     * @var array
     */
    protected $commands = [];

    /**
     * @var string
     */
    protected $namespace = 'some\test\app\http\controllers';

    /**
     * @var string
     */
    protected $serviceProviderNameSpace = '';

    /**
     * Bootstrap the application services.
     * @param Gate $gate
     * @param Router $router
     */
    public function boot (Gate $gate, Router $router)
    {
        // register artisan commands
        $this->commands ($this->commands);

        // loading views
        $this->loadViewsFrom ($this->homeDirectory . '/../../resources/views', $this->serviceProviderNameSpace);

        // loading translations
        $this->loadTranslationsFrom ($this->homeDirectory . '/../../resources/lang', $this->serviceProviderNameSpace);

        // registering elements to publish
        $this->registerPublishElements ();

        // registering helpers
        $this->registerHelpers ();

        //registering middleware
        $this->registerMiddleWare($router);

        // registering routes
        $this->registerRoutes ();

        //register providers
        $this->registerProviders ();

        //registering router items
        $this->registerRouterItems ($router);

        //registering gate items
        $this->registerGateItems ($gate);
    }

    /**
     * Register helper function
     */
    protected function registerHelpers ()
    {
        $filePath = $this->homeDirectory . '/../http/helpers.php';

        if (file_exists ($filePath))
            require_once $filePath;
    }

    /**
     *  Registering all vendor items which needs to be published
     */
    protected function registerPublishElements ()
    {
        $directory = $this->homeDirectory . '/../../database/migrations/';

        // Publish your migrations
        if (file_exists ($directory))
            $this->publishes ([
                $this->homeDirectory . '/../../database/migrations/' => database_path ('/migrations'),
            ], 'migrations');

        $directory = $this->homeDirectory . '/../public';

        // Publishing assets
        if (file_exists ($directory))
            $this->publishes ([
                $this->homeDirectory . '/../public' => public_path ('honeycomb'),
            ], 'public');
    }

    /**
     * Registering routes
     */
    protected function registerRoutes ()
    {
        $filePath = $this->homeDirectory . '/../../app/honeycomb/routes.php';

        if (file_exists ($filePath))
            \Route::group (['namespace' => $this->namespace], function ($router) use ($filePath) {
                require $filePath;
            });
    }

    /**
     * Registering 3rd party providers which are required for this package to run
     */
    protected function registerProviders ()
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

    /**
     * Registering middleware
     * @param Router $router
     */
    protected function registerMiddleWare (Router $router)
    {
    }
}
