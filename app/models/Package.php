<?php
use Carbon\Carbon;

class Package extends Eloquent
{

	/**
	 * The raw informations
	 *
	 * @var array
	 */
	protected $informations = array();

	/**
	 * Get all of the Package's Maintainers
	 *
	 * @return Collection
	 */
	public function maintainers()
	{
		return $this->belongsToMany('Maintainer');
	}

	/**
	 * Get all of the Package's versions
	 *
	 * @return Collection
	 */
	public function versions()
	{
		return $this->hasMany('Version')->orderBy('created_at', 'DESC');
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////// RAW INFORMATIONS ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get raw informations
	 *
	 * @return array
	 */
	protected function getInformations($source)
	{
		// Check if we have the informations in cache
		if (!array_key_exists($source, $this->informations)) {

			// Otherwise fetch them
			switch ($source) {
				case 'packagist':
					$this->informations['packagist'] = Cache::rememberForever($this->name.'-packagist', function() {
						$informations = App::make('guzzle')->get('/packages/'.$this->name.'.json')->send()->json();
						return (object) $informations['package'];
					});
					break;

				case 'repository':
					$this->informations['repository'] = Cache::rememberForever($this->name.'-repository', function() {
						$source = str_contains($this->repository, 'github') ? 'github' : 'bitbucket';
						$name   = explode('/', $this->repository);
						$name   = $name[3].'/'.$name[4];

						try {
							$informations = App::make($source)->get($name.'?client_id=376e127206f9a567e4c2&client_secret=cc9b32c88bf79ffbe84d72e996b85f78eb8b89f5')->send()->json();
						}
						catch (Exception $e) {
							$informations = array();
						}

						return $informations;
					});
					break;
			}
		}

		return $this->informations[$source];
	}

	/**
	 * Get the Packagist informations of a package
	 *
	 * @return object
	 */
	public function getPackagist()
	{
		return $this->getInformations('packagist');
	}

	/**
	 * Get the Github informations of a package
	 *
	 * @return object
	 */
	public function getRepository()
	{
		return $this->getInformations('repository');
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// ATTRIBUTES ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get Travis badge
	 *
	 * @return string
	 */
	public function getTravisAttribute()
	{
		$repository = explode('/', $this->repository);
		$travis = $repository[3].'/'.str_replace('.git', null, $repository[4]);

		return HTML::image('https://secure.travis-ci.org/' .$travis. '.png');
	}

	/**
	 * Get relative date
	 *
	 * @return Carbon
	 */
	public function getRelativeDateAttribute()
	{
		return $this->versions[0]->created_at->diffForHumans(Carbon::now());
	}

	/**
	 * Get tags as an array
	 *
	 * @return array
	 */
	public function getTagsAttribute()
	{
		if ($this->versions->isEmpty()) return array();

		$tags = $this->versions[0]->keywords;
		$tags = array_filter($tags, function($value) {
			return !in_array($value, array('laravel', 'illuminate', 'L4', 'Laravel 4', 'laravel4', 'laravel-4'));
		});

		return (array) $tags;
	}

	/**
	 * Get Maintainers as a string list
	 *
	 * @return string
	 */
	public function getMaintainersListAttribute()
	{
		$list = array();
		$maintainers = $this->maintainers;
		foreach ($maintainers as $maintainer) {
			$list[] = $maintainer->__toString();
		}

		return implode(', ', $list);
	}

}
