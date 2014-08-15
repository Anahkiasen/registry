<?php
namespace Registry\PackagesEndpoints;

use Bitbucket\API\API as Bitbucket;
use Github\Client as Github;
use Guzzle\Http\Client;
use Illuminate\Support\ServiceProvider;
use Packagist\Api\Client as Packagist;

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

		$this->app->bind('endpoints.github_api', function ($app) {
			$credentials = $app['config']->get('services.github');
			$github = new Github;
			$github->authenticate($credentials['id'], $credentials['secret'], Github::AUTH_URL_CLIENT_ID);

			return $github;
		});

		$this->app->bind('endpoints.guzzle', function ($app) {
			return new Client('https://packagist.org');
		});

		$this->app->bind('endpoints.github', function ($app) {
			$github = new Client('https://github.com/');
			$github->setDefaultOption('headers', ['Accept' => 'application/json']);

			return $github;
		});

		$this->app->bind('endpoints.bitbucket', function ($app) {
			return new Bitbucket;
		});

		$this->app->bind('endpoints.travis', function ($app) {
			return new Client('https://api.travis-ci.org/repos/');
		});

		$this->app->bind('endpoints.scrutinizer', function ($app) {
			return new Client('https://scrutinizer-ci.com/api/repositories/g/');
		});
	}
}
