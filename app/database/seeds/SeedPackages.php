<?php
class SeedPackages extends Seeder
{

	/**
	 * A list of packages to ignore
	 *
	 * @var array
	 */
	protected $ignore = array(
		'composer/installers',
		'typo3/flow-composer-installers',
		'iyoworks/former',
	);

	/**
	 * Seed the packages
	 *
	 * @return void
	 */
	public function run()
	{
		$packages = $this->getPackages();
		foreach ($packages as $key => $package) {
			if (in_array($package->getName(), $this->ignore)) continue;
			$this->printProgress($key, $package, $packages, 'informations');

			// Create model
			$package = $this->createPackageModel($package);

			// Skip non-library
			if ($package->getPackagist()->type != 'library') {
				continue;
			}

			// Add repository and packagist statistics
			$this->hydrateStatistics($package);
		}

		// Invert freshness scale
		$fresh = Package::min('freshness');
		foreach (Package::all() as $package) {
			$package->freshness = abs($package->freshness - $fresh);
			$package->save();
		}

		// Compute indexes
		$this->computePopularity();
		$this->computeTrust();
	}

	/**
	 * Get all Laravel packages
	 *
	 * @return array
	 */
	protected function getPackages()
	{
		print 'Fetching list of packages'.PHP_EOL;

		return Cache::remember('packages', Config::get('registry.cache'), function() {
			return App::make('packagist')->search('laravel');
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
		// Get type of package
		$vendor  = explode('/', $package->getName())[0];
		$type    = in_array($vendor, array('illuminate', 'laravel')) ? 'component' : 'package';
		$slug    = str_replace('/', '-', $package->getName());

		// Create model
		return Package::create(array(
			'name'        => $package->getName(),
			'slug'        => Str::slug($slug),
			'description' => $package->getDescription(),
			'packagist'   => $package->getUrl(),
			'type'        => $type,
		));
	}

	/**
	 * Hydrate the statistics of a Package
	 *
	 * @param  Package $package
	 *
	 * @return void
	 */
	protected function hydrateStatistics(Package $package)
	{
		// Unify Git repository URL
		$basePattern         = '([a-zA-Z0-9\-]+)';
		$repository          = $package->getPackagist()->repository;
		$package->repository = preg_replace('#((https|http|git)://|git@)(github.com|bitbucket.org)(:|/)' .$basePattern. '/' .$basePattern. '(.git)?#', 'http://$3/$5/$6', $repository);

		// Get watchers and forks
		$repository = $package->getRepository();
		$watchers   = array_get($repository, 'watchers', array_get($repository, 'followers_count'));
		$forks      = array_get($repository, 'forks', array_get($repository, 'forks_count'));
		$created_at = new Carbon\Carbon(array_get($repository, 'created_at', array_get($repository, 'utc_created_on')));
		$pushed_at  = new Carbon\Carbon(array_get($repository, 'updated_at', array_get($repository, 'utc_last_updated')));

		// Save additional informations
		$package->fill(array(
			// Downloads
			'downloads_total'   => $package->getPackagist()->downloads['total'],
			'downloads_monthly' => $package->getPackagist()->downloads['monthly'],
			'downloads_daily'   => $package->getPackagist()->downloads['daily'],

			// Repository statistics
			'watchers'          => $watchers,
			'forks'             => $forks,
			'favorites'         => $package->getPackagist()->favers,

			// Date-related statistics
			'created_at'        => $created_at->toDateTimeString(),
			'pushed_at'         => $pushed_at->toDateTimeString(),
			'seniority'         => $created_at->diffInDays(),
			'freshness'         => $pushed_at->diffInDays() * -1,
		))->save();
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// INDEXES ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Compute every package's popularity
	 *
	 * @return void
	 */
	protected function computePopularity()
	{
		$this->computeIndexes('popularity', array(
			'downloads_total' => 1.5,
			'watchers'        => 2,
			'forks'           => 1,
			'favorites'       => 0.25
		), array(
			'downloads_total' => Package::whereType('package')->max('downloads_total'),
			'watchers'        => Package::whereType('package')->max('watchers'),
			'forks'           => Package::whereType('package')->max('forks'),
			'favorites'       => Package::whereType('package')->max('favorites'),
		));
	}

	/**
	 * Compute every package's trust index
	 *
	 * @return void
	 */
	protected function computeTrust()
	{
		$this->computeIndexes('trust', array(
			'travisStatus' => 1,
			'seniority'    => 0.5,
			'freshness'    => 1,
		), array(
			'travisStatus' => 2,
			'seniority'    => Package::whereType('package')->max('seniority'),
			'freshness'    => Package::whereType('package')->max('freshness'),
		), 0);
	}

	/**
	 * Compute an index
	 *
	 * @param  string $attribute
	 * @param  array  $weights
	 * @param  array  $ceilings
	 * @param integer $rounding
	 *
	 * @return void
	 */
	protected function computeIndexes($attribute, $weights, $ceilings, $rounding = 2)
	{
		print '-- Computing ' .$attribute. ' indexes'.PHP_EOL;

		$packages = Package::all();

		foreach ($packages as $key => $package) {
			if ($attribute == 'trust') $this->printProgress($key, $package, $packages, 'Travis');
			foreach ($ceilings as $index => $value) {
				$indexes[$index] = ($package->$index * 100 / $value) * $weights[$index];
			}

			$package->$attribute = round(array_sum($indexes) / array_sum($weights), $rounding);
			$package->save();
		}
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
	 * @param  string     $message
	 *
	 * @return string
	 */
	protected function printProgress($key, $package, $total, $message)
	{
		$key     = $key + 1;
		$total   = sizeof($total);
		$package = ($package instanceof Package) ? $package->name : $package->getName();

		print sprintf('Fetching %s informations for [%s/%s] %s', $message, $key, $total, $package).PHP_EOL;
	}

}
