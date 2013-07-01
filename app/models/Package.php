<?php
use Carbon\Carbon;
use Guzzle\Http\Exception\ClientErrorResponseException;

class Package extends Eloquent
{

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
	 * Get the Packagist informations of a package
	 *
	 * @return object
	 */
	public function getPackagist()
	{
		return Cache::rememberForever($this->name.'-packagist', function() {
			$informations = App::make('guzzle')->get('/packages/'.$this->name.'.json')->send()->json();
			return (object) $informations['package'];
		});
	}

	/**
	 * Get the Github informations of a package
	 *
	 * @return object
	 */
	public function getRepository()
	{
		return Cache::rememberForever($this->name.'-repository', function() {
			try {
				$credentials  = Config::get('registry.api.github');
				$informations = App::make($this->source)->get($this->repositoryName.'?client_id=' .$credentials['id']. '&client_secret='.$credentials['secret'])->send()->json();
			} catch (Exception $e) {
				$informations = array();
			}

			return $informations;
		});
	}

	/**
	 * Get the issues of a package
	 *
	 * @return array
	 */
	public function getRepositoryIssues()
	{
		return Cache::rememberForever($this->name.'-repository-issues', function() {
			try {
				$credentials = Config::get('registry.api.github');
				$issues      = App::make($this->source)->get($this->repositoryName.'/issues?client_id=' .$credentials['id']. '&client_secret='.$credentials['secret'])->send()->json();
			} catch (Exception $e) {
				$issues = array();
			}

			return $issues;
		});
	}

	/**
	 * Get Travis informations
	 *
	 * @return array
	 */
	public function getTravis()
	{
		return Cache::rememberForever($this->travis.'-travis', function() {
			try {
				return App::make('travis')->get($this->travis)->send()->json();
			} catch (ClientErrorResponseException $e) {
				return array();
			}
		});
	}

	/**
	 * Get Travis builds
	 *
	 * @return array
	 */
	public function getTravisBuilds()
	{
		return Cache::rememberForever($this->travis.'-travis-builds', function() {
			return App::make('travis')->get($this->travis.'/builds')->send()->json();
		});
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// ATTRIBUTES ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the source name of a Package's repository
	 *
	 * @return string
	 */
	public function getSourceAttribute()
	{
		return str_contains($this->repository, 'github') ? 'github' : 'bitbucket';
	}

	/**
	 * Get the Repository name
	 *
	 * @return string
	 */
	public function getRepositoryNameAttribute()
	{
		$name = explode('/', $this->repository);
		$name = $name[3].'/'.$name[4];

		return $name;
	}

	/**
	 * Get travis badge
	 *
	 * @return string
	 */
	public function getTravisBuildAttribute()
	{
		$status = array('unknown', 'failing', 'passing');

		return $status[(int) $this->build_status];
	}

	/**
	 * Get relative date
	 *
	 * @return Carbon
	 */
	public function getRelativeDateAttribute()
	{
		return $this->versions[0]->relativeDate;
	}

	/**
	 * Get tags as an array
	 *
	 * @return array
	 */
	public function getKeywordsAttribute()
	{
		$keywords = (array) json_decode($this->getOriginal('keywords'), true);
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
		$maintainers = $this->maintainers;
		foreach ($maintainers as $maintainer) {
			$list[] = $maintainer->__toString();
		}

		return implode(', ', $list);
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

}
