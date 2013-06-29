<?php
class SeedMaintainers extends Seeder
{

	/**
	 * Seed the packages
	 *
	 * @return void
	 */
	public function run()
	{
		$packages = Package::all();
		foreach ($packages as $package) {
			$maintainers = $package->getPackagist()->maintainers;
			foreach ($maintainers as &$maintainer) {
				$maintainer = $this->getExisting($maintainer)->id;
			}

			$package->maintainers()->sync($maintainers);
		}
	}

	/**
	 * Get the Maintainer model
	 *
	 * @param  array $maintainer
	 *
	 * @return Maintainer
	 */
	protected function getExisting($maintainer)
	{
		$name = array_get($maintainer, 'name');
		$existingMaintainer = Maintainer::whereName($name)->first();

		// Create model if it doesn't already
		if (!$existingMaintainer) {
			$existingMaintainer = Maintainer::create(array(
				'name'     => $name,
				'slug'     => Str::slug($name),
				'email'    => array_get($maintainer, 'email'),
				'homepage' => array_get($maintainer, 'homepage'),
				'github'   => 'http://github.com/'.$name,
			));
			$existingMaintainer->touch();
		}

		return $existingMaintainer;
	}

}