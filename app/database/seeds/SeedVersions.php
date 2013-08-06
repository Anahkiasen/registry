<?php
use Registry\Package;

class SeedVersions extends DatabaseSeeder
{

	/**
	 * Seed versions
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('versions')->truncate();

		$packages = Package::all();
		foreach ($packages as $package) {
			$versions = $this->getPackageVersions($package);
			if (!empty($versions)) {
				DB::table('versions')->insert($versions);
				$package->keywords = $versions[0]['keywords'];
				$package->save();
			}
		}
	}

	/**
	 * Get all Versions of a Package
	 *
	 * @param  Package $package
	 *
	 * @return array
	 */
	protected function getPackageVersions(Package $package)
	{
		$versions = $package->getPackagist()->versions;
		foreach ($versions as &$version) {
			$time    = new Carbon\Carbon($version['time']);
			$version = array(
				'name'              => $version['name'],
				'description'       => $version['description'],
				'keywords'          => $version['keywords'],
				'homepage'          => $version['homepage'],
				'version'           => $version['version'],

				'created_at' => $time->toDateTimeString(),
				'updated_at' => $time->toDateTimeString(),
				'package_id' => $package->id,
			);
		}

		return array_values($versions);
	}

}
