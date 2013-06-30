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
			print 'Fetching informations for ['.($key + 1).'/'.sizeof($packages).'] ' .$package->getName().PHP_EOL;

			// Create model
			$package = $this->createPackageModel($package);

			// Skip non-library
			if ($package->getPackagist()->type != 'library') {
				continue;
			}

			// Add repository and packagist statistics
			$this->hydrateStatistics($package);
		}

		$this->computePopularity();
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

		// Save additional informations
		$package->fill(array(
			'downloads_total'   => $package->getPackagist()->downloads['total'],
			'downloads_monthly' => $package->getPackagist()->downloads['monthly'],
			'downloads_daily'   => $package->getPackagist()->downloads['daily'],
			'watchers'          => $watchers,
			'forks'             => $forks,
			'favorites'         => $package->getPackagist()->favers,
		))->touch();
	}

	/**
	 * Compute every package's popularity
	 *
	 * @return void
	 */
	protected function computePopularity()
	{
		// Compute popularity of packages
		$packages = Package::all();
		$weight   = array(
			'downloads_total' => 1.5,
			'watchers'        => 2,
			'forks'           => 1,
			'favorites'       => 0.25
		);

		// Get maximum value
		$max = array(
			'downloads_total' => DB::table('packages')->whereType('package')->max('downloads_total'),
			'watchers'        => DB::table('packages')->whereType('package')->max('watchers'),
			'forks'           => DB::table('packages')->whereType('package')->max('forks'),
			'favorites'       => DB::table('packages')->whereType('package')->max('favorites'),
		);

		foreach ($packages as $package) {
			foreach ($max as $index => $value) {
				$indexes[$index] = ($package->$index * 100 / $value) * $weight[$index];
			}

			$package->popularity = round(array_sum($indexes) / array_sum($weight), 2);
			$package->touch();
		}
	}

}
