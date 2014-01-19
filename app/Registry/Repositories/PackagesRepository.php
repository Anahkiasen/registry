<?php
namespace Registry\Repositories;

use Registry\Package;
use Registry\Abstracts\AbstractRepository;

/**
* The Packages Repository
*/
class PackagesRepository extends AbstractRepository
{
	/**
	 * Build a new packages Repository
	 *
	 * @param Package $packages
	 */
	public function __construct(Package $packages)
	{
		$this->items = $packages;
	}

	////////////////////////////////////////////////////////////////////
	////////////////////////////// STATISTICS //////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get the minimal value for an attribute
	 *
	 * @param  string $attribute
	 *
	 * @return integer
	 */
	public function min($attribute)
	{
		return $this->items->whereType('package')->min($attribute);
	}

	/**
	 * Get the maximal value for an attribute
	 *
	 * @param  string $attribute
	 *
	 * @return integer
	 */
	public function max($attribute)
	{
		return $this->items->whereType('package')->max($attribute);
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////// GLOBAL QUERIES ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get all packages
	 *
	 * @param integer|null $perPage
	 *
	 * @return Collection
	 */
	public function all($perPage = NULL)
	{
		return parent::all($perPage)->load('maintainers');
	}

	/**
	 * Get all "packages" packages, sorted by popularity
	 *
	 * @return Collection
	 */
	public function popular()
	{
		return $this->items->with('maintainers', 'versions')->whereType('package')->orderBy('popularity', 'DESC')->get();
	}

	/**
	 * Get all packages, sorted by latest first
	 *
	 * @return Collection
	 */
	public function latest()
	{
		return $this->items->with('maintainers')->orderBy('created_at', 'DESC')->get();
	}

	/**
	 * Get all packages, sorted by oldest first
	 *
	 * @return Collection
	 */
	public function oldest()
	{
		return $this->items->with('maintainers')->orderBy('created_at')->get();
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////// SINGLE-RESULT QUERIES /////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Find a package by its slug
	 *
	 * @param  string $slug
	 *
	 * @return Package
	 */
	public function findBySlug($slug)
	{
		return $this->items->with('versions', 'comments.maintainer')->whereSlug($slug)->firstOrFail();
	}

	/**
	 * Get all packages similar to another one
	 *
	 * @param  Package $package
	 *
	 * @return Collection
	 */
	public function findSimilarTo(Package $package)
	{
		$similar = $this->items->with('versions')->similar($package)->take(5)->get();

		// Sort by popularity and number of tags in common
		$similar->sortBy(function ($similarPackage) use ($package) {
			return $similarPackage->popularity + sizeof(array_intersect($similarPackage->keywords, $package->keywords)) * -1;
		});

		return $similar;
	}
}
