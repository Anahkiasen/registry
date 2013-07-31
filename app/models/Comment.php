<?php

/**
 * A Comment on a Package
 */
class Comment extends Eloquent
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = array('content', 'user_id', 'package_id');

	/**
	 * Get the author of the Comment
	 *
	 * @return User
	 */
	public function maintainer()
	{
		return $this->belongsTo('Maintainer');
	}
}