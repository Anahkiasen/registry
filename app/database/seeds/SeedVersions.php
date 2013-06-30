<?php
class SeedVersions extends Seeder
{

	/**
	 * Seed versions
	 *
	 * @return void
	 */
	public function run()
	{
		$packages = Package::all();
		foreach ($packages as $package) {
			$versions = $this->getPackageVersions($package);
			DB::table('versions')->insert($versions);
			$package->keywords = $versions[0]['keywords'];
			$package->save();
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
			$time     = new Carbon\Carbon($version['time']);
			$keywords = json_encode($version['keywords']);
			$version  = array(
				'name'              => $version['name'],
				'description'       => $version['description'],
				'keywords'          => $keywords,
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
