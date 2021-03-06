<?php
namespace Registry;

use Arrounded\Traits\Authentifiable;
use Config;
use HTML;
use Illuminate\Auth\UserInterface;
use Registry\Abstracts\AbstractModel;
use Arrounded\Traits\Sluggable;

/**
 * The Maintainer of a Package
 */
class Maintainer extends AbstractModel implements UserInterface
{
	use Authentifiable;
	use Sluggable;
	use Traits\Gravatar;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = array(
		'name',
		'email',
		'github',
		'homepage',
	);

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = array(
		'stars',
		'pivot',
		'columns',
		'created_at',
		'updated_at',
	);

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
		$this->setJsonAttribute('columns', $columns);
	}

	/**
	 * Decode columns to array
	 *
	 * @return array
	 */
	public function getColumnsAttribute()
	{
		return $this->getJsonAttribute('columns') ?: Config::get('registry.columns');
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
		if (empty($popularity)) {
			return 0;
		}

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
