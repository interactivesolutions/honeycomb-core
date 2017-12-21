<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Tests\Traits;

use DB;
use Illuminate\Contracts\Console\Kernel;

/**
 * Trait MemoryDatabaseMigrations
 * @package InteractiveSolutions\HoneycombCore\Tests\Traits
 */
trait MemoryDatabaseMigrations
{
    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrations(): void
    {
        $this->artisan('migrate:fresh');

        $this->app[Kernel::class]->setArtisan(null);

        $this->beforeApplicationDestroyed(function () {
            if (DB::connection()->getDatabaseName() != ':memory:') {
                $this->artisan('migrate:rollback');
            }
        });
    }
}
