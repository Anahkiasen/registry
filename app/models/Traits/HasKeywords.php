<?php
namespace Traits;

/**
 * A model with JSONified keywords
 */
trait HasKeywords
{
	/**
	 * Get keywords as an array
	 *
	 * @return array
	 */
	public function getKeywordsAttribute()
	{
		$keywords = $this->getOriginal('keywords');
		$keywords = (array) json_decode($keywords, true);

		// Filter out redundant keywords
		$keywords = array_filter($keywords, function($value) {
			return !in_array($value, array('laravel', 'illuminate', 'L4', 'Laravel 4', 'laravel4', 'laravel-4'));
		});

		return $keywords;
	}
}
