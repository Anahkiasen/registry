<?php

/**
 * The Maintainer of a Package
 */
class Maintainer extends Eloquent
{
	use Traits\Gravatar;

	////////////////////////////////////////////////////////////////////
	//////////////////////////// RELATIONSHIPS /////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get all of the Maintainer's packages, by most populart first
	 *
	 * @return Collection
	 */
	public function packages()
	{
		return $this->belongsToMany('Package')->popular();
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// ATTRIBUTES ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the Maintainer's popularity
	 *
	 * @return integer
	 */
	public function getPopularityAttribute()
	{
		// Get all packages with a decent popularity
		$packages   = array_pluck($this->packages->toArray(), 'popularity');
		$popularity = array_filter($packages, function($package) {
			return $package > 0.5;
		});

		// Cancel fitler if empty
		if (empty($popularity)) {
			$popularity = $packages;
		}

		// Compute average popularity
		$popularity = array_sum($popularity) / sizeof($popularity);
		$popularity = round($popularity, 2);

		return $popularity;
	}

	/**
	 * Get number of packages
	 *
	 * @return integer
	 */
	public function getPackagesNumberAttribute()
	{
		return sizeof($this->packages);
	}

	/**
	 * Get the Maintainer as a string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return HTML::linkAction('MaintainersController@maintainer', $this->name, $this->slug);
	}
}
