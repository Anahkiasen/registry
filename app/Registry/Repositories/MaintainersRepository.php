<?php
namespace Registry\Repositories;

use DB;
use Registry\Maintainer;
use Registry\Abstracts\AbstractRepository;

/**
* The Maintainers Repository
*/
class MaintainersRepository extends AbstractRepository
{
	/**
	 * The base Model
	 *
	 * @var Maintainer
	 */
	protected $entries;

	/**
	 * Build a new Maintainers Repository
	 *
	 * @param Maintainer $maintainers
	 */
	public function __construct(Maintainer $maintainers)
	{
		$this->entries = $maintainers;
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////// GLOBAL QUERIES ////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Return all Maintainers
	 *
	 * @return Collection
	 */
	public function all()
	{
		return $this->entries->get();
	}

	/**
	 * Get all maintainers, sorted by aggregated popularity
	 *
	 * @return Collection
	 */
	public function popular()
	{
		$maintainers = $this->entries->with('packages.versions')->get();
		$maintainers->sortBy(function($maintainer) {
			return $maintainer->popularity * -1;
		});

		return $maintainers;
	}

	/**
	 * Flush all Maintainers
	 *
	 * @return boolean
	 */
	public function flush()
	{
		return parent::flush() and DB::table('maintainer_package')->truncate();
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
		return $this->entries->with('packages.versions')->whereSlug($slug)->firstOrFail();
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
	 * Update or create a Maintainer by its attributes
	 *
	 * @param  array $attributes
	 *
	 * @return Maintainer
	 */
	public function updateOrCreate($attributes)
	{
		$maintainer = $this->findOrCreate($attributes);
		$maintainer->fill($attributes)->save();

		return $maintainer;
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
		return $this->entries->whereName($name)->first();
	}
}
