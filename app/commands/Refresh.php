<?php
use Illuminate\Console\Command;
use Registry\Package;
use Symfony\Component\Console\Input\InputArgument;

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
				return $this->refreshPackage($this->argument('package'));
			} else {
				$packages = Package::where('name', 'LIKE', '%' .$package. '%')->get();
				foreach ($packages as $package) {
					$this->refreshPackage($package);
				}
				return true;
			}
		}

		// Clear cache
		if (DB::table('versions')->first()) {
			$this->call('cache:clear');
		}

		// Rebuild database
		$this->call('db:seed');
		system('git commit -am "Rebuild database"');
		system('git push origin master');

		// Send it over
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

		Cache::forget($package->name.'-packagist');
		Cache::forget($package->name.'-repository');
		Cache::forget($package->name.'-repository-issues');
		Cache::forget($package->travis.'-travis');
		Cache::forget($package->travis.'-travis-builds');

		$seeder = new SeedPackages;
		$seeder->hydrateStatistics($package);
		$seeder->computeAllIndexes();
	}
}
