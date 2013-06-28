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

	/**
	 * Get the Maintainer as a string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return HTML::link($this->github, $this->name, array('target' => '_blank'));
	}

}