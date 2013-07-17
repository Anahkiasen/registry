<?php
use Colors\Color;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
	/**
	 * The Colorizer instance
	 *
	 * @var Color
	 */
	protected $colors;

	/**
	 * Reset the tests
	 */
	public function setUp()
	{
		$this->refreshApplication();
		$this->colors = new Color;
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
