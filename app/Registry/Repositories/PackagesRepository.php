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
	 * The base Model
	 *
	 * @var Package
	 */
	protected $entries;

	/**
	 * Build a new packages Repository
	 *
	 * @param Package $packages
	 */
	public function __construct(Package $packages)
	{
		$this->entries = $packages;
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////// GLOBAL QUERIES ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get all packages
	 *
	 * @return Collection
	 */
	public function all()
	{
		return $this->entries->with('maintainers')->get();
	}

	/**
	 * Get all "packages" packages, sorted by popularity
	 *
	 * @return Collection
	 */
	public function packages()
	{
		return $this->entries->with('maintainers')->whereType('package')->latest('popularity')->get();
	}

	/**
	 * Get all packages, sorted by oldest first
	 *
	 * @return Collection
	 */
	public function oldest()
	{
		return $this->entries->oldest()->get();
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
		return $this->entries->with('versions', 'comments.maintainer')->whereSlug($slug)->firstOrFail();
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
		$similar = $this->entries->with('versions')->similar($package)->take(5)->get();

		// Sort by popularity and number of tags in common
		$similar->sortBy(function($similarPackage) use ($package) {
			return $similarPackage->popularity + sizeof(array_intersect($similarPackage->keywords, $package->keywords)) * -1;
		});

		return $similar;
	}
}
