<?php

/**
 * A Package in the registry
 */
class Package extends Eloquent
{
	////////////////////////////////////////////////////////////////////
	//////////////////////////// RELATIONSHIPS /////////////////////////
	////////////////////////////////////////////////////////////////////

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
	 * Get all of the Package's Versions
	 *
	 * @return Collection
	 */
	public function versions()
	{
		return $this->hasMany('Version')->latest();
	}

	/**
	 * Get the Package's Comments
	 *
	 * @return Collection
	 */
	public function comments()
	{
		return $this->hasMany('Comment');
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////// RAW INFORMATIONS ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the Packagist informations of a package
	 *
	 * @return object
	 */
	public function getPackagist()
	{
		return (object) $this->getFromApi('guzzle', '/packages/'.$this->name.'.json')['package'];
	}

	/**
	 * Get the Github informations of a package
	 *
	 * @return array
	 */
	public function getRepository()
	{
		extract(Config::get('registry.api.github'));
		$url    = $this->repositoryName.'?client_id=' .$id. '&client_secret='.$secret;
		$source = Str::contains($this->repository, 'github') ? 'github' : 'bitbucket';

		return $this->getFromApi($source, $url);
	}

	/**
	 * Get the issues of a package
	 *
	 * @return array
	 */
	public function getRepositoryIssues()
	{
		extract(Config::get('registry.api.github'));
		$url    = $this->repositoryName.'/issues?client_id=' .$id. '&client_secret='.$secret;
		$source = Str::contains($this->repository, 'github') ? 'github' : 'bitbucket';

		return $this->getFromApi($source, $url);
	}

	/**
	 * Get Travis informations
	 *
	 * @return array
	 */
	public function getTravis()
	{
		return $this->getFromApi('travis', $this->travis);
	}

	/**
	 * Get Travis builds
	 *
	 * @return array
	 */
	public function getTravisBuilds()
	{
		return $this->getFromApi('travis', $this->travis.'/builds');
	}

	/**
	 * Get Scrutinizer
	 *
	 * @return array
	 */
	public function getScrutinizer()
	{
		return $this->getFromApi('scrutinizer', $this->repositoryName.'/metrics');
	}

	/**
	 * Get informations from an API
	 *
	 * @param  string $source       Source
	 * @param  string $url          The endpoint
	 *
	 * @return array
	 */
	protected function getFromApi($source, $url)
	{
		return Cache::rememberForever($url, function() use ($source, $url) {
			try {
				$informations = App::make($source)->get($url)->send()->json();
			} catch (Exception $e) {
				$informations = array();
			}

			return $informations;
		});
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// ATTRIBUTES ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the shorthand name for the package
	 *
	 * @return string  A vendor/package string
	 */
	public function getRepositoryNameAttribute()
	{
		$name = explode('/', $this->repository);
		$name = $name[3].'/'.$name[4];

		return $name;
	}

	/**
	 * Get the Travis status of the Package
	 *
	 * @return string
	 */
	public function getTravisBuildAttribute()
	{
		$status = array('unknown', 'failing', 'passing');

		return $status[(int) $this->build_status];
	}

	/**
	 * Get tags as an array
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

	/**
	 * Get Maintainers as a string list
	 *
	 * @return string
	 */
	public function getMaintainersListAttribute()
	{
		$list = array();
		foreach ($this->maintainers as $maintainer) {
			$list[] = $maintainer->__toString();
		}

		return implode(', ', $list);
	}

	/**
	 * The DateTime fields of the model
	 *
	 * @return array
	 */
	public function getDates()
	{
		return array_merge(parent::getDates(), array('pushed_at'));
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// QUERY SCOPES /////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get packages by most popular
	 *
	 * @param  Query $query
	 *
	 * @return Query
	 */
	public function scopePopular($query)
	{
		return $query->orderBy('popularity', 'DESC');
	}

	/**
	 * Return packages similar to another one
	 *
	 * @param  Query $query
	 * @param  Package $package
	 *
	 * @return Query
	 */
	public function scopeSimilar($query, $package)
	{
		return $query->where('name', '!=', $package->name)->where(function($query) use ($package) {
			foreach ($package->keywords as $keyword) {
				$query->orWhere('keywords', 'LIKE', "%$keyword%");
			}
		});
	}

}
