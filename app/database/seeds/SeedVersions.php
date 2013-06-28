<?php
class SeedVersions extends Seeder
{
	public function run()
	{
		$packages = Package::all();
		foreach ($packages as $package) {
			$versions = $package->getInformations()->getVersions();
			foreach ($versions as $version) {
				Version::create(array(
					'name'              => $version->getName(),
					'description'       => $version->getDescription(),
					'keywords'          => json_encode($version->getKeywords()),
					'homepage'          => $version->getHomepage(),
					'version'           => $version->getVersion(),
					'versionNormalized' => $version->getVersionNormalized(),

					'created_at' => new DateTime($version->getTime()),
					'updated_at' => new DateTime($version->getTime()),
					'package_id' => $package->id,
				))->touch();
			}
		}
	}
}