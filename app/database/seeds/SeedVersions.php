<?php
class SeedVersions extends Seeder
{
	public function run()
	{
		$packages = Package::all();
		foreach ($packages as $package) {
			$versions = $package->getInformations()->versions;
			foreach ($versions as $version) {
				Version::create(array(
					'name'              => $version['name'],
					'description'       => $version['description'],
					'keywords'          => json_encode($version['keywords']),
					'homepage'          => $version['homepage'],
					'version'           => $version['version'],

					'created_at' => new DateTime($version['time']),
					'updated_at' => new DateTime($version['time']),
					'package_id' => $package->id,
				))->touch();
			}
		}
	}
}