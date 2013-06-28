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
					// 'keywords'          => $version->getKeywords(),
					'homepage'          => $version->getHomepage(),
					'version'           => $version->getVersion(),
					'versionNormalized' => $version->getVersionNormalized(),
					//'license'           => $version->getLicense(),
					// 'authors'           => $version->getAuthors(),
					// 'source'            => $version->getSource(),
					// 'dist'              => $version->getDist(),
					// 'autoload'          => $version->getAutoload(),
					// 'extra'             => $version->getExtra(),
					// 'require'           => $version->getRequire(),
					// 'requireDev'        => $version->getRequireDev(),
					// 'bin'               => $version->getBin(),

					'created_at' => new DateTime($version->getTime()),
					'updated_at' => new DateTime($version->getTime()),
					'package_id' => $package->id,
				))->touch();
			}
		}
	}
}