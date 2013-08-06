<?php
use Registry\Traits\Colorizer;

class DatabaseSeeder extends Seeder
{
	use Colorizer;

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->seed('SeedPackages');
		$this->seed('SeedVersions');
		$this->seed('SeedMaintainers');
	}

	/**
	 * Run a seed
	 *
	 * @param  string $class
	 *
	 * @return void
	 */
	public function seed($class)
	{
		$name = str_replace('Seed', null, $class);
		$this->info('Seeding '.$name);

		$this->call($class);
	}
}
