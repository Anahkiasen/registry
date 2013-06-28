<?php
class Package extends Eloquent
{

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