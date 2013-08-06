<?php
use Registry\Package;
use Registry\Repositories\MaintainersRepository;

class SeedMaintainers extends DatabaseSeeder
{
	/**
	 * The Maintainers Repository
	 *
	 * @var MaintainersRepository
	 */
	protected $maintainers;

	/**
	 * Build the seed
	 *
	 * @param MaintainersRepository $maintainers
	 */
	public function __construct(MaintainersRepository $maintainers)
	{
		$this->maintainers = $maintainers;
	}

	/**
	 * Seed the packages
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('maintainers')->truncate();
		DB::table('maintainer_package')->truncate();

		$packages = Package::with('maintainers')->get();

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

		return $this->maintainers->findOrCreate(array(
			'name'     => $name,
			'email'    => array_get($maintainer, 'email'),
			'homepage' => array_get($maintainer, 'homepage'),
			'github'   => 'http://github.com/'.$name,
		));
	}
}
