<?php
namespace Registry;

use Config;
use HTML;
use Illuminate\Auth\UserInterface;
use Registry\Abstracts\AbstractModel;

/**
 * The Maintainer of a Package
 */
class Maintainer extends AbstractModel implements UserInterface
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
		return $this->belongsToMany('Registry\Package')->popular();
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

	////////////////////////////////////////////////////////////////////
	///////////////////////////// ATTRIBUTES ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get a link to the Maintainer
	 *
	 * @return string
	 */
	public function getLinkAttribute()
	{
		return HTML::linkRoute('maintainer', $this->name, $this->slug);
	}

	/**
	 * Save the columns as JSON
	 *
	 * @param array $columns
	 */
	public function setColumnsAttribute($columns)
	{
		$this->attributes['columns'] = json_encode($columns);
	}

	/**
	 * Decode columns to array
	 *
	 * @return array
	 */
	public function getColumnsAttribute()
	{
		$columns = $this->getOriginal('columns');
		if (!$columns) {
			return Config::get('registry.columns');
		}

		return json_decode($columns, true);
	}

	/**
	 * Get the Maintainer's popularity
	 *
	 * @return integer
	 */
	public function getPopularityAttribute()
	{
		// Get all packages with a decent popularity
		$popularity = $this->packages->lists('popularity');

		// Compute average popularity
		$popularity = array_sum($popularity) / sizeof($popularity);
		$popularity = round($popularity, 2);

		return $popularity;
	}

	/**
	 * Get the Maintainer as a string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->link;
	}
}
