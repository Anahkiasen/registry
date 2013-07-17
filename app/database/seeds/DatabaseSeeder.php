<?php
use Colors\Color;

class DatabaseSeeder extends Seeder
{
	/**
	 * The Colorizer instance
	 *
	 * @var Color
	 */
	protected $colors;

	/**
	 * Build a new Seeder
	 */
	public function __construct()
	{
		$this->colors = new Color;
	}

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
	public function line($message)
	{
		print $message.PHP_EOL;
	}

	/**
	 * Print an info
	 *
	 * @param  string $message
	 *
	 * @return string
	 */
	public function success($message)
	{
		$colors = $this->colors;

		$this->line($colors($message)->green);
	}

	/**
	 * Print an info
	 *
	 * @param  string $message
	 *
	 * @return string
	 */
	public function info($message)
	{
		$colors = $this->colors;

		$this->line($colors($message)->blue);
	}

	/**
	 * Print an error
	 *
	 * @param  string $message
	 *
	 * @return string
	 */
	public function error($message)
	{
		$colors = $this->colors;

		$this->line($colors($message)->red);
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
		$colors = $this->colors;

		$this->line($colors($message)->yellow);
	}
}
