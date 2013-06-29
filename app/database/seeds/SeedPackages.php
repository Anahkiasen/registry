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
	);

	/**
	 * Seed the packages
	 *
	 * @return [type] [description]
	 */
	public function run()
	{
		print 'Fetching list of packages'.PHP_EOL;
		$packages = Cache::remember('packages', Config::get('registry.cache'), function() {
			return App::make('packagist')->search('laravel');
		});

		foreach ($packages as $key => $package) {
			if (in_array($package->getName(), $this->ignore)) continue;

			// Get type of package
			$vendor  = explode('/', $package->getName())[0];
			$type    = in_array($vendor, array('illuminate', 'laravel')) ? 'component' : 'package';

			// Create model
			$package = Package::create(array(
				'name'        => $package->getName(),
				'slug'        => Str::slug(str_replace('/', '-', $package->getName())),
				'description' => $package->getDescription(),
				'packagist'   => $package->getUrl(),
				'favorites'   => $package->getFavers(),
				'type'        => $type,
			));

			// Skip non-library
			print 'Fetching informations for ['.($key + 1).'/'.sizeof($packages).'] ' .$package->name.PHP_EOL;
			if ($package->getPackagist()->type != 'library') {
				continue;
			}

			// Unify Git repository URL
			$basePattern         = '([a-zA-Z0-9\-]+)';
			$repository          = $package->getPackagist()->repository;
			$package->repository = preg_replace('#((https|http|git)://|git@)(github.com|bitbucket.org)(:|/)' .$basePattern. '/' .$basePattern. '(.git)?#', 'http://$3/$5/$6', $repository);

			// Get watchers and forks
			$repository = $package->getRepository();
			$watchers = array_get($repository, 'watchers', array_get($repository, 'followers_count'));
			$forks    = array_get($repository, 'forks', array_get($repository, 'forks_count'));

			// Save additional informations
			$package->downloads_total   = $package->getPackagist()->downloads['total'];
			$package->downloads_monthly = $package->getPackagist()->downloads['monthly'];
			$package->downloads_daily   = $package->getPackagist()->downloads['daily'];
			$package->watchers          = $watchers;
			$package->forks             = $forks;
			$package->touch();
		}

		// Compute popularity of packages
		$packages               = Package::all();
		$max['downloads_total'] = DB::table('packages')->max('downloads_total');
		$max['watchers']        = DB::table('packages')->max('watchers');
		$max['forks']           = DB::table('packages')->max('forks');

		foreach ($packages as $package) {
			foreach ($max as $index => $value) {
				$indexes[$index] = $package->$index * 100 / $value;
			}

			$package->popularity = round(array_sum($indexes) / 3, 2);
			$package->touch();
		}
	}

}