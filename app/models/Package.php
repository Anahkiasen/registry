<?php
class Package extends Eloquent
{

	/**
	 * The raw informations
	 *
	 * @var Packagist\Api\Result\Package
	 */
	protected $informations;

	/**
	 * Get all of the Package's Maintainers
	 *
	 * @return Collection
	 */
	public function maintainers()
	{
		return $this->belongsToMany('Maintainer');
	}

	/**
	 * Get all of the Package's versions
	 *
	 * @return Collection
	 */
	public function versions()
	{
		return $this->hasMany('Version');
	}

	/**
	 * Get the informations of a package
	 *
	 * @return object
	 */
	public function getInformations()
	{
		if (!$this->informations) {
			$this->informations = Cache::remember($this->name, 1440, function() {
				return App::make('packagist')->get($this->name);
			});
		}

		return $this->informations;
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// ATTRIBUTES ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the total number of downloads
	 *
	 * @return integer
	 */
	public function getDownloadsAttribute()
	{
		return (int) $this->getInformations()->getDownloads()->getTotal();
	}

	/**
	 * Get tags as an array
	 *
	 * @return array
	 */
	public function getTagsAttribute()
	{
		if ($this->versions->isEmpty()) return array();

		return $this->versions[0]->keywords;
	}

	/**
	 * Get Maintainers as a string list
	 *
	 * @return string
	 */
	public function getMaintainersListAttribute()
	{
		$list = array();
		$maintainers = $this->maintainers;
		foreach ($maintainers as $maintainer) {
			$list[] = $maintainer->__toString();
		}

		return implode(', ', $list);
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// SCOPES ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Fetch only components
	 *
	 * @param  Query $query
	 *
	 * @return Query
	 */
	public function scopeComponent($query)
	{
		return $query->where('type', 'component');
	}

	/**
	 * Fetch only packages
	 *
	 * @param  Query $query
	 *
	 * @return Query
	 */
	public function scopePackage($query)
	{
		return $query->where('type', 'package');
	}

}