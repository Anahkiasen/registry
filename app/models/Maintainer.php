<?php
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

/**
 * The Maintainer of a Package
 */
class Maintainer extends Eloquent implements UserInterface, RemindableInterface
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
	////////////////////////// AUTHENTIFICATION ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
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
