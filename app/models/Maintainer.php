<?php
class Maintainer extends Eloquent
{

	/**
	 * Get all of the Maintainer's repositories
	 *
	 * @return Collection
	 */
	public function repositories()
	{
		return $this->belongsToMany('Package');
	}

}