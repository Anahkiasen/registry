<?php
namespace Registry\Repositories;

use Registry\Version;
use Registry\Abstracts\AbstractRepository;

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
}
