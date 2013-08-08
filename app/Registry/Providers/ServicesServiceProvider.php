<?php
namespace Registry\Providers;

use Illuminate\Support\ServiceProvider;
use Registry\Repositories\MaintainersRepository;
use Registry\Services\MaintainersAuth;

class ServicesServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the Registry's package with Laravel
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('Registry\Services\MaintainersAuth', function ($app) {
			$repository = $app->make('Registry\Repositories\MaintainersRepository');

			return new MaintainersAuth($app, $repository);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('services');
	}
}
