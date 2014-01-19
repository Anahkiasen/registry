<?php
use Packagist\Api\Client as Packagist;
use Registry\Package;
use Registry\Services\IndexesComputer;
use Registry\Services\PackagesStatisticsHydrater;
use Registry\Traits\Timer;

class PackagesTableSeeder extends DatabaseSeeder
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
		'rcrowe/Turbo' // Just for now
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
		$total    = sizeof($packages);

		foreach ($packages as $key => $package) {
			$this->startTimer();

			// Skip ignored packages
			if (in_array($package->getName(), $this->ignore)) {
				continue;
			}

			// Create model
			$package = $this->createPackageModel($package);
			if (!$package) {
				continue;
			}

			// Skip non-library
			if ($package->getPackagist()['type'] !== 'library') {
				continue;
			}

			// Add repository and packagist statistics
			$this->cacheEndpoints($key, $package, $packages);
			$this->hydrateStatistics($package);

			// Total and remaining time
			$this->displayProgress($key, $total);
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

		return $this->container['cache']->rememberForever('packages', function () {
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
		$vendor = explode('/', $package->getName())[0];
		$type   = in_array($vendor, ['illuminate', 'laravel']) ? 'component' : 'package';

		// Create base package
		$package = Package::create(array(
			'name'        => $package->getName(),
			'description' => $package->getDescription(),
			'packagist'   => $package->getUrl(),
			'type'        => $type,
		));

		// Cancel if no results found
		if (!$package->getPackagist()) {
			return false;
		}

		// Save repository slug
		$basePattern = '([a-zA-Z0-9\-]+)';
		$package->repository = preg_replace(
			'#((https|http|git)://|git@)(github.com|bitbucket.org)(:|/)' .$basePattern. '/' .$basePattern. '(.git)?#',
			'http://$3/$5/$6',
			$package->getPackagist()['repository']);

		// Save Laravel requirement
		$package->laravel = $package->requirement;
		$package->save();

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
		$this->startFlashTimer();
		$statistics = new PackagesStatisticsHydrater($package);

		// Hydrate statistics
		$statistics->hydrateFavorites();
		$statistics->hydrateTimes();
		$statistics->hydrateTests();
		$statistics->hydrateRawStatistics();
		$statistics->hydrateRepositoryInformations();
		$this->comment('-- Hydrating statistics (%sms)', $this->stopFlashTimer());

		return $statistics->getPackage();
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// HELPERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Display the progress of the seeding
	 *
	 * @param integer $key
	 * @param integer $total
	 *
	 * @return void
	 */
	protected function displayProgress($key, $total)
	{
		$current   = $this->stopTimer();
		$remaining = $this->estimateForIterations($total - $key)->format('%H:%I:%S');

		$this->line('-- Total time : %ss, remaining : %s', $current, $remaining);
	}

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
		$cacheQueue = ['Repository', 'Packagist', 'Travis', 'TravisBuilds', 'Scrutinizer'];
		foreach ($cacheQueue as $cache) {
			$this->startFlashTimer();
			$package->{'get'.$cache}();
			$this->comment('-- %s (%sms)', $cache, $this->stopFlashTimer());
		}
	}
}
