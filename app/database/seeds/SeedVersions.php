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
			$versions = $this->getPackageVersions($package);
			if (!empty($versions)) {
				$package->update(array(
					'keywords' => $versions[0]['keywords'],
				));
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
		$versions = $package->getPackagist()['versions'];
		foreach ($versions as &$version) {
			$time = new Carbon($version['time']);
			$this->versions->create(array(
				'name'        => $version['name'],
				'description' => $version['description'],
				'keywords'    => $version['keywords'],
				'homepage'    => $version['homepage'],
				'version'     => $version['version'],

				'created_at'  => $time->toDateTimeString(),
				'updated_at'  => $time->toDateTimeString(),
				'package_id'  => $package->id,
			));
		}

		return $this->versions->all();
	}
}
