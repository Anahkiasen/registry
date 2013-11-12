<?php
use Registry\Abstracts\AbstractSeeder;

class DatabaseSeeder extends AbstractSeeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->seed('Packages');
		$this->seed('Versions');
		$this->seed('Maintainers');
	}
}
