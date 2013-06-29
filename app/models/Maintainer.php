<?php
class Maintainer extends Eloquent
{

	/**
	 * Get all of the Maintainer's repositories
	 *
	 * @return Collection
	 */
	public function packages()
	{
		return $this->belongsToMany('Package')->orderBy('downloads_total', 'DESC');
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
