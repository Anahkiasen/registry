<?php
use Carbon\Carbon;
use Guzzle\Http\Exception\ClientErrorResponseException;

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
	 * Get the Packagist informations of a package
	 *
	 * @return object
	 */
	public function getPackagist()
	{
		if (!array_key_exists('packagist', $this->informations)) {
			$this->informations['packagist'] = Cache::rememberForever($this->name.'-packagist', function() {
				$informations = App::make('guzzle')->get('/packages/'.$this->name.'.json')->send()->json();
				return (object) $informations['package'];
			});
		}

		return $this->informations['packagist'];
	}

	/**
	 * Get the Github informations of a package
	 *
	 * @return object
	 */
	public function getRepository()
	{
		if (!array_key_exists('repository', $this->informations)) {
			$this->informations['repository'] = Cache::rememberForever($this->name.'-repository', function() {
				$source = str_contains($this->repository, 'github') ? 'github' : 'bitbucket';
				$name   = explode('/', $this->repository);
				$name   = $name[3].'/'.$name[4];

				try {
					$credentials  = Config::get('registry.api.github');
					$informations = App::make($source)->get($name.'?client_id=' .$credentials['id']. '&client_secret='.$credentials['secret'])->send()->json();
				} catch (Exception $e) {
					$informations = array();
				}

				return $informations;
			});
		}

		return $this->informations['repository'];
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// ATTRIBUTES ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get Travis status
	 *
	 * @return integer
	 */
	public function getTravisStatusAttribute()
	{
		if (!$this->travis) {
			return null;
		}

		// Get build status
		$status = Cache::rememberForever($this->travis.'-travis', function() {
			try {
				return App::make('travis')->get($travis)->send()->json();
			}	catch(ClientErrorResponseException $e) {
				return array();
			}
		});

		// Invert scale
		$status = array_get($status, 'last_build_status', 2);
		$status = (int) abs($status - 2);

		return $status;
	}

	/**
	 * Get travis badge
	 *
	 * @return string
	 */
	public function getTravisAttribute()
	{
		$status = array('unknown', 'failing', 'success');

		return $status[$this->travisStatus];
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
