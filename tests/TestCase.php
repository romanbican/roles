<?php

use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase as TestBenchTestCase;

class TestCase extends TestBenchTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [Ultraware\Roles\RolesServiceProvider::class, TestMigrationsServiceProvider::class];
    }

    /**
     * Resolve application Console Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton(Illuminate\Contracts\Console\Kernel::class, Orchestra\Testbench\Console\Kernel::class);
    }

    protected function setupDbConfig($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function runMigrations()
    {
        $this->loadMigrationsFrom([
            '--database' => 'testbench',
        ]);
    }
}

class TestMigrationsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom([
            realpath(__DIR__ . '/../migrations'),
            realpath(__DIR__ . '/database/migrations')
        ]);
    }
}
