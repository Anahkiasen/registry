<?php
namespace Registry\Repositories;

use Registry\Maintainer;

/**
* The Maintainers Repository
*/
class MaintainersRepository
{
	/**
	 * The base Model
	 *
	 * @var Maintainer
	 */
	protected $maintainers;

	/**
	 * Build a new Maintainers Repository
	 *
	 * @param Maintainer $maintainers
	 */
	public function __construct(Maintainer $maintainers)
	{
		$this->maintainers = $maintainers;
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////// GLOBAL QUERIES ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get all maintainers, sorted by aggregated popularity
	 *
	 * @return Collection
	 */
	public function all()
	{
		$maintainers = $this->maintainers->with('packages.versions')->get();
		$maintainers = array_sort($maintainers, function($maintainer) {
			return $maintainer->popularity * -1;
		});

		return $maintainers;
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////// SINGLE-RESULT QUERIES /////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Get a Maintainer by its slug
	 *
	 * @param  string $slug
	 *
	 * @return Maintainer
	 */
	public function findBySlug($slug)
	{
		return $this->maintainers->with('packages.versions')->whereSlug($slug)->firstOrFail();
	}

	/**
	 * Find or create a Maintainer by its attributes
	 *
	 * @param  array $maintainer
	 *
	 * @return Maintainer
	 */
	public function findOrCreate($maintainer)
	{
		$name = array_get($maintainer, 'name');
		$existingMaintainer = $this->lookup($name);

		// Create model if it doesn't already
		if (!$existingMaintainer) {
			$existingMaintainer = $this->create($maintainer);
		}

		return $existingMaintainer;
	}

	/**
	 * Lookup an user by name
	 *
	 * @param  string $name
	 *
	 * @return Maintainer|null
	 */
	public function lookup($name)
	{
		return $this->maintainers->whereName($name)->first();
	}

	////////////////////////////////////////////////////////////////////
	///////////////////////////// MODEL FLOW ///////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Return an instance of Maintainer
	 *
	 * @param  array  $attributes
	 *
	 * @return Maintainer
	 */
	public function create(array $attributes)
	{
		return $this->maintainers->create($attributes);
	}
}
