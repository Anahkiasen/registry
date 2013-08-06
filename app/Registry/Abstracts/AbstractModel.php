<?php
namespace Registry\Abstracts;

use Eloquent;

abstract class AbstractModel extends Eloquent
{
	/**
	 * Check if an attribute exists on the model
	 *
	 * @param  string  $key
	 *
	 * @return boolean
	 */
	public function __isset($key)
	{
		if ($this->hasGetMutator($key)) {
			return true;
		}

		return parent::__isset($key);
	}
}
