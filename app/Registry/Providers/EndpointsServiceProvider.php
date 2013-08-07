<?php
namespace Registry\Providers;

use Guzzle\Http\Client;
use Illuminate\Support\ServiceProvider;
use Packagist\Api\Client as Packagist;
use Registry\Services\PackagesEndpoints;

class EndpointsServiceProvider extends ServiceProvider
{
	/**
	 * Register the Registry's package with Laravel
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerEndpoints();

		$this->app->bind('endpoints', function ($app) {
			return new PackagesEndpoints($app);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('endpoints');
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// BINDINGS ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Register the various endpoints
	 *
	 * @return void
	 */
	protected function registerEndpoints()
	{
		$this->app->bind('endpoints.packagist', function () {
			return new Packagist;
		});

		$this->app->bind('endpoints.guzzle', function ($app) {
			return new Client('https://packagist.org');
		});

		$this->app->bind('endpoints.github', function ($app) {
			return new Client('https://github.com/');
		});

		$this->app->bind('endpoints.github_api', function ($app) {
			return new Client('https://api.github.com/');
		});

		$this->app->bind('endpoints.bitbucket', function ($app) {
			return new Client('https://bitbucket.org/api/1.0/repositories/');
		});

		$this->app->bind('endpoints.travis', function ($app) {
			return new Client('https://api.travis-ci.org/repos/');
		});

		$this->app->bind('endpoints.scrutinizer', function ($app) {
			return new Client('https://scrutinizer-ci.com/api/repositories/g/');
		});
	}
}