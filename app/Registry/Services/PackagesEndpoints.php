<?php
namespace Registry\Services;

use Exception;
use Guzzle\Http\Client as Guzzle;
use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Packagist\Api\Client as Packagist;

class PackagesEndpoints
{
	/**
	 * The IoC Container
	 *
	 * @var Container
	 */
	protected $app;

	/**
	 * The Packagist API
	 *
	 * @var Packagist
	 */
	protected $packagist;

	/**
	 * The Guzzle instance
	 *
	 * @var Guzzle
	 */
	protected $guzzle;

	/**
	 * Build a new PackagesServices
	 *
	 * @param Container $app
	 */
	public function __construct(Container $app, Packagist $packagist, Guzzle $guzzle)
	{
		$this->app       = $app;
		$this->packagist = $packagist;
		$this->guzzle    = $guzzle;
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
			$api    = $this->app['config']->get('registry.api.github');
			$url    = $package->repositoryName.$url.'?client_id=' .$api['id']. '&client_secret='.$api['secret'];
			$source = Str::contains($package->repository, 'github') ? 'github' : 'bitbucket';
		}

		return $this->app['cache']->rememberForever($url, function() use ($source, $url) {
			try {
				$informations = $this->getApi($source)->get($url)->send()->json();
			} catch (Exception $e) {
				$informations = array();
			}

			return $informations;
		});
	}

	/**
	 * Get an API
	 *
	 * @param  string $source
	 *
	 * @return Guzzle
	 */
	protected function getApi($source)
	{
		switch ($source) {
			case 'packagist':
				return $this->packagist;

			case 'guzzle':
				return clone $this->guzzle->setBaseUrl('https://packagist.org');

			case 'github':
				return clone $this->guzzle->setBaseUrl('https://api.github.com/repos/');

			case 'bitbucket':
				return clone $this->guzzle->setBaseUrl('https://bitbucket.org/api/1.0/repositories/');

			case 'travis':
				return clone $this->guzzle->setBaseUrl('https://api.travis-ci.org/repos/');

			case 'scrutinizer':
				return clone $this->guzzle->setBaseUrl('https://scrutinizer-ci.com/api/repositories/g/');
		}
	}
}