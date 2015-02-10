<?php namespace Bican\Roles;

use Illuminate\Support\ServiceProvider;

class RolesServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__.'/../../migrations/' => base_path('/database/migrations')
		], 'migrations');
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{

	}
}
