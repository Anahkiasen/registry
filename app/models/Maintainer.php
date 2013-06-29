<?php
class Maintainer extends Eloquent
{

	////////////////////////////////////////////////////////////////////
	//////////////////////////// RELATIONSHIPS /////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get all of the Maintainer's repositories
	 *
	 * @return Collection
	 */
	public function packages()
	{
		return $this->belongsToMany('Package')->orderBy('popularity', 'DESC');
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// ATTRIBUTES ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the Maintainer's Gravatar
	 *
	 * @return string
	 */
	public function getGravatarAttribute()
	{
		$hash    = md5(strtolower(trim($this->email)));
		$default = URL::asset('app/img/box.svg');
		$size    = 160;

		return sprintf('http://www.gravatar.com/avatar/%s?d=%s&s=%s', $hash, $default, $size);
	}

	/**
	 * Get the Maintainer as a string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return HTML::linkAction('PackagesController@maintainer', $this->name, $this->slug);
	}

}
