<?php
namespace Registry\Services;

use Exception;
use Guzzle\Http\Client as Guzzle;
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
	 * @param Packagist $packagist
	 * @param Guzzle    $guzzle
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
		// Get correct source and URL for SCM
		if ($source == 'scm') {
			list($source, $url) = $this->getScmEndpoint($package, $url);
		}

		// Create Guzzle instance
		$request = $this->app['endpoints.'.$source]->get($url);
		$request->getQuery()->merge(array(
			'client_id'     => $this->app['config']->get('registry.api.github')['id'],
			'client_secret' => $this->app['config']->get('registry.api.github')['secret'],
		));

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
	 * @param  string  $url
	 *
	 * @return array
	 */
	protected function getScmEndpoint(Package $package, $url)
	{
		// Get source and URL
		if (Str::contains($package->repository, 'github')) {
			$source = 'github_api';
			$url    = '/repos/'.$package->travis.$url;
		} else {
			$source = 'bitbucket';
			$url    = $package->travis.$url;
		}

		return [$source, $url];
	}
}
