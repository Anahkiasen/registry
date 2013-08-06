<?php
use Carbon\Carbon;
use Packagist\Api\Client as Packagist;
use Registry\Abstracts\AbstractSeeder;
use Registry\Package;
use Registry\Services\IndexesComputer;
use Registry\Services\PackagesStatisticsHydrater;
use Registry\Traits\Timer;

class SeedPackages extends AbstractSeeder
{
	use Timer;

	/**
	 * A list of packages to ignore (forks or CI)
	 *
	 * @var array
	 */
	protected $ignore = array(
		'composer/installers',
		'typo3/flow-composer-installers',
		'iyoworks/former',
		'ppi/skeleton-app',
	);

	/**
	 * Seed the packages
	 *
	 * @return void
	 */
	public function run()
	{
		$this->packages->flush();
		$packages = $this->getPackagesFromPackagist();

		foreach ($packages as $key => $package) {
			$this->startTimer();

			// Skip ignored packages
			if (in_array($package->getName(), $this->ignore)) {
				continue;
			}

			// Create model
			$package = $this->createPackageModel($package);

			// Skip non-library
			if ($package->getPackagist()['type'] !== 'library') {
				continue;
			}

			// Add repository and packagist statistics
			$this->cacheEndpoints($key, $package, $packages);
			$this->hydrateStatistics($package);
		}

		// Invert freshness scale
		$fresh = $this->packages->min('freshness');
		foreach ($this->packages->all() as $package) {
			$package->update(array(
				'freshness' => abs($package->freshness - $fresh)
			));
		}

		// Compute indexes
		$this->computeAllIndexes();
	}

	/**
	 * Compute indexes
	 *
	 * @return void
	 */
	public function computeAllIndexes()
	{
		$indexesComputer = new IndexesComputer($this->packages);

		// Compute popularity
		$this->colorize('magenta', 'Computing popularity');
		$indexesComputer->computePopularity();

		// Compute trust
		$this->colorize('magenta', 'Computing trust');
		$indexesComputer->computeTrust();
	}

	/**
	 * Get all Laravel packages
	 *
	 * @return array
	 */
	protected function getPackagesFromPackagist()
	{
		$this->comment('Fetching list of packages');

		return $this->container['cache']->rememberForever('packages', function() {
			return (new Packagist)->search('laravel');
		});
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////// PACKAGE CREATION ///////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Create the Package model from raw informations
	 *
	 * @param  array $package
	 *
	 * @return Package
	 */
	protected function createPackageModel($package)
	{
		// Unify Git repository URL
		$basePattern = '([a-zA-Z0-9\-]+)';
		$vendor      = explode('/', $package->getName())[0];
		$type        = in_array($vendor, ['illuminate', 'laravel']) ? 'component' : 'package';

		// Create base package
		$package = new Package(array(
			'name'        => $package->getName(),
			'description' => $package->getDescription(),
			'packagist'   => $package->getUrl(),
			'type'        => $type,
		));

		// Save additional attributes
		$repository = $package->getPackagist()['repository'];
		$package->repository  = preg_replace('#((https|http|git)://|git@)(github.com|bitbucket.org)(:|/)' .$basePattern. '/' .$basePattern. '(.git)?#', 'http://$3/$5/$6', $repository);

		$package->update(array(
			'slug'   => str_replace('/', '-', $package->repositoryName),
			'travis' => $package->repositoryName,
		));

		return $package;
	}

	/**
	 * Hydrate the statistics of a Package
	 *
	 * @param  Package $package
	 *
	 * @return void
	 */
	public function hydrateStatistics(Package $package)
	{
		$statistics = new PackagesStatisticsHydrater($package);

		// Hydrate statistics
		$statistics->hydrateFavorites();
		$statistics->hydrateTimes();
		$statistics->hydrateTests();
		$statistics->hydrateRawStatistics();

		return $statistics->getPackage();
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// HELPERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Print progress
	 *
	 * @param  integer    $key
	 * @param  Package    $package
	 * @param  Collection $total
	 *
	 * @return string
	 */
	protected function cacheEndpoints($key, $package, $total)
	{
		$key   = $key + 1;
		$total = sizeof($total);
		$name  = ($package instanceof Package) ? $package->name : $package->getName();

		// Hit the various endpoints to cache them
		$this->info('Fetching informations for [%03d/%03d] %s', $key, $total, $name);
		$cacheQueue = ['Repository', 'RepositoryIssues', 'Packagist', 'Travis', 'TravisBuilds', 'Scrutinizer'];
		foreach ($cacheQueue as $cache) {
			$this->comment('-- '.$cache);
			$package->{'get'.$cache}();
		}

		// Total and remaining time
		$remaining = $this->stopTimer();
		$total     = $this->estimateForIterations($total - $key)->format('%H:%I:%S');

		$this->line('-- Total time : %ss, remaining : %s', $remaining, $total);
	}
}
