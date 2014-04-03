 <?php
use Illuminate\Console\Command;
use Registry\Package;
use Symfony\Component\Console\Input\InputOption;
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
	protected $description = 'Refreshes the Registry';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		// Run outstanding migrations
		$this->call('migrate');

		if ($package = $this->argument('package')) {
			if (Str::contains($package, '/')) {
				return $this->refreshPackage($package);
			}

			return $this->refreshVendor($package);
		}

		// Clear cache if the database is not empty
		if ($this->option('clear') and $this->laravel['db']->table('versions')->first()) {
			$this->call('cache:clear');
		}

		// Send it over
		$this->call('db:seed');
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('clear', 'C', InputOption::VALUE_NONE, 'Clear database or not'),
		);
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
		$this->laravel['cache']->forget($package->travis);
		$this->laravel['cache']->forget($package->travis.'-issues');
		$this->laravel['cache']->forget($package->travis.'-packagist');
		$this->laravel['cache']->forget($package->travis.'-scm');
		$this->laravel['cache']->forget($package->travis.'-travis');
		$this->laravel['cache']->forget($package->travis.'-travis-builds');

		// Recompute statistics
		$seeder = new SeedPackages;
		$seeder->hydrateStatistics($package);
		$seeder->computeAllIndexes();
	}
}
