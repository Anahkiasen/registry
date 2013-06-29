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
			$versions = $package->getPackagist()->versions;
			foreach ($versions as &$version) {
				$time = new Carbon\Carbon($version['time']);
				$version = array(
					'name'              => $version['name'],
					'description'       => $version['description'],
					'keywords'          => json_encode($version['keywords']),
					'homepage'          => $version['homepage'],
					'version'           => $version['version'],

					'created_at' => $time->toDateTimeString(),
					'updated_at' => $time->toDateTimeString(),
					'package_id' => $package->id,
				);
			}
			DB::table('versions')->insert(array_values($versions));
		}
	}

}