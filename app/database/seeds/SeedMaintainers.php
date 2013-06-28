<?php
class SeedMaintainers extends Seeder
{

	/**
	 * Seed the packages
	 *
	 * @return [type] [description]
	 */
	public function run()
	{
		$packages = Package::all();
		foreach ($packages as $package) {
			$maintainers = $package->getInformations()->getMaintainers();
			foreach ($maintainers as &$maintainer) {
				$existingMaintainer = Maintainer::whereName($maintainer->getName())->first();
				if (!$existingMaintainer) {
					$existingMaintainer = Maintainer::create(array(
						'name'     => $maintainer->getName(),
						'email'    => $maintainer->getEmail(),
						'homepage' => $maintainer->getHomepage(),
					));
					$existingMaintainer->touch();
				}
				$maintainer = $existingMaintainer->id;
			}
			$package->maintainers()->sync($maintainers);
		}

	}

}