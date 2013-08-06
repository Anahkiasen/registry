<?php
namespace Registry\Traits;

use Carbon\Carbon;

/**
 * Grants a class timing abilities
 */
trait Timer
{
	/**
	 * An array of timers
	 *
	 * @var array
	 */
	protected $timers;

	/**
	 * The current timer
	 *
	 * @var integer
	 */
	protected $timer;

	/**
	 * Start a timer
	 *
	 * @return void
	 */
	protected function startTimer()
	{
		$this->timer = microtime(true);
	}

	/**
	 * Stop the current timer
	 *
	 * @return integer The time took by the timer
	 */
	protected function stopTimer()
	{
		$timer = round(microtime(true) - $this->timer, 4);

		// Save timer
		$this->timers[] = $timer;
		$this->timer = null;

		return $timer;
	}

	/**
	 * Get the average time took by timers
	 *
	 * @return integer
	 */
	public function getTimersAverage()
	{
		return array_sum($this->timers) / sizeof($this->timers);
	}

	/**
	 * Compute an estimated time for X iterations based on previous timers
	 *
	 * @param  integer $iterations
	 *
	 * @return Carbon
	 */
	public function estimateForIterations($iterations)
	{
		// Comput remaining seconds
		$remaining = $this->getTimersAverage() * $iterations;

		// Convert to Carbon object
		$remaining = Carbon::createFromTimestamp(time() + $remaining);
		$remaining = Carbon::now()->diff($remaining);

		return $remaining;
	}
}