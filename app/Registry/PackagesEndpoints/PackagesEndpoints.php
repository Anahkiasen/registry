<?php
namespace Registry\PackagesEndpoints;

use Exception;
use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Registry\Package;

class PackagesEndpoints
{
	/**
	 * The IoC Container
	 *
	 * @var Container
	 */
	protected $app;

	/**
	 * Build a new PackagesEndpoints
	 *
	 * @param Container $app
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;
	}

	/**
	 * Get informations from an API
	 *
	 * @param  Package $package
	 * @param  string  $source       Source
	 * @param  string  $url          The endpoint
	 *
	 * @return array
	 */
	public function getFromApi(Package $package, $source, $url)
	{
		// Create Guzzle instance
		$request = $this->app['endpoints.'.$source]->get($url);

		return $this->app['cache']->rememberForever($url, function() use ($request) {
			try {
				$informations = $request->send()->json();
			} catch (Exception $e) {
				$informations = array();
			}

			return $informations;
		});
	}

	/**
	 * Get the correct SCM endpoint
	 *
	 * @param  Package $package
	 *
	 * @return array
	 */
	public function getRepository(Package $package)
	{
		// Get source and URL
		if (Str::contains($package->repository, 'github')) {
			$source = 'github_api';
			$class  = 'Registry\PackagesEndpoints\GithubRepository';
		} else {
			$source = 'bitbucket';
			$class  = 'Registry\PackagesEndpoints\BitbucketRepository';
		}

		return new $class($package, $this->app['endpoints.'.$source], $this->app['cache']);
	}
}
