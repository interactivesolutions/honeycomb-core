<?php

declare(strict_types = 1);

namespace Tests;


use Illuminate\Foundation\Application;
use InteractiveSolutions\HoneycombCore\Providers\HCCoreServiceProvider;
use InteractiveSolutions\HoneycombCore\Tests\BuildsMocks;

/**
 * Class TestCase
 * @package Tests
 */
abstract class TestCase extends \Orchestra\Testbench\BrowserKit\TestCase
{
    use BuildsMocks;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            HCCoreServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
    }
}
