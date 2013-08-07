<?php
namespace Registry\Services;

use Carbon\Carbon;
use Registry\Package;

/**
 * Hydrates statistics of a Package
 */
class PackagesStatisticsHydrater
{
	/**
	 * A Package to hydrate with statistics
	 *
	 * @var Package
	 */
	protected $package;

	/**
	 * The Package's repository informations
	 *
	 * @var array
	 */
	protected $repository;

	/**
	 * Build a new PackagesStatisticsHydrater
	 *
	 * @param Package $package
	 */
	public function __construct(Package $package)
	{
		$this->package    = $package;
		$this->repository = $package->getRepository();
	}

	/**
	 * Get the "favorites" (stars/watchers/etc.)
	 *
	 * @return void
	 */
	public function hydrateFavorites()
	{
		$this->package->fill(array(
			'favorites' => $this->package->getPackagist()['favers'],
			'watchers'  => $this->getWithFallback($this->repository, 'watchers', 'followers_count'),
			'forks'     => $this->getWithFallback($this->repository, 'forks', 'forks_count'),
		));
	}

	/**
	 * Hydrate time-related statistics
	 *
	 * @return void
	 */
	public function hydrateTimes()
	{
		// Get data
		$created_at = $this->getWithFallback($this->repository, 'created_at', 'utc_created_on');
		$pushed_at  = $this->getWithFallback($this->repository, 'pushed_at', 'utc_last_updated');

		// Convert to Carbon
		$created_at = new Carbon($created_at);
		$pushed_at  = new Carbon($pushed_at);

		// Fill attributes
		$this->package->fill(array(
			'created_at' => $created_at->toDateTimeString(),
			'pushed_at'  => $pushed_at->toDateTimeString(),
			'seniority'  => $created_at->diffInDays(),
			'freshness'  => $pushed_at->diffInDays(),
		));
	}

	/**
	 * Hydrate test-related statistics
	 *
	 * @return void
	 */
	public function hydrateTests()
	{
		// Get build status
		$buildStatus = array_get($this->package->getTravis(), 'last_build_status', 2);
		$buildStatus = (int) abs($buildStatus - 2);

		// Fill attributes
		$this->package->fill(array(
			'build_status' => $buildStatus,
			'consistency'  => $this->computeConsistency(),
			'coverage'     => $this->computeCoverage(),
		));
	}

	/**
	 * Hydrate raw number-based statistics
	 *
	 * @return void
	 */
	public function hydrateRawStatistics()
	{
		$packagist = $this->package->getPackagist();

		$this->package->fill(array(
			'issues'            => $this->computeIssuesRatio(),
			'downloads_total'   => $packagist['downloads']['total'],
			'downloads_monthly' => $packagist['downloads']['monthly'],
			'downloads_daily'   => $packagist['downloads']['daily'],
		));
	}

	/**
	 * Save and return the Package
	 *
	 * @return Package
	 */
	public function getPackage()
	{
		$this->package->save();

		return $this->package;
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////// COMPUTED VALUES ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Compute the issues open/closed ratio
	 *
	 * @return integer
	 */
	protected function computeIssuesRatio()
	{
		// Get open and total issues
		$opened = array_get($this->repository, 'open_issues_count', 0);
		$total  = $this->package->getRepositoryIssues();
		$total  = $this->getWithFallback($total, '0.number', 'count');

		if ($total == 0) {
			return 100;
		}

		// Compute percentage
		$issues = ($total - $opened) * 100 / $total;

		return round($issues);
	}

	/**
	 * Compute builds consistency
	 *
	 * @return integer
	 */
	protected function computeConsistency()
	{
		$builds = $this->package->getTravisBuilds();
		$consistency = clone $builds->filter(function ($build) {
			return $build['result'] !== 1;
		});

		// If no builds, cancel
		if ($consistency->isEmpty()) {
			return 0;
		}

		// Compute and round
		$consistency = sizeof($consistency) * 100 / sizeof($builds);
		$consistency = round($consistency);

		return $consistency;
	}

	/**
	 * Compute code coverage
	 *
	 * @return integer
	 */
	protected function computeCoverage()
	{
		$coverage = $this->package->getScrutinizer();
		$methods  = array_get($coverage, 'php_code_coverage.methods', 1);
		$covered  = array_get($coverage, 'php_code_coverage.covered_methods', 1);

		return ceil($covered * 100 / $methods);
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// HELPERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get an entry from an array, or a fallback entry
	 *
	 * @param  array  $array
	 * @param  string $key
	 * @param  string $fallback
	 *
	 * @return mixed
	 */
	protected function getWithFallback($array, $key, $fallback = null)
	{
		return array_get($array, $key, array_get(
			$array, $fallback
		));
	}
}
