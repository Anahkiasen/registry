<?php

class DatabaseSeeder extends Seeder
{
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

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// COLORS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Print an info
	 *
	 * @param  string $message
	 *
	 * @return string
	 */
	public function info($message)
	{
		print "\033[0;34m" .$message. "\033[0m".PHP_EOL;
	}

	/**
	 * Print a comment
	 *
	 * @param  string $message
	 *
	 * @return string
	 */
	public function comment($message)
	{
		print "\033[0;35m" .$message. "\033[0m".PHP_EOL;
	}

}
