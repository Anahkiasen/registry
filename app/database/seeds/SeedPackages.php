<?php
class SeedPackages extends Seeder
{
	public function run()
	{
		$packages = Cache::remember('packages', 1440, function() {
			return App::make('packagist')->search('laravel');
		});

		foreach ($packages as $package) {
			$vendor = explode('/', $package->getName())[0];
			$type   = in_array($vendor, array('illuminate', 'laravel')) ? 'component' : 'package';

			Package::create(array(
				'name'        => $package->getName(),
				'description' => $package->getDescription(),
				'url'         => $package->getUrl(),
				'downloads'   => $package->getDownloads(),
				'favorites'   => $package->getFavers(),
				'type'        => $type,
			))->touch();
		}
	}
}