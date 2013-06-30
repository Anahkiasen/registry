<?php
use Carbon\Carbon;

class Version extends Eloquent
{

	////////////////////////////////////////////////////////////////////
	////////////////////////////// ATTRIBUTES //////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the keywords of a Version
	 *
	 * @return array
	 */
	public function getKeywordsAttribute()
	{
		return json_decode($this->getOriginal('keywords'), true);
	}

	/**
	 * Get relative date
	 *
	 * @return Carbon
	 */
	public function getRelativeDateAttribute()
	{
		return $this->created_at->diffForHumans();
	}

}
