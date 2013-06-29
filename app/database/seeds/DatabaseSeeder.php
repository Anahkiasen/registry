<?php

class DatabaseSeeder extends Seeder {

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
		print 'Seeding '.$name.PHP_EOL;

		$this->call($class);
	}

}