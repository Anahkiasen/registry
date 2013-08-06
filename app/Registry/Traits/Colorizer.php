<?php
namespace Registry\Traits;

use Colors\Color;

/**
 * Add colorizer capability to the class
 */
trait Colorizer
{
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
		$colors = new Color;

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
		$colors = new Color;

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
		$colors = new Color;

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
		$colors = new Color;

		$this->line($colors($message)->yellow);
	}
}
