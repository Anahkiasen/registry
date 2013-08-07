<?php
namespace Registry\Repositories;

use Registry\Abstracts\AbstractRepository;
use Registry\Version;

/**
* The Versions Repository
*/
class VersionsRepository extends AbstractRepository
{
	/**
	 * The base Model
	 *
	 * @var Version
	 */
	protected $entries;

	/**
	 * Build a new Versions Repository
	 *
	 * @param Version $Versions
	 */
	public function __construct(Version $versions)
	{
		$this->entries = $versions;
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
		return $this->entries->get();
	}
}
