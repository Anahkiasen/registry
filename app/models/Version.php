<?php
class Version extends Eloquent
{
	public function getKeywordsAttribute()
	{
		return json_decode($this->getOriginal('keywords'), true);
	}
}