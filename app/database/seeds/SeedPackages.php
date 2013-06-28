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
		$packages = Cache::remember('packages', 1440, function() {
			return App::make('packagist')->search('laravel');
		});

		foreach ($packages as $package) {
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
			if ($package->getInformations()->getType() != 'library') {
				continue;
			}

			// Get tags
			$versions = $package->getInformations()->getVersions();
			$first    = array_keys($versions)[0];
			$keywords = $versions[$first]->getKeywords();
			unset($keywords[array_search('laravel', $keywords)]);

			$package->tags   = json_encode(array_values($keywords));
			$package->touch();
		}
	}

}