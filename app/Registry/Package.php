<?php
namespace Registry;

use App;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Registry\Abstracts\AbstractModel;
use Registry\Services\PackagesEndpoints;
use dflydev\markdown\MarkdownExtraParser;

/**
 * A Package in the registry
 */
class Package extends AbstractModel
{
	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = array(
		'illuminate', 'updated_at',
	);

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
		return $this->belongsToMany('Registry\Maintainer');
	}

	/**
	 * Get all of the Package's Versions
	 *
	 * @return Collection
	 */
	public function versions()
	{
		return $this->hasMany('Registry\Version')->latest();
	}

	/**
	 * Get the Package's Comments
	 *
	 * @return Collection
	 */
	public function comments()
	{
		return $this->hasMany('Registry\Comment');
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
		return $this->getFromApi('guzzle', '/packages/'.$this->name.'.json')['package'];
	}

	/**
	 * Get the Github informations of a package
	 *
	 * @return array
	 */
	public function getRepository()
	{
		return App::make('endpoints')->getRepository($this);
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
		return $this->getFromApi('scrutinizer', $this->travis.'/metrics');
	}

	/**
	 * Get a PackagesEndpoints Service
	 *
	 * @param  string $source
	 * @param  string $url
	 *
	 * @return PackagesEndpoints
	 */
	protected function getFromApi($source, $url)
	{
		$data = App::make('endpoints')->getFromApi($this, $source, $url);

		return new Collection($data);
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// ATTRIBUTES ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the Laravel requirement for the package
	 *
	 * @return string
	 */
	public function getRequirementAttribute()
	{
		if ($this->laravel) {
			return $this->laravel;
		}

		// Get Laravel requirement
		$version = $this->getPackagist()['versions'];
		$version = array_get($version, key($version).'.require');
		$laravel = array_get($version, 'laravel/framework');
		if (!$laravel) $laravel = array_get($version, 'illuminate/support');
		if (!$laravel) $laravel = '>=4.0.0';

		// Unify wildcards
		$laravel = str_replace('x', '*', $laravel);

		// Remove special flags
		$laravel = preg_replace('#(.+)[\-@](dev|BETA[0-9])#', '$1', $laravel);

		// Transcribe major wildcards to versions
		$laravel = preg_replace('#^\*$#', '>=4.0.0', $laravel);
		$laravel = str_replace('dev-master', '>=4.1.0', $laravel);
		$laravel = str_replace('v', null, $laravel);

		// Transcribe next version wildcards
		$laravel = preg_replace('#>=4.0$#', '~4', $laravel);
		$laravel = preg_replace('#^4.*$#', '~4.0', $laravel);
		$laravel = preg_replace('#^4.0.*$#', '~4.0.0', $laravel);

		$laravel = preg_replace('#^~4$#', '>=4.0.0', $laravel);
		$laravel = preg_replace('#^~4.0$#', '>=4.0.0,<5.0.0', $laravel);
		$laravel = preg_replace('#^~4.0.0$#', '>=4.0.0,<4.1.0', $laravel);

		return $laravel;
	}

	/**
	 * Get the parsed Markdown of the README
	 *
	 * @return string
	 */
	public function getReadmeAttribute()
	{
		$languages = 'php|javascript|css|json';

		// Get README and replace fenced blocks
		$readme = $this->getOriginal('readme');
		$readme = preg_replace('#```(' .$languages. ')?#', "~~~\n$1", $readme);

		// Convert Markdown
		$parser = new MarkdownExtraParser;
		$readme = $parser->transformMarkdown($readme);

		// Add syntax highlighting
		$readme = preg_replace("#<pre><code>(" .$languages. ")\n#", '<pre><code data-language="$1">', $readme);
		$readme = preg_replace_callback("#\n(    ){1,}#", function ($matches) {
			$tabs = (strlen($matches[0]) - 1) / 4;

			return "\n".str_repeat("\t", $tabs);
		}, $readme);

		return $readme;
	}

	/**
	 * Get only the vendor
	 *
	 * @return string
	 */
	public function getVendorAttribute()
	{
		return explode('/', $this->travis)[0];
	}

	/**
	 * Get only the package
	 *
	 * @return string
	 */
	public function getPackageAttribute()
	{
		return explode('/', $this->travis)[1];
	}

	/**
	 * Get the Package's Keywords
	 *
	 * @return Collection
	 */
	public function getKeywordsAttribute()
	{
		if ($this->versions->isEmpty()) {
			return array();
		}

		return $this->versions->first()->keywords;
	}

	/**
	 * Keep the slug in sync with the repository
	 *
	 * @param string $repository
	 */
	public function setRepositoryAttribute($repository)
	{
		list($package, $vendor) = array_reverse(explode('/', $repository));

		$this->attributes['repository'] = $repository;
		$this->attributes['travis']     = $vendor.'/'.$package;
		$this->attributes['slug']       = Str::slug($vendor.'-'.$package);
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
	 * Get Maintainers as a string list
	 *
	 * @return string
	 */
	public function getMaintainersListAttribute()
	{
		return $this->maintainers->implode('link', ', ');
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

	////////////////////////////////////////////////////////////////////
	///////////////////////////// SERIALIZATION ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$package = parent::toArray();
		$package['keywords'] = json_decode(array_get($package, 'keywords.0'));

		return $package;
	}

}
