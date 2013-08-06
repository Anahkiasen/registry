<?php
namespace Registry\Repositories;

use Registry\Package;

/**
* The Packages Repository
*/
class PackagesRepository
{
	/**
	 * The base Model
	 *
	 * @var Package
	 */
	protected $packages;

	/**
	 * Build a new packages Repository
	 *
	 * @param Package $packages
	 */
	function __construct(Package $packages)
	{
		$this->packages = $packages;
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////// GLOBAL QUERIES ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get all packages, sorted by popularity
	 *
	 * @return Collection
	 */
	public function all()
	{
		return $this->packages->with('maintainers')->whereType('package')->latest('popularity')->get();
	}

	/**
	 * Get all packages, sorted by oldest first
	 *
	 * @return Collection
	 */
	public function oldest()
	{
		return $this->packages->oldest()->get();
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
		return $this->packages->with('versions', 'comments.maintainer')->whereSlug($slug)->firstOrFail();
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
		$similar = $this->packages->with('versions')->similar($package)->take(5)->get();

		// Sort by popularity and number of tags in common
		$similar->sortBy(function($similarPackage) use ($package) {
			return $similarPackage->popularity + sizeof(array_intersect($similarPackage->keywords, $package->keywords)) * -1;
		});

		return $similar;
	}
}