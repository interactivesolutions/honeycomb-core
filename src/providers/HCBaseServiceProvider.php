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
    protected $namespace = '';

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

        //registering middleware
        $this->registerMiddleWare($router);

        // registering routes
        $this->registerRoutes ($router);

        //registering router items
        $this->registerRouterItems ($router);

        //registering gate items
        $this->registerGateItems ($gate);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //register providers
        $this->registerProviders ();

        // registering helpers
        $this->registerHelpers ();
    }

    /**
     * Register helper function
     */
    protected function registerHelpers ()
    {
        $filePath = $this->homeDirectory . '/../http/helpers.php';

        if( file_exists($filePath) ) {
            require_once $filePath;
        }
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

        $directory = $this->homeDirectory . '/../config';

        // Publishing assets
        if (file_exists ($directory))
            $this->publishes ([
                $this->homeDirectory . '/../config' => config_path('/'),
            ], 'config');
    }

    /**
     * Registering routes
     * @param Router $router
     */
    protected function registerRoutes (Router $router)
    {
        $filePath = $this->homeDirectory . '/../../app/honeycomb/routes.php';

        if( file_exists($filePath) ) {
            if (! $this->app->routesAreCached()) {
                $router->group([
                    'namespace'  => $this->namespace,
                ], function (Router $router) use ($filePath) {
                    require $filePath;
                });
            }
        }
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
