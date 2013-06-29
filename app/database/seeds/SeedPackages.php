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
				'description' => $package->getDescription(),
				'packagist'   => $package->getUrl(),
				'favorites'   => $package->getFavers(),
				'type'        => $type,
			));

			// Skip non-library
			print 'Fetching informations for ['.($key + 1).'/'.sizeof($packages).'] ' .$package->name.PHP_EOL;
			if ($package->getInformations()->type != 'library') {
				continue;
			}

			// Save
			$package->downloads_total   = $package->getInformations()->downloads['total'];
			$package->downloads_monthly = $package->getInformations()->downloads['monthly'];
			$package->downloads_daily   = $package->getInformations()->downloads['daily'];
			$package->github            = $package->getInformations()->repository;
			$package->touch();
		}
	}

}