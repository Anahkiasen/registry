<?php
class Version extends Eloquent
{

	/**
	 * Get the keywords of a Version
	 *
	 * @return array
	 */
	public function getKeywordsAttribute()
	{
		return json_decode($this->getOriginal('keywords'), true);
	}

}
