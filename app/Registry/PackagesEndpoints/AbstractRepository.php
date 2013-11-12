<?php
namespace Registry\PackagesEndpoints;

use Exception;
use Illuminate\Cache\CacheManager;
use Registry\Package;

abstract class AbstractRepository
{
	/**
	 * The Package
	 *
	 * @var Package
	 */
	protected $package;

	/**
	 * The IoC Container implementation
	 *
	 * @var CacheManager
	 */
	protected $cache;

	/**
	 * The API client
	 *
	 * @var Client
	 */
	protected $client;

	/**
	 * Build a new Github Repository
	 *
	 * @param Package $package
	 */
	public function __construct(Package $package, $client, CacheManager $cache)
	{
		$this->cache   = $cache;
		$this->package = $package;
		$this->client  = $client;

		// Hit endpoints
		$this->show();
		$this->readme();
		$this->issues();
	}

	/**
	 * Get the README contents
	 *
	 * @return string
	 */
	public function readme()
	{
		return $this->cache->rememberForever($this->package->travis.'-readme', function() {
			try {
				return (string) $this->readmeEndpoint();
			} catch (Exception $e) {
				return '';
			}
		});
	}

	/**
	 * Get the issues
	 *
	 * @param string $information An information to fetch from the data
	 *
	 * @return array
	 */
	protected function issues($information = null)
	{
		$informations = $this->cache->rememberForever($this->package->travis.'-issues', function() {
			try {
				return $this->issuesEndpoint();
			} catch (Exception $e) {
				return array();
			}
		});

		return $information ? array_get($informations, $information) : $informations;
	}

	/**
	 * Get the core informations
	 *
	 * @param string $information An information to fetch from the data
	 *
	 * @return array
	 */
	protected function show($information = null)
	{
		$informations = $this->cache->rememberForever($this->package->travis.'-scm', function() {
			try {
				return $this->showEndpoint();
			} catch (Exception $e) {
				return array();
			}
		});

		return $information ? array_get($informations, $information) : $informations;
	}

}
