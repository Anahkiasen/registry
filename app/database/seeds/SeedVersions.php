<?php
use Carbon\Carbon;
use Registry\Abstracts\AbstractSeeder;
use Registry\Package;

class SeedVersions extends AbstractSeeder
{
	/**
	 * Seed versions
	 *
	 * @return void
	 */
	public function run()
	{
		$this->versions->flush();

		foreach ($this->packages->all() as $package) {
			$keywords = $this->createPackageVersions($package);
			$package->update(array(
				'keywords' => array_values($keywords),
			));
		}
	}

	/**
	 * Get all Versions of a Package
	 *
	 * @param  Package $package
	 *
	 * @return array An array of keywords
	 */
	protected function createPackageVersions(Package $package)
	{
		$versions    = array();
		$rawVersions = $package->getPackagist()['versions'];

		// Cancel if no versions (for some reason)
		if (empty($rawVersions)) {
			return array();
		}

		foreach ($rawVersions as $version) {
			$time       = new Carbon($version['time']);
			$versions[] = $this->versions->create(array(
				'name'        => $version['name'],
				'description' => $version['description'],
				'keywords'    => array_values($version['keywords']),
				'homepage'    => $version['homepage'],
				'version'     => $version['version'],

				'created_at'  => $time->toDateTimeString(),
				'updated_at'  => $time->toDateTimeString(),
				'package_id'  => $package->id,
			));
		}

		return $versions[0]->keywords;
	}
}
