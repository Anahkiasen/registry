<?php

class TestCase extends Arrounded\Testing\TestCase
{
	/**
	 * Reset the tests
	 */
	public function setUp()
	{
		$this->refreshApplication();
	}

	/**
	 * Creates the application.
	 *
	 * @return Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting     = true;
		$testEnvironment = 'testing';

		return require __DIR__.'/../../bootstrap/start.php';
	}

	/**
	 * Seed the current database
	 *
	 * @return void
	 */
	protected function seedDatabase()
	{
		if (!Registry\Package::count() === 0) {
			Artisan::call('db:seed');
		}
	}
}
