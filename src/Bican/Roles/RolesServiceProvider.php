<?php

namespace Bican\Roles;

use Illuminate\Support\ServiceProvider;

class RolesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../../config/roles.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('roles.php');
        } else {
            $publishPath = base_path('config/roles.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');

        $this->publishes([
            __DIR__ . '/../../migrations/' => base_path('/database/migrations')
        ], 'migrations');

        $this->registerBladeExtensions();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/roles.php', 'roles');
    }

    /**
     * Register Blade extensions.
     *
     * @return void
     */
    protected function registerBladeExtensions()
    {
        $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $blade->directive('role', function ($expression) {
            return "<?php if (Auth::check() && Auth::user()->is{$expression}): ?>";
        });

        $blade->directive('endrole', function () {
            return "<?php endif; ?>";
        });

        $blade->directive('permission', function ($expression) {
            return "<?php if (Auth::check() && Auth::user()->can{$expression}): ?>";
        });

        $blade->directive('endpermission', function () {
            return "<?php endif; ?>";
        });

        $blade->directive('level', function ($expression) {
            $level = trim($expression, '()');

            return "<?php if (Auth::check() && Auth::user()->level() >= {$level}): ?>";
        });

        $blade->directive('endlevel', function () {
            return "<?php endif; ?>";
        });

        $blade->directive('allowed', function ($expression) {
            return "<?php if (Auth::check() && Auth::user()->allowed{$expression}): ?>";
        });

        $blade->directive('endallowed', function () {
            return "<?php endif; ?>";
        });
    }
}
