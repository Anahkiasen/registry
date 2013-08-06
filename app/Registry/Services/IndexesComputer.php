<?php
namespace Registry\Services;

use Registry\Repositories\PackagesRepository;

/**
 * Computes indexes over a repository of Packages
 */
class IndexesComputer
{
	/**
	 * The Packages Repository
	 *
	 * @var PackagesRepository
	 */
	protected $packages;

	/**
	 * Build the seed
	 *
	 * @param PackagesRepository $packages
	 */
	public function __construct(PackagesRepository $packages)
	{
		$this->packages = $packages;
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// INDEXES ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Compute every package's popularity
	 *
	 * @return void
	 */
	public function computePopularity()
	{
		$this->computeIndexes('popularity', array(
			'downloads_total' => 1.25,
			'watchers'        => 2,
			'forks'           => 0.75,
			'favorites'       => 0.25,
			'freshness'       => 0.75,
		), array(
			'downloads_total' => 'downloads_total',
			'watchers'        => 'watchers',
			'forks'           => 'forks',
			'favorites'       => 'favorites',
			'freshness'       => 'freshness',
		));
	}

	/**
	 * Compute every package's trust index
	 *
	 * @return void
	 */
	public function computeTrust()
	{
		$this->computeIndexes('trust', array(
			'travisStatus' => 1.25,
			'seniority'    => 0.5,
			'freshness'    => 1,
			'consistency'  => 1,
			'issues'       => 0.75,
			'coverage'     => 0.5,
		), array(
			'travisStatus' => 2,
			'seniority'    => 'seniority',
			'freshness'    => 'freshness',
			'consistency'  => 'consistency',
			'issues'       => 'issues',
			'coverage'     => 100,
		), 0);
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// HELPERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Compute an index
	 *
	 * @param  string $attribute
	 * @param  array  $weights
	 * @param  array  $ceilings
	 * @param integer $rounding
	 *
	 * @return void
	 */
	protected function computeIndexes($attribute, $weights, $ceilings, $rounding = 2)
	{
		// Fetch ceiling from repository if none is specified
		foreach ($ceilings as $name => $value) {
			if (is_string($value)) {
				$ceilings[$name] = $this->packages->max($value);
			}
		}

		// Compute indexes
		foreach ($this->packages->all() as $package) {
			foreach ($ceilings as $index => $value) {
				$indexes[$index] = ($index == 'freshness')
					? (($package->$index * 100 / -$value) + 100) * $weights[$index]
					: $indexes[$index] = ($package->$index * 100 / $value) * $weights[$index];
			}

			// Round and save
			$package->update(array(
				$attribute => round(array_sum($indexes) / array_sum($weights), $rounding)
			));
		}
	}
}
