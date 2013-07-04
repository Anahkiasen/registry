<?php
use Illuminate\Console\Command;
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
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		if ($this->argument('package')) {
			return $this->refreshPackage($this->argument('package'));
		}

		// Clear cache
		if(DB::table('versions')->first()) {
			$this->call('cache:clear');
		}

		// Rebuild database
		$this->call('migrate:refresh');
		$this->call('db:seed');
		system('git commit -am "Rebuild database"');
		system('git push origin master');

		// Send it over
		$this->call('deploy:update');
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

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
		);
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// CORE METHODS /////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Refresh a package in particular
	 *
	 * @param  string $package
	 *
	 * @return void
	 */
	protected function refreshPackage($package)
	{
		$package = Package::whereName($package)->firstOrFail();
		Eloquent::unguard();

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
