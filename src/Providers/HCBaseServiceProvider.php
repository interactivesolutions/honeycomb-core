<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

/**
 * Class HCBaseServiceProvider
 * @package InteractiveSolutions\HoneycombCore\Providers
 */
class HCBaseServiceProvider extends ServiceProvider
{
    /**
     * List of artisan console commands to register
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Provider controller namespace
     *
     * @var string|null
     */
    protected $namespace;

    /**
     * Provider name
     *
     * @var string|null
     */
    protected $packageName;

    /**
     * List of route paths to load
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Bootstrap the application services.
     *
     * @param Router $router
     */
    public function boot(Router $router)
    {
        /** @var Application $app */
        $app = $this->app;

        $this->commands($this->commands);

        if (!$app->routesAreCached()) {
            $this->loadRoutes($router);
        }

        $this->loadMigrations();

        $this->loadViews();

        $this->loadTranslations();

        $this->registerPublishes();
    }

    /**
     * Load package routes
     *
     * @param Router $router
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function loadRoutes(Router $router): void
    {
        /** @var string $route */
        foreach ($this->getRoutes() as $route) {
            $router->group(['namespace' => $this->namespace], function () use ($route) {
                require $this->packagePath($route);
            });
        }
    }

    /**
     * Load package migrations
     */
    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom($this->packagePath('database/migrations'));
    }

    /**
     * Load package views
     */
    protected function loadViews(): void
    {
        $this->loadViewsFrom($this->packagePath('resources/views'), $this->packageName);
    }

    /**
     * Load package translations
     */
    protected function loadTranslations(): void
    {
        $this->loadTranslationsFrom($this->packagePath('resources/lang'), $this->packageName);
    }

    /**
     *  Registering all vendor items which needs to be published
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            $this->packagePath('resources/assets') => resource_path('assets/honeycomb'),
        ], 'public');

        $this->publishes([
            $this->packagePath('config') => config_path('/'),
        ], 'config');
    }

    /**
     * Get root package path
     *
     * @param string $path
     * @return string
     */
    protected function packagePath(string $path): string
    {
        return __DIR__ . '/../' . $path;
    }

    /**
     * Get routes
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function getRoutes(): array
    {
        $fileSystem = new Filesystem();

        $file = json_decode(
            $fileSystem->get($this->packagePath('hc-config.json')),
            true
        );

        return array_get($file, 'routes', []);
    }
}
