<?php
use Illuminate\Console\Command;
use Registry\Package;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Refreshes one or all packages in the database
 */
class Refresh extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'registry:refresh';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Refresh registry';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		if ($package = $this->argument('package')) {
			if (Str::contains($package, '/')) {
				return $this->refreshPackage($package);
			}

			return $this->refreshVendor($package);
		}

		// Clear cache if the database is not empty
		if ($this->laravel['db']->table('versions')->first()) {
			$this->call('cache:clear');
		}

		// Send it over
		$this->call('db:seed');
		$this->call('deploy:deploy');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('package', InputArgument::OPTIONAL, 'A package to refresh'),
		);
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// CORE METHODS /////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Refresh a vendor
	 *
	 * @param  string $vendor
	 *
	 * @return void
	 */
	protected function refreshVendor($vendor)
	{
		$packages = Package::where('name', 'LIKE', '%' .$vendor. '%')->get();
		foreach ($packages as $package) {
			$this->refreshPackage($package);
		}
	}

	/**
	 * Refresh a package in particular
	 *
	 * @param  Package|string $package
	 *
	 * @return void
	 */
	protected function refreshPackage($package)
	{
		Eloquent::unguard();

		// Fetch package
		if (!$package instanceof Package) {
			$package = Package::whereName($package)->firstOrFail();
		}

		$this->info('Refreshing package '.$package->name);

		// Forget caches related to the Package
		$this->laravel['cache']->forget($package->name.'-packagist');
		$this->laravel['cache']->forget($package->name.'-repository');
		$this->laravel['cache']->forget($package->name.'-repository-issues');
		$this->laravel['cache']->forget($package->travis.'-travis');
		$this->laravel['cache']->forget($package->travis.'-travis-builds');

		// Recompute statistics
		$seeder = new SeedPackages;
		$seeder->hydrateStatistics($package);
		$seeder->computeAllIndexes();
	}
}
