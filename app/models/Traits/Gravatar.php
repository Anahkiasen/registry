<?php
namespace Traits;

/**
 * A model that has a gravatar
 */
trait Gravatar
{
	/**
	 * Get the Maintainer's Gravatar
	 *
	 * @return string
	 */
	public function getGravatarAttribute()
	{
		$hash    = md5(strtolower(trim($this->email)));
		$default = 'http://registry.autopergamene.eu/app/img/placeholder.png';
		$size    = 160;

		return sprintf('http://www.gravatar.com/avatar/%s?d=%s&s=%s', $hash, $default, $size);
	}
}
