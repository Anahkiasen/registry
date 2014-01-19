<?php
namespace Registry\Traits;

/**
 * A model with JSONified keywords
 */
trait HasKeywords
{
	/**
	 * Save keywords as JSON
	 *
	 * @param array $keywords
	 */
	public function setKeywordsAttribute($keywords)
	{
		$this->setJsonAttribute('keywords', $keywords);
	}

	/**
	 * Get keywords as an array
	 *
	 * @return array
	 */
	public function getKeywordsAttribute()
	{
		$keywords = $this->getJsonAttribute('keywords');

		// Filter out redundant keywords
		$keywords = array_filter($keywords, function ($value) {
			return !in_array($value, array('laravel', 'illuminate', 'L4', 'Laravel 4', 'laravel4', 'laravel-4'));
		});

		return $keywords;
	}
}
