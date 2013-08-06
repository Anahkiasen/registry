<?php
namespace Registry;

use Registry\Abstracts\AbstractModel;

/**
 * A Comment on a Package
 */
class Comment extends AbstractModel
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = array('content', 'maintainer_id', 'package_id');

	/**
	 * Get the author of the Comment
	 *
	 * @return User
	 */
	public function maintainer()
	{
		return $this->belongsTo('Registry\Maintainer');
	}
}
