<?php
namespace Registry\PackagesEndpoints;

use Registry\Package;
use Illuminate\Cache\CacheManager;

/**
 * An interface to fetch Repository informations about a Package
 */
interface RepositoryInterface
{
	/**
	 * Build a new Github Repository
	 *
	 * @param Package $package
	 */
	public function __construct(Package $package, $client, CacheManager $cache);

	/**
	 * Get the Repository's opened issues
	 *
	 * @return integer
	 */
	public function openedIssues();

	/**
	 * Get the number of issues
	 *
	 * @return integer
	 */
	public function issuesCount();

	/**
	 * Get the Repository's watchers
	 *
	 * @return integer
	 */
	public function favorites();

	/**
	 * Get the Repository's forks
	 *
	 * @return integer
	 */
	public function forks();

	/**
	 * Get the creation date of the Repository
	 *
	 * @return Carbon
	 */
	public function createdAt();

	/**
	 * Get the last time the Repository was updated
	 *
	 * @return Carbon
	 */
	public function updatedAt();

	/**
	 * Get the Repository's README
	 *
	 * @return string
	 */
	public function readmeEndpoint();

	/**
	 * Get the Repository's README
	 *
	 * @return string
	 */
	public function showEndpoint();
}
